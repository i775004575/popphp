<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp2
 * @category   Pop
 * @package    Pop
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Router\Match;

/**
 * Pop router match abstract class
 *
 * @category   Pop
 * @package    Pop
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
abstract class AbstractMatch
{

    /**
     * Controller class name
     * @var string
     */
    protected $controller = null;

    /**
     * Action name
     * @var string
     */
    protected $action = null;

    /**
     * Matched route parameters
     * @var array
     */
    protected $routeParams = [];

    /**
     * Matched dispatch parameters
     * @var array
     */
    protected $dispatchParams = [];

    /**
     * Default controller class name
     * @var array
     */
    protected $defaultController = null;

    /**
     * Prepared routes
     * @var array
     */
    protected $routes = [];

    /**
     * Get the matched controller class name
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get the matched action name
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the matched route params
     *
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Determine if there are matched route params
     *
     * @return boolean
     */
    public function hasRouteParams()
    {
        return (count($this->routeParams) > 0);
    }

    /**
     * Get the matched dispatch params
     *
     * @return array
     */
    public function getDispatchParams()
    {
        return $this->dispatchParams;
    }

    /**
     * Determine if there are matched dispatch params
     *
     * @return boolean
     */
    public function hasDispatchParams()
    {
        return (count($this->dispatchParams) > 0);
    }

    /**
     * Get the default controller class name
     *
     * @return string
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }

    /**
     * Constructor
     *
     * Instantiate the match object
     *
     * @return AbstractMatch
     */
    abstract public function __construct();

    /**
     * Match the route to the controller class
     *
     * @param  array   $routes
     * @return boolean
     */
    abstract public function match($routes);

    /**
     * Prepare the routes
     *
     * @param  array $routes
     * @return void
     */
    abstract protected function prepareRoutes($routes);

    /**
     * Get required parameters from the route
     *
     * @param  string $route
     * @return array
     */
    abstract protected function getRequiredParams($route);

    /**
     * Get optional parameters from the route
     *
     * @param  string $route
     * @return array
     */
    abstract protected function getOptionalParams($route);

    /**
     * Get parameters from the route string
     *
     * @param  string $route
     * @return array
     */
    abstract protected function getDispatchParamsFromRoute($route);

    /**
     * Process parameters from the route string
     *
     * @param  array $params
     * @param  array $routeParams
     * @return mixed
     */
    abstract protected function processDispatchParamsFromRoute($params, $routeParams);

}