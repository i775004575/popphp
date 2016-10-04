<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Router\Match;

/**
 * Pop router match interface
 *
 * @category   Pop
 * @package    Pop\Router
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface MatchInterface
{

    /**
     * Determine if the route has been matched
     *
     * @return boolean
     */
    public function hasRoute();

    /**
     * Get the matched route
     *
     * @return string
     */
    public function getRoute();

    /**
     * Get the matched controller class name
     *
     * @return string
     */
    public function getController();

    /**
     * Get the matched action name
     *
     * @return string
     */
    public function getAction();

    /**
     * Get the matched controller params
     *
     * @return array
     */
    public function getControllerParams();

    /**
     * Determine if there are matched controller params
     *
     * @return boolean
     */
    public function hasControllerParams();

    /**
     * Get the matched dispatch params
     *
     * @return array
     */
    public function getDispatchParams();

    /**
     * Determine if there are matched dispatch params
     *
     * @return boolean
     */
    public function hasDispatchParams();

    /**
     * Get the default controller class name
     *
     * @return mixed
     */
    public function getDefaultController();

    /**
     * Match the route to the controller class
     *
     * @param  array   $routes
     * @return boolean
     */
    public function match($routes);

    /**
     * Method to process if a route was not found
     *
     * @return void
     */
    public function noRouteFound();

}