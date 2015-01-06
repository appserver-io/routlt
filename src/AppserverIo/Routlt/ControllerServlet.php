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
 *
 * @Route(name="controller",
 *        displayName="Servlet providing controller functionality",
 *        description="A annotated conntroller servlet implementation.",
 *        urlPattern={"/", "/*"},
 *        initParams={{"configurationFile", "WEB-INF/routes.json"},
 *                    {"action.base.path", "/WEB-INF/classes/"},
 *                    {"routlt.configuration.file", "WEB-INF/routlt.properties"}})
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
     * @deprecated Since version 0.3.0
     */
    const INIT_PARAMETER_CONFIGURATION_FILE = 'configurationFile';

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
     * The array with the path descriptors.
     *
     * @var array
     */
    protected $paths = array();

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
        $this->initDirectory();
        $this->initMappings();
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
        $configurationFile = new \SplFileInfo(
            $this->getServletConfig()->getWebappPath() . DIRECTORY_SEPARATOR . $configurationFileName
        );

        // if the file is readable
        if ($configurationFile->isFile() && $configurationFile->isReadable()) {

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
     * Initializes the mappings by parsing a directory that has
     * action classes with annotations.
     *
     * @return void
     */
    protected function initDirectory()
    {

        // load the absolute path to the applications base directory
        $webappPath = $this->getServletConfig()->getWebappPath();

        // load the relative path to the applications actions
        if ($actionBasePath = $this->getServletConfig()->getInitParameter(ControllerServlet::INIT_PARAMETER_ACTION_BASE_PATH)) {

            // concatenate the action path to an absolute path
            $actionPath = $webappPath . $actionBasePath;

            // parse the directory for actions
            $directoryParser = new DirectoryParser();
            $directoryParser->injectController($this);
            $directoryParser->parse($actionPath);

            // initialize the mappings
            foreach ($this->getPathDescriptors() as $pathDescriptor) {
                $this->mappings[$pathDescriptor->getName()] = $pathDescriptor->getClassName();
            }
        }
    }

    /**
     * Initializes the mappings to create the routes by reading the configuration file.
     *
     * Values found in the configuration file will overwrite the ones found in the
     * annotations parsed by initDirectory() method.
     *
     * @return void
     * @see \AppserverIo\Routlt\ControllerServlet::initDirectory()
     */
    protected function initMappings()
    {

        // load the configuration filename
        $configurationFileName = $this->getInitParameter(ControllerServlet::INIT_PARAMETER_CONFIGURATION_FILE);

        // load the path to the configuration file
        $configurationFile = new \SplFileInfo(
            $this->getServletConfig()->getWebappPath() . DIRECTORY_SEPARATOR . $configurationFileName
        );

        // if the file is readable
        if ($configurationFile->isFile() && $configurationFile->isReadable()) {

            // initialize the variable for the file content
            $content = '';

            // read the content of the configuration file
            $fileHandle = $configurationFile->openFile();
            while (!$fileHandle->eof()) {
                $content .= $fileHandle->fgets();
            }

            // explode the mappings from the content we found
            $stdClass = json_decode($content);

            // add the mappings found in the configuration file
            foreach ($stdClass->routes as $route) {
                $this->mappings[$route->urlMapping] = $route->actionClass;
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

        // iterate over the action mappings and initialize the action instances
        foreach ($this->getMappings() as $urlMapping => $actionClass) {

            // initialize a new action instance
            $action = new $actionClass(new ArrayContext());

            // if the action is servlet context aware
            if ($action instanceof ServletContextAware) {
                $action->setServletContext($this->getServletContext());
            }

            // add the initialized action
            $this->routes[$urlMapping] = $action;
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
     * @param \AppserverIo\Psr\Servlet\ServletRequest  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponse $servletResponse The response sent back to the client
     *
     * @return void
     */
    public function service(ServletRequest $servletRequest, ServletResponse $servletResponse)
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
