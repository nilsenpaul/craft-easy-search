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

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the DefaultController actionIndex() method';

        return $result;
    }

    /**
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'Welcome to the DefaultController actionDoSomething() method';

        return $result;
    }
}
