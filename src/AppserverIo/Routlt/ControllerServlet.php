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
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Properties\Properties;
use AppserverIo\Psr\Context\ArrayContext;
use AppserverIo\Psr\Context\ContextInterface;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Routlt\Util\ContextKeys;
use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Routlt\Description\PathDescriptorInterface;

/**
 * Abstract example implementation that provides some kind of basic MVC functionality
 * to handle requests by subclasses action methods.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ControllerServlet extends HttpServlet implements ControllerInterface
{

    /**
     * The key for the init parameter with the action namespace.
     *
     * @var string
     */
    const INIT_PARAMETER_ACTION_NAMESPACE = 'action.namespace';

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
     * The array with request method action -> route mappings.
     *
     * @var array
     */
    protected $actionMappings = array();

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $config The configuration to initialize the servlet with
     *
     * @return void
     */
    public function init(ServletConfigInterface $config)
    {

        // call parent method
        parent::init($config);

        // load the values from the configuration file
        $this->initConfiguration();

        // initialize the routing
        $this->initRoutes();
    }

    /**
     * Returns the session manager instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface $servletRequest The request instance
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionManagerInterface The session manager instance
     */
    public function getSessionManager(ServletRequestInterface $servletRequest)
    {
        return $servletRequest->getContext()->search('SessionManagerInterface');
    }

    /**
     * Returns the provide instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface $servletRequest The request instance
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionManagerInterface The provider instance
     */
    public function getProvider(ServletRequestInterface $servletRequest)
    {
        return $servletRequest->getContext()->search('ProviderInterface');
    }

    /**
     * Returns the naming directoy instance (the application).
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The naming directory instance
     */
    public function getNamingDirectory()
    {
        return $this->getServletContext()->getApplication();
    }

    /**
     * Returns the object manager instance
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ObjectManagerInterface The object manager instance
     */
    public function getObjectManager()
    {
        return $this->getNamingDirectory()->search('ObjectManagerInterface');
    }

    /**
     * This method returns the default route we'll invoke if the path info doesn't contain one.
     *
     * @return string The default route
     */
    public function getDefaultRoute()
    {
        return ControllerServlet::DEFAULT_ROUTE;
    }

    /**
     * Returns the array with the path descriptors.
     *
     * @return array The array with the path descriptors
     */
    public function getPathDescriptors()
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

    /**
     * Returns the requested session name. This is the session name, that
     * is SESSID by default or has been specified in the web.xml.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface $servletRequest The request instance
     *
     * @return string|null The requested session name, or NULL if no session manager has been registered
     */
    public function getSessionName(ServletRequestInterface $servletRequest)
    {

        // try load the session manager
        $sessionManager = $this->getSessionManager($servletRequest);

        // if a session manager is available, return the requested session name
        if ($sessionManager != null) {
            return $sessionManager->getSessionSettings()->getSessionName();
        }
    }

    /**
     * Returns the ID of the session with the requested session name.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface $servletRequest The request instance
     *
     * @return string|null The ID of the requested session or NULL if no session name is available
     * @see AppserverIo\Routlt\ControllerServlet::getSessionName()
     */
    public function getSessionId(ServletRequestInterface $servletRequest)
    {

        // initialize the HTTP session-ID
        $sessionId = null;

        // query whether we can find a HTTP session-ID in the actual request or not
        if ($servletRequest->hasCookie($requestedSessionName = $this->getSessionName($servletRequest))) {
            $sessionId = $servletRequest->getCookie($requestedSessionName)->getValue();
        }

        // return the HTTP session-ID
        return $sessionId;
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
        $configurationFileName = $this->getInitParameter(ControllerServlet::INIT_PARAMETER_ROUTLT_CONFIGURATION_FILE);

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

        // load the action namespace
        $actionNamespace = strtolower($this->getInitParameter(ControllerServlet::INIT_PARAMETER_ACTION_NAMESPACE));

        // register the beans located by annotations and the XML configuration
        foreach ($this->getObjectManager()->getObjectDescriptors() as $descriptor) {
            // check if we've found a servlet descriptor
            if ($descriptor instanceof PathDescriptorInterface) {
                // initialize a new action instance
                $action = $this->newActionInstance($descriptor->getClassName(), new ArrayContext());

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

                // prepare the route, e. g. /index/index
                $route = str_replace($actionNamespace, '', $descriptor->getName());

                // initialize the action mappings
                foreach ($descriptor->getActions() as $actionDescriptor) {
                    // iterate over all request methods
                    foreach ($actionDescriptor->getRequestMethods() as $requestMethod) {
                        // prepare the action path with the route
                        $actionPath = $route;

                        // prepare the action mapping with the route + the action name
                        $actionMapping = array($route, $actionDescriptor->getMethodName());

                        // prepare the action path -> concatenate route + action name
                        $actionPath .= $actionDescriptor->getName();

                        // add the action path -> route mapping for the request method
                        $this->actionMappings[$requestMethod][$actionPath] = $actionMapping;

                        // add an alias for the route for the action's default method
                        if ($actionDescriptor->getMethodName() === $action->getDefaultMethod()) {
                            $this->actionMappings[$requestMethod][$route] = $actionMapping;
                        }
                    }
                }

                // add the initialized action
                $this->routes[$route] = $action;
            }
        }
    }

    /**
     * Creates a new instance of the action with the passed class name and context.
     *
     * @param string                                    $className The class name of the action to be created
     * @param \AppserverIo\Psr\Context\ContextInterface $context   The action context
     *
     * @return \AppserverIo\Routlt\ActionInterface The action instance
     */
    public function newActionInstance($className, ContextInterface $context)
    {
        return new $className($context);
    }

    /**
     * Returns the available routes.
     *
     * @return array The array with the available routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns the array with request method action -> route mappings.
     *
     * @return array The request method action -> route mappings
     */
    public function getActionMappings()
    {
        return $this->actionMappings;
    }

    /**
     * Returns the array with request method action -> route mappings
     * for the passed servlet request.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface $servletRequest The request instance
     *
     * @return array The request method action -> route mappings for the passed request method
     */
    public function getActionMappingsForServletRequest(ServletRequestInterface $servletRequest)
    {

        // load the servlet request method
        $requestMethod = $servletRequest->getMethod();

        // load the action mappings
        $actionMappings = $this->getActionMappings();

        // query whether we've action mappings for the request method or not
        if (isset($actionMappings[$requestMethod])) {
            return $actionMappings[$requestMethod];
        }
    }

    /**
     * Delegates to HTTP method specific functions like doPost() for POST e.g.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response sent back to the client
     *
     * @return void
     *
     * @throws \AppserverIo\Psr\Servlet\ServletException If no action has been found for the requested path
     */
    public function service(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse)
    {

        try {
            // pre-initialize response
            $servletResponse->addHeader(HttpProtocol::HEADER_X_POWERED_BY, get_class($this));

            // load the the DI provider and session manager instance
            $provider = $this->getProvider($servletRequest);
            $sessionId = $this->getSessionId($servletRequest);

            // load the path info from the servlet request
            $pathInfo = $servletRequest->getPathInfo();

            // if the requested action has been found in the path info
            if ($pathInfo == null) {
                $pathInfo = $this->getDefaultRoute();
            }

            // prepare the path of the requested action
            $requestedAction = $pathInfo;

            // load the routes
            $routes = $this->getRoutes();

            // load the action mappings for the actual servlet request
            $actionMappings = $this->getActionMappingsForServletRequest($servletRequest);

            // we start dispatching the request
            $run = true;

            do {
                // query whether one of the routes match the path information
                if (isset($actionMappings[$requestedAction])) {
                    // extract route + method from the action mapping
                    list ($route, $method) = $actionMappings[$requestedAction];

                    // resolve the action with the found mapping
                    $action = $routes[$route];

                    // set the method that has to be invoked in the action context
                    $action->setAttribute(ContextKeys::METHOD_NAME, $method);

                    // inject the dependencies
                    $provider->injectDependencies($action, $sessionId);

                    // pre-dispatch the action
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

                // strip the last directory
                $requestedAction = dirname($requestedAction);

                // query whether we've to stop dispatching
                if ($requestedAction === '/') {
                    $run = false;
                }

            } while ($run);

            // throw an action, because we can't find an action mapping
            throw new DispatchException(
                sprintf('Can\'t find action to dispatch path info %s', $pathInfo)
            );

        } catch (DispatchException $de) {
            // results in a 404 error page
            throw new ServletException($de->__toString(), 404);
        } catch (\Exception $e) {
            // results in a 500 error page
            throw new ServletException($e->__toString(), 500);
        }
    }
}
