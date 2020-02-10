<?php
/**
 * Easy Search plugin for Craft CMS 3.x
 *
 * tbd
 *
 * @link      https://nilsenpaul.nl
 * @copyright Copyright (c) 2020 nils&paul
 */

namespace nilsenpaul\easysearch\controllers;

use nilsenpaul\easysearch\EasySearch;

use Craft;
use craft\web\Controller;

/**
 * @author    nils&paul
 * @package   EasySearch
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionGetAvailableFields($elementType, $source)
    {
        $element = new $elementType;
        
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
        }

        return $this->asJson(array_merge([
            [
                'handle' => "--any--",
                'label' => Craft::t('easy-search', 'Any field'),
            ],
        ], $fieldsToReturn));
    }

    protected function getEntryFields($element, $source)
    {
        $sectionsAndEntryTypes = [];

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

                if ($element::hasContent() && ($fieldLayout = $element->getFieldLayout()) !== null) {
                    foreach ($fieldLayout->getFields() as $field) {
                        if ($field->searchable) {
                            $fieldsToReturn[] = [
                                'handle' => $field->handle,
                                'label' => $field->name,
                            ];
                        }
                    }
                }
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
        if ($element::hasContent() && ($fieldLayout = $element->getFieldLayout()) !== null) {
            foreach ($fieldLayout->getFields() as $field) {
                if ($field->searchable) {
                    $fieldsToReturn[] = [
                        'handle' => $field->handle,
                        'label' => $field->name,
                    ];
                }
            }
        }

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
        if ($element::hasContent() && ($fieldLayout = $element->getFieldLayout()) !== null) {
            foreach ($fieldLayout->getFields() as $field) {
                if ($field->searchable) {
                    $fieldsToReturn[] = [
                        'handle' => $field->handle,
                        'label' => $field->name,
                    ];
                }
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

    protected function getAssetFields($element, $source)
    {
        $folderUid = str_replace('folder:', '', $source);        
        $element->volumeId = Craft::$app->getAssets()->getFolderByUid($folderUid)->volumeId;

        $fieldsToReturn = [];
        if ($element::hasContent() && ($fieldLayout = $element->getFieldLayout()) !== null) {
            foreach ($fieldLayout->getFields() as $field) {
                if ($field->searchable) {
                    $fieldsToReturn[] = [
                        'handle' => $field->handle,
                        'label' => $field->name,
                    ];
                }
            }
        }

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
}
