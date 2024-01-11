<?php

namespace nilsenpaul\easysearch\events;

use craft\base\ElementInterface;
use yii\base\Event;

class DefineAvailableFieldsEvent extends Event
{
    /**
     * @var array An array of associative arrays describing the searchable fields.
     * Each searchable field must have the following keys: `handle` & `label`.
     */
    public $fields;

    /**
     * @var ElementInterface An (empty) instance of the element type listed in the CP element index page.
     */
    public $element;
}
