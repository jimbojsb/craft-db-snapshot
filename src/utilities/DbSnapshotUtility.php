<?php
/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshots your database using mysqldump and stores it on an asset volume of your choosing
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh Butts
 */

namespace jimbojsbcraftdbsnapshot\dbsnapshot\utilities;

use jimbojsbcraftdbsnapshot\dbsnapshot\DbSnapshot;
use jimbojsbcraftdbsnapshot\dbsnapshot\assetbundles\dbsnapshotutilityutility\DbSnapshotUtilityUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * DB Snapshot Utility
 *
 * Utility is the base class for classes representing Control Panel utilities.
 *
 * https://craftcms.com/docs/plugins/utilities
 *
 * @author    Josh Butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
class DbSnapshotUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * Returns the display name of this utility.
     *
     * @return string The display name of this utility.
     */
    public static function displayName(): string
    {
        return Craft::t('db-snapshot', 'DbSnapshotUtility');
    }

    /**
     * Returns the utility’s unique identifier.
     *
     * The ID should be in `kebab-case`, as it will be visible in the URL (`admin/utilities/the-handle`).
     *
     * @return string
     */
    public static function id(): string
    {
        return 'dbsnapshot-db-snapshot-utility';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@jimbojsbcraftdbsnapshot/dbsnapshot/assetbundles/dbsnapshotutilityutility/dist/img/DbSnapshotUtility-icon.svg");
    }

    /**
     * Returns the number that should be shown in the utility’s nav item badge.
     *
     * If `0` is returned, no badge will be shown
     *
     * @return int
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * Returns the utility's content HTML.
     *
     * @return string
     */
    public static function contentHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(DbSnapshotUtilityUtilityAsset::class);

        $someVar = 'Have a nice day!';
        return Craft::$app->getView()->renderTemplate(
            'db-snapshot/_components/utilities/DbSnapshotUtility_content',
            [
                'someVar' => $someVar
            ]
        );
    }
}
