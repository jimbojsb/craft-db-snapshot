<?php
/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot your database with mysqldump and store it to a an asset volume (S3). Also restore it from that same location. Great for local dev snapshots and nightly backups.
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh butts
 */

/**
 * DB Snapshot en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('db-snapshot', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Josh butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
return [
    'DB Snapshot plugin loaded' => 'DB Snapshot plugin loaded',
];
