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

use AppserverIo\Psr\Context\ArrayContext;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use Doctrine\ORM\Mapping\UniqueConstraint;
use AppserverIo\Appserver\ServletEngine\ServletManager;

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
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // create an array with available routes
        $routes = array('/test' => $action);

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
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // create an array with available routes
        $routes = array('/index' => $action);

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

        // mock the action
        $servletContextAwareInterface = 'AppserverIo\Routlt\Util\ServletContextAware';
        $action = $this->getMock($servletContextAwareInterface, get_class_methods($servletContextAwareInterface));

        // create a mock instance of the servlet manager instance
        $servletManagerInterface = '\AppserverIo\Appserver\ServletEngine\ServletManager';
        $servletManager = $this->getMock($servletManagerInterface, get_class_methods($servletManagerInterface));
        $servletManager->expects($this->once())
            ->method('registerEpbReference');
        $servletManager->expects($this->once())
            ->method('registerResReference');

        // initialize a path descriptor mock instance
        $pathDescriptorInterface = 'AppserverIo\Routlt\Description\PathDescriptorInterface';
        $pathDescriptor = $this->getMock($pathDescriptorInterface, get_class_methods($pathDescriptorInterface));
        $pathDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('DummyAction'));
        $pathDescriptor->expects($this->once())
            ->method('getClassName')
            ->will($this->returnValue('AppserverIo\Routlt\Util\ServletContextAware'));
        $pathDescriptor->expects($this->once())
            ->method('getEpbReferences')
            ->will(
                $this->returnValue(
                    array($this->getMock('AppserverIo\Psr\EnterpriseBeans\Description\EpbReferenceDescriptorInterface'))
                )
            );
        $pathDescriptor->expects($this->once())
            ->method('getResReferences')
            ->will(
                $this->returnValue(
                    array($this->getMock('AppserverIo\Psr\EnterpriseBeans\Description\ResReferenceDescriptorInterface'))
                )
            );

        // add it to the array with return values
        $objectDescriptors = array($pathDescriptor);

        // create a mock instance of the object manager interface
        $objectManagerInterface = 'AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ObjectManagerInterface';
        $objectManager = $this->getMock($objectManagerInterface, get_class_methods($objectManagerInterface));
        $objectManager->expects($this->once())
            ->method('getObjectDescriptors')
            ->will($this->returnValue($objectDescriptors));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getObjectManager', 'getServletContext', 'newActionInstance'));

        // mock object + servlet manager
        $controller->expects($this->once())
            ->method('newActionInstance')
            ->will($this->returnValue($action));
        $controller->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));
        $controller->expects($this->exactly(3))
            ->method('getServletContext')
            ->will($this->returnValue($servletManager));

        // initialize the servlet configuration
        $servletConfig = $this->getMock('AppserverIo\Psr\Servlet\ServletConfigInterface');
        $servletConfig->expects($this->exactly(1))
            ->method('getWebappPath')
            ->will($this->returnValue(__DIR__));

        // invoke the method we want to test
        $controller->init($servletConfig);

        // check the array of routes
        $this->assertEquals(array('DummyAction' => $action), $controller->getRoutes());
    }

    /**
     * This tests the service() method with a request without any registered routes.
     *
     * @expectedException \AppserverIo\Psr\Servlet\ServletException
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
        $controller->expects($this->exactly(2))
            ->method('getInitParameter')
            ->withConsecutive(
                array(ControllerServlet::INIT_PARAMETER_ROUTLT_CONFIGURATION_FILE),
                array(ControllerServlet::INIT_PARAMETER_ACTION_NAMESPACE)
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue('/WEB-INF/routlt.properties'),
                $this->returnValue('/AppserverIo/Routlt/Actions')
            );
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
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();
        // create an array with available routes
        $routes = array('/test' => $action);

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
     * Tests the newActionInstance() method.
     *
     * @return void
     */
    public function testNewActionInstance()
    {

        // initialize the controller with mocked methods
        $controller = new ControllerServlet();

        // test that the created instance is of the correct type
        $this->assertInstanceOf('\stdClass', $controller->newActionInstance('\stdClass', new ArrayContext()));
    }

    /**
     * Test the getSessionManager() method.
     *
     * This test needs a real Request instance, because the interface
     * doesn't implement the getContext() method.
     *
     * @return void
     */
    public function testGetSessionManager()
    {

        // create a mock context instance
        $context = $this->getMockBuilder('AppserverIo\Psr\Naming\NamingDirectoryInterface')
            ->setMethods(get_class_methods('AppserverIo\Psr\Naming\NamingDirectoryInterface'))
            ->getMock();

        // mock the methods
        $context->expects($this->any())
            ->method('search')
            ->with('SessionManagerInterface')
            ->will($this->returnValue($result = new \stdClass()));

        // create a servlet request instance
        $servletRequest = new Request();
        $servletRequest->injectContext($context);

        // initialize the controller
        $controller = new ControllerServlet();

        // check the session manager instance
        $this->assertEquals($result, $controller->getSessionManager($servletRequest));
    }

    /**
     * Test the getSessionName() method.
     *
     * @return void
     */
    public function testGetSessionName()
    {

        // mock the session settings instance
        $sessionSettings = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\SessionSettingsInterface')
            ->setMethods(get_class_methods('AppserverIo\Appserver\ServletEngine\SessionSettingsInterface'))
            ->getMock();

        // mock the methods
        $sessionSettings->expects($this->once())
            ->method('getSessionName')
            ->will($this->returnValue($sessionName = 'mySession'));

        // mock the session manager instance
        $sessionManager = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\SessionManagerInterface')
            ->setMethods(get_class_methods('AppserverIo\Appserver\ServletEngine\SessionManagerInterface'))
            ->getMock();

        // mock the methods
        $sessionManager->expects($this->once())
            ->method('getSessionSettings')
            ->will($this->returnValue($sessionSettings));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getSessionManager'));

        // mock the configuration file name
        $controller->expects($this->once())
            ->method('getSessionManager')
            ->will($this->returnValue($sessionManager));

        // create a mock servlet reques instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');

        // check the session name
        $this->assertSame($sessionName, $controller->getSessionName($servletRequest));
    }

    /**
     * Test the getSessionId() method.
     *
     * @return void
     */
    public function testGetSessionId()
    {

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getSessionName'));

        // mock the session name
        $controller->expects($this->once())
            ->method('getSessionName')
            ->will($this->returnValue($sessionName = 'mySession'));

        // create a mock cookie
        $cookie = $this->getMockBuilder('AppserverIo\Http\HttpCookie')
            ->disableOriginalConstructor()
            ->setMethods(array('getValue'))
            ->getMock();

        // mock the methods
        $cookie->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($sessionId = uniqid()));

        // create a mock servlet reques instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');

        // mock the methods
        $servletRequest->expects($this->once())
            ->method('hasCookie')
            ->with($sessionName)
            ->will($this->returnValue(true));
        $servletRequest->expects($this->once())
            ->method('getCookie')
            ->with($sessionName)
            ->will($this->returnValue($cookie));

        // check the session name
        $this->assertSame($sessionId, $controller->getSessionId($servletRequest));
    }

    /**
     * Test the getProvider() method.
     *
     * This test needs a real Request instance, because the interface
     * doesn't implement the getContext() method.
     *
     * @return void
     */
    public function testGetProvider()
    {

        // create a mock context instance
        $context = $this->getMockBuilder('AppserverIo\Psr\Naming\NamingDirectoryInterface')
            ->setMethods(get_class_methods('AppserverIo\Psr\Naming\NamingDirectoryInterface'))
            ->getMock();

        // mock the methods
        $context->expects($this->any())
            ->method('search')
            ->with('ProviderInterface')
            ->will($this->returnValue($result = new \stdClass()));

        // create a servlet request instance
        $servletRequest = new Request();
        $servletRequest->injectContext($context);

        // initialize the controller
        $controller = new ControllerServlet();

        // check the session manager instance
        $this->assertEquals($result, $controller->getProvider($servletRequest));
    }

    /**
     * Test the getObjectManager() method.
     *
     * @return void
     */
    public function testGetObjectManager()
    {

        // create a mock context instance
        $context = $this->getMockBuilder('AppserverIo\Psr\Naming\NamingDirectoryInterface')
            ->setMethods(get_class_methods('AppserverIo\Psr\Naming\NamingDirectoryInterface'))
            ->getMock();

        // mock the methods
        $context->expects($this->any())
            ->method('search')
            ->with('ObjectManagerInterface')
            ->will($this->returnValue($result = new \stdClass()));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getNamingDirectory'));

        // mock the methods
        $controller->expects($this->once())
            ->method('getNamingDirectory')
            ->will($this->returnValue($context));

        // check the session manager instance
        $this->assertEquals($result, $controller->getObjectManager());
    }

    /**
     * Test the getNamingDirectory() method.
     *
     * @return void
     */
    public function testGetNamingDirectory()
    {

        // mock the application
        $application = $this->getMockBuilder('AppserverIo\Psr\Application\ApplicationInterface')
            ->setMethods(get_class_methods('AppserverIo\Psr\Application\ApplicationInterface'))
            ->getMock();

        // create a real manager instance -> this is a \Stackable
        $servletManager = new ServletManager();
        $servletManager->injectApplication($application);

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getServletContext'));

        // mock the methods
        $controller->expects($this->once())
            ->method('getServletContext')
            ->will($this->returnValue($servletManager));

        // check the session manager instance
        $this->assertEquals($application, $controller->getNamingDirectory());
    }

    /**
     * Test the addPathDescriptor() + getPathDescriptors() methods.
     *
     * @return void
     */
    public function testAddGetPathDescriptors()
    {

        // mock a path descriptor
        $pathDescriptor = $this->getMockBuilder('AppserverIo\Routlt\Description\PathDescriptorInterface')
            ->setMethods(get_class_methods('AppserverIo\Routlt\Description\PathDescriptorInterface'))
            ->getMock();

        // initialize the controller
        $controller = new ControllerServlet();

        // add a path descriptor
        $controller->addPathDescriptor($pathDescriptor);

        // count the number of path descriptors and check the path descriptor itself
        $this->assertCount(1, $pathDescriptors = $controller->getPathDescriptors());
        $this->assertSame($pathDescriptor, array_pop($pathDescriptors));
    }
}
