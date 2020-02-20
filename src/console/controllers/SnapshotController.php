<?php

/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot your database with mysqldump and store it to a an asset volume (S3). Also restore it from that same location. Great for local dev snapshots and nightly backups.
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh butts
 */

namespace jimbojsb\dbsnapshot\console\controllers;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Handler\GuzzleV6\GuzzleHandler;
use Aws\Sts\StsClient;
use craft\base\VolumeInterface;
use craft\helpers\FileHelper;
use jimbojsb\dbsnapshot\DbSnapshot;

use Craft;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * DB Snapshot Commands
 *
 * @author    Josh butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
class SnapshotController extends Controller
{
    // Public Properties
    // =========================================================================

    /**
     * @var null|string
     */
    public $filename;

    /**
     * Cache key to use for caching purposes
     */
    const CACHE_KEY_PREFIX = 'aws.';

    /**
     * Cache duration for access token
     */
    const CACHE_DURATION_SECONDS = 3600;


    // Public Methods
    // =========================================================================

    /**
     * @param string $actionID
     *
     * @return array|string[]
     */
    public function options($actionID): array
    {
        return [
            'filename',
        ];
    }

    /**
     * create a new snapshot
     */
    public function actionCreate()
    {
        $this->stdout('Creating snapshot ... ');
        $db = Craft::$app->getDb();
        $view = Craft::$app->getView();
        $settings = DbSnapshot::getInstance()->settings;

        $tmpPath = $this->createTempFolder();

        $filename = $view->renderString($settings->getFilename());
        $tmpFile = "$tmpPath/$filename";
        $volumeFile = $filename;

        try {
            $db->backupTo($tmpFile);
            if ($settings->getCompress()) {
                $compressCmd = "/bin/gzip $tmpFile";
                shell_exec($compressCmd);
                $tmpFile = "$tmpFile.gz";
                $volumeFile = "$volumeFile.gz";
            }
            $filesystem = $this->getFilesystem();
            $filesystem->putStream($volumeFile, @fopen($tmpFile, 'r'), []);
        } catch (\Throwable $e) {
            Craft::$app->getErrorHandler()->logException($e);
            $this->stderr('error: ' . $e->getMessage() . PHP_EOL, \craft\helpers\Console::FG_RED);
            unlink($tmpFile);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        unlink($tmpFile);
        $this->stdout("done\n");
        return ExitCode::OK;
    }

    /**
     * load an existing snapshot. You can pass in a --filename
     */
    public function actionLoad()
    {
        $this->stdout('Loading snapshot ... ');
        $db = Craft::$app->getDb();
        $view = Craft::$app->getView();
        $settings = DbSnapshot::getInstance()->settings;

        $tmpPath = $this->createTempFolder();

        if (!$filename = $this->filename) {
            $filename = $view->renderString($settings->getFilename());
        }

        $tmpFile = "$tmpPath/$filename";
        $volumeFile = $filename;

        try {
            $filesystem = $this->getFilesystem();

            if ($settings->getCompress()) {
                $volumeFile = "$volumeFile.gz";
                $tmpFile = "$tmpFile.gz";
            }

            if ($filesystem->has($volumeFile)) {
                file_put_contents($tmpFile, $filesystem->readStream($volumeFile));
            } else {
                throw new \RuntimeException("Snapshot $volumeFile does not exist");
            }

            if ($settings->getCompress()) {
                $compressCmd = "/bin/gunzip $tmpFile";
                shell_exec($compressCmd);
                $tmpFile = str_replace(".gz", "", $tmpFile);
            }
            $db->restore($tmpFile);
        } catch (\Throwable $e) {
            Craft::$app->getErrorHandler()->logException($e);
            $this->stderr('error: ' . $e->getMessage() . PHP_EOL, \craft\helpers\Console::FG_RED);
            unlink($tmpFile);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        unlink($tmpFile);
        $this->stdout("done\n");
        return ExitCode::OK;
    }

    private function createTempFolder()
    {
        $tmpPath = Craft::$app->path->getStoragePath() . "/db_snapshots";
        if (!file_exists($tmpPath)) {
            mkdir($tmpPath, 0777, true);
        }
        return $tmpPath;
    }

    /**
     * Build the config array based on a keyID and secret
     *
     * @param string|null $keyId The key ID
     * @param string|null $secret The key secret
     * @param string|null $region The region to user
     * @param bool $refreshToken If true will always refresh token
     * @return array
     */
    public static function buildConfigArray($keyId = null, $secret = null, $region = null, $refreshToken = false): array
    {
        $config = [
            'region' => $region,
            'version' => 'latest'
        ];

        $client = Craft::createGuzzleClient();
        $config['http_handler'] = new GuzzleHandler($client);

        if (empty($keyId) || empty($secret)) {
            // Assume we're running on EC2 and we have an IAM role assigned. Kick back and relax.
        } else {
            $tokenKey = static::CACHE_KEY_PREFIX . md5($keyId . $secret);
            $credentials = new Credentials($keyId, $secret);

            if (Craft::$app->cache->exists($tokenKey) && !$refreshToken) {
                $cached = Craft::$app->cache->get($tokenKey);
                $credentials->unserialize($cached);
            } else {
                $config['credentials'] = $credentials;
                $stsClient = new StsClient($config);
                $result = $stsClient->getSessionToken(['DurationSeconds' => static::CACHE_DURATION_SECONDS]);
                $credentials = $stsClient->createCredentials($result);
                $cacheDuration = $credentials->getExpiration() - time();
                $cacheDuration = $cacheDuration > 0 ?: static::CACHE_DURATION_SECONDS;
                Craft::$app->cache->set($tokenKey, $credentials->serialize(), $cacheDuration);
            }

            // TODO Add support for different credential supply methods
            $config['credentials'] = $credentials;
        }

        return $config;
    }

    /**
     * Get the Amazon S3 client.
     *
     * @param array $config client config
     * @param array $credentials credentials to use when generating a new token
     * @return S3Client
     */
    protected static function client(array $config = [], array $credentials = []): S3Client
    {
        if (!empty($config['credentials']) && $config['credentials'] instanceof Credentials) {
            $config['generateNewConfig'] = function () use ($credentials) {
                $args = [
                    $credentials['keyId'],
                    $credentials['secret'],
                    $credentials['region'],
                    true
                ];
                return call_user_func_array(self::class . '::buildConfigArray', $args);
            };
        }

        return new S3Client($config);
    }

    /**
     * Return the credentials as an array
     *
     * @return array
     */
    private function _getCredentials()
    {
        $settings = DbSnapshot::getInstance()->settings;
        return [
            'keyId' => $settings->getAccessKey(),
            'secret' => $settings->getSecretKey(),
            'region' => $settings->getRegion(),
        ];
    }

    /**
     * Get the config array for AWS Clients.
     *
     * @return array
     */
    private function _getConfigArray()
    {
        $credentials = $this->_getCredentials();

        return self::buildConfigArray($credentials['keyId'], $credentials['secret'], $credentials['region']);
    }

    /**
     * @param \jimbojsb\dbsnapshot\models\Settings $settings
     * @return Filesystem
     */
    private function getFilesystem()
    {
        $settings = DbSnapshot::getInstance()->settings;

        $config = $this->_getConfigArray();

        if ($settings->getEndpoint()) {
            $config['endpoint'] = $settings->getEndpoint();
        }

        $s3Client = static::client($config, $this->_getCredentials());
        $s3Adapter = new AwsS3Adapter($s3Client, $settings->getBucket(), $settings->getPath());
        $filesystem = new Filesystem($s3Adapter);
        return $filesystem;
    }
}
