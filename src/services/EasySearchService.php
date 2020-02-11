<?php

namespace nilsenpaul\easysearch\services;

use Craft;
use craft\base\Component;

class EasySearchService extends Component
{
    public function getAvailableFieldsForElementType(String $elementType, String $source)
    {
        $element = new $elementType;

        if ($element) {
            $fieldsToReturn = [];
            switch ($elementType) {
                case 'craft\\elements\\Entry':
                    $fieldsToReturn += $this->getEntryFields($element, $source);
                    break;
                case 'craft\\elements\\Category':
                    $fieldsToReturn = $this->getCategoryFields($element, $source);
                    break;
                case 'craft\\elements\\Asset':
                    $fieldsToReturn = $this->getAssetFields($element, $source);
                    break;
                case 'craft\\elements\\User':
                    $fieldsToReturn = $this->getUserFields($element, $source);
                    break;
                case 'craft\\commerce\\elements\\Product':
                    $fieldsToReturn = $this->getProductFields($element, $source);
                    break;
            }

            return array_merge([
                [
                    'handle' => "--any--",
                    'label' => Craft::t('easy-search', 'Any field'),
                ],
            ], $fieldsToReturn);
        }

        return [];
    }

    protected function getEntryFields($element, $source)
    {
        $sectionsAndEntryTypes = [];
        $fieldsToReturn = [];

        // Do we need all sections, or just one?
        if ($source === '*') {
            foreach (Craft::$app->getSections()->getAllSections() as $section) {
                foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
                    $sectionsAndEntryTypes[$section->id][] = $entryType->id;
                }
            }
        } else {
            $sectionUid = str_replace('section:', '', $source);
            $section = Craft::$app->getSections()->getSectionByUid($sectionUid);
            foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
                $sectionsAndEntryTypes[$section->id][] = $entryType->id;
            }
        }

        // Get all searchable fields for the sections and entry types we found
        foreach ($sectionsAndEntryTypes as $sectionId => $entryTypes) {
            foreach ($entryTypes as $entryTypeId) {
                $element->sectionId = $sectionId;
                $element->typeId = $entryTypeId;

                $this->getFieldsForElement($element, $fieldsToReturn);
            }
        }

        return array_merge([
            [
                'handle' => 'title',
                'label' => Craft::t('app', 'Title'),
            ],
            [
                'handle' => 'slug',
                'label' => Craft::t('app', 'Slug'),
            ],
        ], $fieldsToReturn);
    }

    protected function getUserFields($element, $source)
    {
        $fieldsToReturn = [];
        $this->getFieldsForElement($element, $fieldsToReturn);

        return array_merge([
            [
                'handle' => 'username',
                'label' => Craft::t('app', 'Username'),
            ],
            [
                'handle' => 'firstName',
                'label' => Craft::t('app', 'First Name'),
            ],
            [
                'handle' => 'lastName',
                'label' => Craft::t('app', 'Last Name'),
            ],
            [
                'handle' => 'fullName',
                'label' => Craft::t('app', 'Full Name'),
            ],
            [
                'handle' => 'email',
                'label' => Craft::t('app', 'Email'),
            ],
        ], $fieldsToReturn);
    }

    protected function getCategoryFields($element, $source)
    {
        $groupUid = str_replace('group:', '', $source);
        $element->groupId = Craft::$app->getCategories()->getGroupByUid($groupUid)->id;

        $fieldsToReturn = [];
        $this->getFieldsForElement($element, $fieldsToReturn);

        return array_merge([
            [
                'handle' => 'title',
                'label' => Craft::t('app', 'Title'),
            ],
            [
                'handle' => 'slug',
                'label' => Craft::t('app', 'Slug'),
            ],
        ], $fieldsToReturn);
    }

    protected function getAssetFields($element, $source)
    {
        $folderUid = str_replace('folder:', '', $source);
        $element->volumeId = Craft::$app->getAssets()->getFolderByUid($folderUid)->volumeId;

        $fieldsToReturn = [];
        $this->getFieldsForElement($element, $fieldsToReturn);

        return array_merge([
            [
                'handle' => 'title',
                'label' => Craft::t('app', 'Title'),
            ],
            [
                'handle' => 'filename',
                'label' => Craft::t('app', 'Filename'),
            ],
            [
                'handle' => 'extension',
                'label' => Craft::t('easy-search', 'Extension'),
            ],
            [
                'handle' => 'kind',
                'label' => Craft::t('easy-search', 'Kind'),
            ],
        ], $fieldsToReturn);
    }

    protected function getFieldsForElement($element, &$fieldsToReturn)
    {
        if ($element::hasContent() && ($fieldLayout = $element->getFieldLayout()) !== null) {
            foreach ($fieldLayout->getFields() as $field) {
                $fieldType = strtolower(str_replace('craft\\fields\\', '', $field::className()));

                if ($field->searchable) {
                    $fieldsToReturn[] = [
                        'handle' => $field->handle,
                        'label' => $field->name,
                    ];
                }
            }
        }
    }

    protected function getProductFields($element, $source)
    {
        $productTypeIds = [];
        $fieldsToReturn = [];

        $commerce = Craft::$app->getPlugins()->getPlugin('commerce');

        // Do we need all sections, or just one?
        if ($source === '*') {
            foreach ($commerce->getProductTypes()->getAllProductTypes() as $productType) {
                $productTypeIds[] = $productType->id;
            }
        } else {
            $productTypeUid = str_replace('productType:', '', $source);
            $productType = $commerce->getProductTypes()->getProductTypeByUid($productTypeUid);
            $productTypeIds[] = $productType->id;
        }

        // Get all searchable fields for the sections and entry types we found
        foreach ($productTypeIds as $productTypeId) {
            $element->typeId = $productTypeId;

            $this->getFieldsForElement($element, $fieldsToReturn);
        }

        return array_merge([
            [
                'handle' => 'title',
                'label' => Craft::t('app', 'Title'),
            ],
            [
                'handle' => 'slug',
                'label' => Craft::t('app', 'Slug'),
            ],
        ], $fieldsToReturn);
    }
}
