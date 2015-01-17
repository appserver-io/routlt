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

use AppserverIo\Server\Exceptions\ModuleException;
use AppserverIo\Psr\Context\ArrayContext;
use AppserverIo\Psr\Servlet\ServletConfig;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Routlt\Description\DirectoryParser;
use AppserverIo\Routlt\Description\PathDescriptorInterface;
use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Properties\Properties;

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
class ControllerServlet extends HttpServlet implements Controller
{

    /**
     * The key for the init parameter with the path to the configuration file.
     *
     * @var string
     */
    const INIT_PARAMETER_ACTION_BASE_PATH = 'action.base.path';

    /**
     * The key for the init parameter with the path to the configuration file.
     *
     * @var string
     */
    const INIT_PARAMETER_ROUTLT_CONFIGURATION_FILE = 'routlt.configuration.file';

    /**
     * The default action if no valid action name was found in the path info.
     *
     * @var string
     */
    const DEFAULT_ROUTE = '/index';

    /**
     * The array with the initialized routes.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \AppserverIo\Psr\Servlet\ServletConfig $config The configuration to initialize the servlet with
     *
     * @return void
     */
    public function init(ServletConfig $config)
    {

        // call parent method
        parent::init($config);

        // load the values from the configuration file
        $this->initConfiguration();

        // initialize the routing
        $this->initRoutes();
    }

    /**
     * Loads the values found in the configuration file and merges
     * them with the servlet context initialization parameters.
     *
     * @return void
     */
    protected function initConfiguration()
    {

        // load the relative path to the Routlt configuration file
        $configurationFileName = $this->getServletConfig()->getInitParameter(ControllerServlet::INIT_PARAMETER_ROUTLT_CONFIGURATION_FILE);

        // load the path to the configuration file
        $configurationFile = $this->getServletConfig()->getWebappPath() . DIRECTORY_SEPARATOR . ltrim($configurationFileName, '/');

        // if the file is readable
        if (is_file($configurationFile) && is_readable($configurationFile)) {

            // load the  properties from the file
            $properties = new Properties();
            $properties->load($configurationFile);

            // append the properties to the servlet context
            foreach ($properties as $paramName => $paramValue) {
                $this->getServletContext()->addInitParameter($paramName, $paramValue);
            }
        }
    }

    /**
     * Initializes the available routes.
     *
     * @return void
     */
    protected function initRoutes()
    {

        // load the object manager instance
        $objectManager = $this->getServletContext()->getApplication()->search('ObjectManagerInterface');

        // register the beans located by annotations and the XML configuration
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {

            // check if we've found a servlet descriptor
            if ($descriptor instanceof PathDescriptorInterface) {

                // load the class name
                $className = $descriptor->getClassName();

                // initialize a new action instance
                $action = new $className(new ArrayContext());

                // if the action is servlet context aware
                if ($action instanceof ServletContextAware) {
                    $action->setServletContext($this->getServletContext());
                }

                // register the EPB references
                foreach ($descriptor->getEpbReferences() as $epbReference) {
                    $this->getServletContext()->registerEpbReference($epbReference);
                }

                // register the resource references
                foreach ($descriptor->getResReferences() as $resReference) {
                    $this->getServletContext()->registerResReference($resReference);
                }

                // add the initialized action
                $this->routes[$descriptor->getName()] = $action;
            }
        }
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
     * @param \AppserverIo\Psr\Servlet\ServletRequest  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponse $servletResponse The response sent back to the client
     *
     * @return void
     */
    public function service(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {

        // load the application instance
        $application = $this->getServletContext()->getApplication();

        // inject the dependencies
        $dependencyInjectionContainer = $application->search('ProviderInterface');

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

                // check if we've a HTTP session-ID
                $sessionId = null;

                // if no session has already been load, initialize the session manager
                if ($manager = $application->search('SessionManager')) {
                    $requestedSessionName = $manager->getSessionSettings()->getSessionName();
                    if ($servletRequest->hasCookie($requestedSessionName)) {
                        $sessionId = $servletRequest->getCookie($requestedSessionName)->getValue();
                    }
                }

                // inject the dependencies
                $dependencyInjectionContainer->injectDependencies($action, $sessionId);

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

    /**
     * Returns the array with the path descriptors.
     *
     * @return array The array with the path descriptors
     */
    protected function getPathDescriptors()
    {
        return $this->paths;
    }

    /**
     * Adds a path descriptor to the controller.
     *
     * @param \AppserverIo\Routlt\Description\PathDescriptorInterface $pathDescriptor The path descriptor to add
     *
     * @return void
     */
    public function addPathDescriptor(PathDescriptorInterface $pathDescriptor)
    {
        $this->paths[] = $pathDescriptor;
    }
}
