<?php
/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshots your database using mysqldump and stores it on an asset volume of your choosing
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh Butts
 */

namespace jimbojsbcraftdbsnapshot\dbsnapshot\services;

use jimbojsbcraftdbsnapshot\dbsnapshot\DbSnapshot;

use Craft;
use craft\base\Component;

/**
 * Snapshotcreate Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Josh Butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
class Snapshotcreate extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     DbSnapshot::$plugin->snapshotcreate->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (DbSnapshot::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
