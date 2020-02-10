<?php
/**
 * Easy Search plugin for Craft CMS 3.x
 *
 * tbd
 *
 * @link      https://nilsenpaul.nl
 * @copyright Copyright (c) 2020 nils&paul
 */

namespace nilsenpaul\easysearch;

use nilsenpaul\easysearch\services\EasySearchService as EasySearchServiceService;
use nilsenpaul\easysearch\assetbundles\EasySearchBundle;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use craft\helpers\UrlHelper;

/**
 * Class EasySearch
 *
 * @author    nils&paul
 * @package   EasySearch
 * @since     1.0.0
 *
 * @property  EasySearchServiceService $easySearchService
 */
class EasySearch extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var EasySearch
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app->getRequest()->isCpRequest) {
            Craft::$app->getView()->registerAssetBundle(EasySearchBundle::class);
        }

        Craft::info(
            Craft::t(
                'easy-search',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        $this->registerTranslations();
    }

    protected function registerTranslations()
    {
        Craft::$app->getView()->registerTranslations('easy-search', [
            'Build a search query',
            'Any field',
            'contains',
            'does not contain',
            'is equal to',
            'is not equal to',
            'is empty',
            'is not empty',
        ]);
    }
}
