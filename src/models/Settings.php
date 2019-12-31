<?php
/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot your database with mysqldump and store it to a an asset volume (S3). Also restore it from that same location. Great for local dev snapshots and nightly backups.
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh butts
 */

namespace jimbojsb\dbsnapshot\models;

use jimbojsb\dbsnapshot\DbSnapshot;

use Craft;
use craft\base\Model;

/**
 * DbSnapshot Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Josh butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
class Settings extends Model
{
    public $path = 'db_snapshots';
    public $filename;
    public $compress = true;
    public $accessKey;
    public $secretKey;
    public $endpoint;
    public $region = 'us-east-1';
    public $bucket;


    public function __construct($config = [])
    {
        parent::__construct($config);
        if (!$this->filename) {
            $this->filename = Craft::$app->config->db->database . ".sql";
        }
    }




    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }
}
