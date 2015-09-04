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
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;
use AppserverIo\Routlt\Util\ContextKeys;
use AppserverIo\Routlt\Util\ActionAware;
use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Routlt\Results\ResultInterface;
use AppserverIo\Routlt\Description\PathDescriptorInterface;
use AppserverIo\Routlt\Description\ResultDescriptorInterface;

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
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface The provider instance
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

                // prepare the action's the result descriptors
                foreach ($descriptor->getResults() as $resultDescriptor) {
                    $action->addResult($this->newResultInstance($resultDescriptor));
                }

                // prepare the route, e. g. /index/index
                $route = str_replace($actionNamespace, '', $descriptor->getName());

                // initialize the action mappings
                foreach ($descriptor->getActions() as $actionDescriptors) {
                    // iterate over all request methods
                    foreach ($actionDescriptors as $requestMethod => $actionDescriptor) {
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
     * Creates a new instance of the action result the passed descriptor.
     *
     * @param \AppserverIo\Routlt\Description\ResultDescriptorInterface $resultDescriptor The action result descriptor
     *
     * @return \AppserverIo\Routlt\ActionInterface The action instance
     */
    public function newResultInstance(ResultDescriptorInterface $resultDescriptor)
    {

        // load the class name
        $className = $resultDescriptor->getType();

        // create a new instance of the result
        $result = new $className($resultDescriptor);

        // if the result is servlet context aware
        if ($result instanceof ServletContextAware) {
            $result->setServletContext($this->getServletContext());
        }

        // return the instance
        return $result;
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
     * Checks whether or not an action is generally available for any request method.
     * Will return TRUE if so, FALSE otherwise.
     * This method replicates a lot of the checks generally necessary but omits the request method check.
     * Still best called in exception- or edge-cases
     *
     * @param string $pathInfo The action path which has been requested
     *
     * @return boolean
     */
    public function checkGeneralActionAvailability($pathInfo)
    {
        // iterate the request methods we have mappings for and check if we can find the requested action
        foreach ($this->getActionMappings() as $actionMapping) {
            $run = true;
            $requestedAction = $pathInfo;
            do {
                if (isset($actionMapping[$requestedAction])) {
                    return true;
                }
                // strip the last directory
                $requestedAction = dirname($requestedAction);

                // query whether we've to stop dispatching
                if ($requestedAction === '/' || $requestedAction === false) {
                    $run = false;
                }
            } while ($run === true);
        }

        // nothing found? Return false then
        return false;
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

        // nothing found? Method must not be allowed then
        throw new DispatchException(
            sprintf('Method %s not allowed', $requestMethod),
            405
        );
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
            $sessionId = $servletRequest->getProposedSessionId();

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
                    $result = $action->perform($servletRequest, $servletResponse);

                    // process the result if available
                    if (($instance = $action->findResult($result)) instanceof ResultInterface) {
                        // query whether or not the result is action aware
                        if ($instance instanceof ActionAware) {
                            $instance->setAction($action);
                        }

                        // process the result
                        $instance->process($servletRequest, $servletResponse);
                    }

                    // post-dispatch the action instance
                    $action->postDispatch($servletRequest, $servletResponse);

                    // stop processing
                    return;
                }

                // strip the last directory
                $requestedAction = dirname($requestedAction);

                // query whether we've to stop dispatching
                if ($requestedAction === '/' || $requestedAction === false) {
                    $run = false;
                }

            } while ($run);

            // We did not find anything for this method/URI connection.
            // We have to evaluate if there simply is a method restriction
            // This replicates a lot of the checks we did before but omits extra iterations in a positive dispatch event,
            // 4xx's should be the exception and can handle that penalty therefore
            if ($this->checkGeneralActionAvailability($pathInfo)) {
                // nothing found? Method must not be allowed then
                throw new DispatchException(
                    sprintf('Method %s not allowed', $servletRequest->getMethod()),
                    405
                );
            }

            // throw an action, because we can't find an action mapping
            throw new DispatchException(
                sprintf('Can\'t find action to dispatch path info %s', $pathInfo),
                404
            );

        } catch (DispatchException $de) {
            // results in a 4xx error
            throw new ServletException($de->__toString(), $de->getCode());
        } catch (\Exception $e) {
            // results in a 500 error page
            throw new ServletException($e->__toString(), 500);
        }
    }
}
