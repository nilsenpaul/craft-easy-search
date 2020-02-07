<?php

namespace nilsenpaul\easysearch\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EasySearchBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@nilsenpaul/easysearch/resources';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'easy-search.js',
        ];

        $this->css = [
            'easy-search.css',
        ];

        parent::init();
    }
}