<?php

/**
 * AppserverIo\Routlt\ControllerServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt;

use TechDivision\Context\BaseContext;
use TechDivision\Servlet\ServletConfig;
use TechDivision\Servlet\ServletRequest;
use TechDivision\Servlet\ServletResponse;
use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\Server\Exceptions\ModuleException;

/**
 * Abstract example implementation that provides some kind of basic MVC functionality
 * to handle requests by subclasses action methods.
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ControllerServlet extends HttpServlet
{

    /**
     * The key for the init parameter with the path to the configuration file.
     *
     * @var string
     */
    const INIT_PARAMETER_CONFIGURATION_FILE = 'configurationFile';

    /**
     * The default action if no valid action name was found in the path info.
     *
     * @var string
     */
    const DEFAULT_ROUTE = '/index';

    /**
     * The array with the available route mappings.
     *
     * @var array
     */
    protected $mappings = array();

    /**
     * The array with the initialized routes.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \TechDivision\Servlet\ServletConfig $config The configuration to initialize the servlet with
     *
     * @return void
     */
    public function init(ServletConfig $config)
    {

        // call parent method
        parent::init($config);

        // loads the mappings from the configuration file and initialize the routes
        $this->initMappings();
        $this->initRoutes();
    }

    /**
     * Initializes the mappings to create the routes from.
     *
     * @return void
     */
    protected function initMappings()
    {

        // load the configuration filename
        $configurationFileName = $this->getInitParameter(ControllerServlet::INIT_PARAMETER_CONFIGURATION_FILE);

        // load the path to the configuration file
        $configurationFile = new \SplFileObject(
            $this->getServletConfig()->getWebappPath() . DIRECTORY_SEPARATOR . $configurationFileName
        );

        // read the content of the configuration file
        $content = '';
        while (!$configurationFile->eof()) {
            $content .= $configurationFile->fgets();
        }

        // explode the mappings from the content we found
        $stdClass = json_decode($content);

        foreach ($stdClass->routes as $route) {
            $this->mappings[$route->urlMapping] = $route->actionClass;
        }
    }

    /**
     * Initializes the available routes.
     *
     * @return void
     */
    protected function initRoutes()
    {
        foreach ($this->getMappings() as $urlMapping => $actionClass) {
            $this->routes[$urlMapping] = new $actionClass(new BaseContext());
        }
    }

    /**
     * Returns the mappings to create the routes from.
     *
     * @return array The array with the mappings
     */
    protected function getMappings()
    {
        return $this->mappings;
    }

    /**
     * Returns the available routes.
     *
     * @return array The array with the available routes
     */
    protected function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Delegates to HTTP method specific functions like doPost() for POST e.g.
     *
     * @param \TechDivision\Servlet\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\ServletResponse $servletResponse The response sent back to the client
     *
     * @return void
     */
    public function service(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        $this->serviceHttpServletRequest($servletRequest, $servletResponse);
    }

    /**
     * Handles the HTTP servlet request.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response sent back to the client
     *
     * @return void
     */
    protected function serviceHttpServletRequest(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the path info from the servlet request
        $pathInfo = $servletRequest->getPathInfo();

        // if the requested action has been found in the path info
        if ($pathInfo == null) {
            $pathInfo = $this->getDefaultRoute();
        }

        // load the routes
        $routes = $this->getRoutes();

        // try to find an action that invokes the request
        foreach ($routes as $route => $action) {

            // if the route match, we'll perform the dispatch process
            if (fnmatch($route, $pathInfo)) {

                // we pre-dispatch the action
                $action->preDispatch($servletRequest, $servletResponse);

                // if the action has been dispatched, we're done
                if ($servletRequest->isDispatched()) {
                    return;
                }

                // if not dispatch the action
                $action->perform($servletRequest, $servletResponse);
                $action->postDispatch($servletRequest, $servletResponse);
                return;
            }
        }

        // we can't find an action that handles this request
        throw new ModuleException(sprintf("No action to handle path info '%s' available.", $pathInfo), 404);
    }

    /**
     * This method returns the default route we'll invoke if the path info doesn't contain one.
     *
     * @return string The default route
     */
    protected function getDefaultRoute()
    {
        return ControllerServlet::DEFAULT_ROUTE;
    }
}
