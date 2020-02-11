<?php

namespace nilsenpaul\easysearch\controllers;

use nilsenpaul\easysearch\EasySearch;

use craft\web\Controller;

class FieldsController extends Controller
{
    public function actionGetAvailableFields($elementType, $source)
    {
        return $this->asJson(EasySearch::$instance->easySearchService->getAvailableFieldsForElementType($elementType, $source));
    }
}
