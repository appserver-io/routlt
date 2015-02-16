<?php

/**
 * AppserverIo\Routlt\ControllerServletTest
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

/**
 * This is test implementation for the controller servlet implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ControllerServletTest extends \PHPUnit_Framework_TestCase
{

    /**
     * This tests the service() method with a request, prepared with a path info.
     *
     * @return void
     */
    public function testServiceWithPathInfo()
    {

        // initialize the array with the methods to mock
        $methods = array('getProvider', 'getSessionManager', 'getRoutes');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test'));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock action instance
        $action = $this->getMock('AppserverIo\Routlt\ActionInterface');
        $action->expects($this->once())->method('preDispatch');
        $action->expects($this->once())->method('perform');
        $action->expects($this->once())->method('postDispatch');

        // create an array with available routes
        $routes = array('/test*' => $action);

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));
        $provider->expects($this->once())->method('injectDependencies');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));
        $controller->expects($this->once())
            ->method('getSessionManager')
            ->will($this->returnValue(null));

        // invoke the method we want to test
        $controller->service($servletRequest, $servletResponse);
    }

    /**
     * This tests the service() method with a request without any path info.
     *
     * @return void
     */
    public function testServiceWithoutPathInfo()
    {

        // initialize the array with the methods to mock
        $methods = array('getProvider', 'getSessionManager', 'getRoutes');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock action instance
        $action = $this->getMock('AppserverIo\Routlt\ActionInterface');
        $action->expects($this->once())->method('preDispatch');
        $action->expects($this->once())->method('perform');
        $action->expects($this->once())->method('postDispatch');

        // create an array with available routes
        $routes = array('/index*' => $action);

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));
        $provider->expects($this->once())->method('injectDependencies');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));
        $controller->expects($this->once())
            ->method('getSessionManager')
            ->will($this->returnValue(null));

        // invoke the method we want to test
        $controller->service($servletRequest, $servletResponse);
    }

    /**
     * This tests the initRoutes() method.
     *
     * @return void
     */
    public function testInitRoutes()
    {

        // initialize a path descriptor mock instance
        $pathDescriptorInterface = 'AppserverIo\Routlt\Description\PathDescriptorInterface';
        $pathDescriptor = $this->getMock($pathDescriptorInterface, get_class_methods($pathDescriptorInterface));
        $pathDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('DummyAction'));
        $pathDescriptor->expects($this->once())
            ->method('getClassName')
            ->will($this->returnValue('\stdClass'));
        $pathDescriptor->expects($this->once())
            ->method('getEpbReferences')
            ->will($this->returnValue(array()));
        $pathDescriptor->expects($this->once())
            ->method('getResReferences')
            ->will($this->returnValue(array()));

        // add it to the array with return values
        $objectDescriptors = array($pathDescriptor);

        // create a mock instance of the object manager interface
        $objectManagerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ObjectManagerInterface';
        $objectManager = $this->getMock($objectManagerInterface, get_class_methods($objectManagerInterface));
        $objectManager->expects($this->once())
            ->method('getObjectDescriptors')
            ->will($this->returnValue($objectDescriptors));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getObjectManager'));

        // mock the configuration file name
        $controller->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));

        // initialize the servlet configuration
        $servletConfig = $this->getMock('AppserverIo\Psr\Servlet\ServletConfigInterface');
        $servletConfig->expects($this->exactly(1))
            ->method('getWebappPath')
            ->will($this->returnValue(__DIR__));

        // invoke the method we want to test
        $controller->init($servletConfig);

        // check the array of routes
        $this->assertEquals(array('DummyAction' => new \stdClass()), $controller->getRoutes());
    }

    /**
     * This tests the service() method with a request without any registered routes.
     *
     * @expectedException \AppserverIo\Server\Exceptions\ModuleException
     * @return void
     */
    public function testServiceWithModuleExceptionExpected()
    {

        // initialize the array with the methods to mock
        $methods = array('getProvider', 'getSessionManager', 'getRoutes');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create an array with available routes
        $routes = array();

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));
        $controller->expects($this->once())
            ->method('getSessionManager')
            ->will($this->returnValue(null));

        // invoke the method we want to test
        $controller->service($servletRequest, $servletResponse);
    }

    /**
     * This tests the init method with mocked servlet configuration.
     *
     * @return void
     */
    public function testInit()
    {

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getRoutes', 'initRoutes', 'initConfiguration'));

        // initialize the servlet configuration
        $servletConfig = $this->getMock('AppserverIo\Psr\Servlet\ServletConfigInterface');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('initConfiguration');
        $controller->expects($this->once())
            ->method('initRoutes');

        // invoke the method we want to test
        $controller->init($servletConfig);
    }

    /**
     * This tests the init method with mocked servlet configuration.
     *
     * @return void
     */
    public function testInitWithRealData()
    {

        // create a mock instance of the object manager interface
        $objectManagerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ObjectManagerInterface';
        $objectManager = $this->getMock($objectManagerInterface, get_class_methods($objectManagerInterface));
        $objectManager->expects($this->once())
            ->method('getObjectDescriptors')
            ->will($this->returnValue(array()));

        // create a mock instance of the servlet manager instance
        $servletManagerInterface = '\AppserverIo\Appserver\ServletEngine\ServletManager';
        $servletManager = $this->getMock($servletManagerInterface, get_class_methods($servletManagerInterface));
        $servletManager->expects($this->once())
            ->method('addInitParameter')
            ->with('property-value');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getInitParameter', 'getWebappPath', 'getObjectManager', 'getServletContext'));

        // mock the configuration file name
        $controller->expects($this->once())
            ->method('getInitParameter')
            ->with(ControllerServlet::INIT_PARAMETER_ROUTLT_CONFIGURATION_FILE)
            ->will($this->returnValue('/WEB-INF/routlt.properties'));
        $controller->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));
        $controller->expects($this->once())
            ->method('getServletContext')
            ->will($this->returnValue($servletManager));

        // initialize the servlet configuration
        $servletConfig = $this->getMock('AppserverIo\Psr\Servlet\ServletConfigInterface');
        $servletConfig->expects($this->once())
            ->method('getWebappPath')
            ->will($this->returnValue(__DIR__));

        // invoke the method we want to test
        $controller->init($servletConfig);
    }

    /**
     * This tests that a dispatched request stop request processing.
     *
     * @return void
     */
    public function testIsDispatchedWithPathInfo()
    {

        // initialize the array with the methods to mock
        $methods = array('getProvider', 'getSessionManager', 'getRoutes');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test'));
        $servletRequest->expects($this->once())
            ->method('isDispatched')
            ->will($this->returnValue(true));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock action instance
        $action = $this->getMock('AppserverIo\Routlt\ActionInterface');
        $action->expects($this->once())->method('preDispatch');
        $action->expects($this->never())->method('perform');
        $action->expects($this->never())->method('postDispatch');

        // create an array with available routes
        $routes = array('/test*' => $action);

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));
        $provider->expects($this->once())->method('injectDependencies');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));
        $controller->expects($this->once())
            ->method('getSessionManager')
            ->will($this->returnValue(null));

        // invoke the method we want to test
        $controller->service($servletRequest, $servletResponse);
    }
}
