<?php

namespace nilsenpaul\easysearch;

use nilsenpaul\easysearch\assetbundles\EasySearchBundle;

use Craft;
use craft\base\Plugin;
use craft\helpers\Json;

use yii\web\View;

class EasySearch extends Plugin
{
    public static $instance;
    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();
        self::$instance = $this;

        if (Craft::$app->getRequest()->isCpRequest) {
            Craft::$app->getView()->registerAssetBundle(EasySearchBundle::class);

            $this->registerPredefinedQueries();
            $this->registerTranslations();
        }
    }

    protected function createSettingsModel()
    {
        return new \nilsenpaul\easysearch\models\Settings();
    }

    protected function registerPredefinedQueries()
    {
        Craft::$app->getView()->registerJs("
            window.predefinedQueries = " . Json::encode($this->getSettings()->queries) . ";
        ", View::POS_HEAD);
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
            'Add a condition',
            'and',
            'or',
            'Use a predefined query',
            '... or build one',
            'Select a search query',
        ]);
    }
}
