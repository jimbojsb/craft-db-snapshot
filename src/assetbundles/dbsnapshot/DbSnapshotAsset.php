<?php
/**
 * DB Snapshot plugin for Craft CMS 3.x
 *
 * Snapshots your database using mysqldump and stores it on an asset volume of your choosing
 *
 * @link      https://github.com/jimbojsb
 * @copyright Copyright (c) 2019 Josh Butts
 */

namespace jimbojsbcraftdbsnapshot\dbsnapshot\assetbundles\DbSnapshot;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * DbSnapshotAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    Josh Butts
 * @package   DbSnapshot
 * @since     1.0.0
 */
class DbSnapshotAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@jimbojsbcraftdbsnapshot/dbsnapshot/assetbundles/dbsnapshot/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/DbSnapshot.js',
        ];

        $this->css = [
            'css/DbSnapshot.css',
        ];

        parent::init();
    }
}
