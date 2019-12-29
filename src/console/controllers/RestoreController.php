<?php
/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshots your database using mysqldump and stores it on an asset volume of your choosing
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh Butts
 */

namespace jimbojsbcraftdbsnapshot\dbsnapshot\console\controllers;

use jimbojsbcraftdbsnapshot\dbsnapshot\DbSnapshot;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Restore Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is plugin-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft db-snapshot/restore
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft db-snapshot/restore/do-something
 *
 * @author    Josh Butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
class RestoreController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle db-snapshot/restore console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'something';

        echo "Welcome to the console RestoreController actionIndex() method\n";

        return $result;
    }

    /**
     * Handle db-snapshot/restore/do-something console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'something';

        echo "Welcome to the console RestoreController actionDoSomething() method\n";

        return $result;
    }
}
