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

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Psr\Context\ArrayContext;

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
        $methods = array('getProvider', 'getSessionManager', 'getRoutes', 'getActionMappingsForServletRequest');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test'));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock result
        $result = $this->getMockBuilder('AppserverIo\Routlt\Results\JsonResult')
            ->disableOriginalConstructor()
            ->getMock();

        // mock some methods
        $result->expects($this->once())
            ->method('process')
            ->with($servletRequest, $servletResponse);

        // create a mock action instance
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // mock some methods
        $action->expects($this->once())
            ->method('perform')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $action->expects($this->once())
            ->method('findResult')
            ->with(ActionInterface::SUCCESS)
            ->will($this->returnValue($result));

        // create two mock action mappings
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));
        $actionMapping->expects($this->once())->method('match')->willReturn(true);
        $actionMapping->expects($this->once())->method('getControllerName')->willReturn('/test');
        $actionMapping->expects($this->once())->method('getMethodName')->willReturn(null);
        $actionMapping->expects($this->once())->method('getRequestParameters')->willReturn(array());

        // create an array with available routes
        $routes = array('/test' => $action);
        $actionMappings = array('/test' => $actionMapping);

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Psr\Di\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));
        $provider->expects($this->exactly(2))->method('injectDependencies');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getActionMappingsForServletRequest')
            ->will($this->returnValue($actionMappings));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));

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
        $methods = array('getProvider', 'getSessionManager', 'getRoutes', 'getActionMappingsForServletRequest');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock action instance
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // create two mock action mappings
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));
        $actionMapping->expects($this->once())->method('match')->willReturn(true);
        $actionMapping->expects($this->once())->method('getControllerName')->willReturn('/index');
        $actionMapping->expects($this->once())->method('getMethodName')->willReturn('/indexAction');
        $actionMapping->expects($this->once())->method('getRequestParameters')->willReturn(array());

        // create an array with available routes
        $routes = array('/index' => $action);
        $actionMappings = array('/index' => $actionMapping);

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Psr\Di\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));
        $provider->expects($this->exactly(2))->method('injectDependencies');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getActionMappingsForServletRequest')
            ->will($this->returnValue($actionMappings));
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));

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

        // create an action descriptor
        $actionDescriptor = $this->getMockBuilder('AppserverIo\Routlt\Description\ActionDescriptorInterface')
            ->setMethods(get_class_methods('AppserverIo\Routlt\Description\ActionDescriptorInterface'))
            ->getMock();

        // mock the methods
        $actionDescriptor->expects($this->exactly(2))
                         ->method('getRestrictions')
                         ->willReturn(array());
        $actionDescriptor->expects($this->exactly(2))
                         ->method('getDefaults')
                         ->willReturn(array());

        // create an result descriptor
        $resultDescriptor = $this->getMockBuilder('AppserverIo\Routlt\Description\ResultDescriptorInterface')
            ->setMethods(get_class_methods('AppserverIo\Routlt\Description\ResultDescriptorInterface'))
            ->getMock();

        // mock the action
        $actionInterface = 'AppserverIo\Routlt\ActionInterface';
        $action = $this->getMock($actionInterface, get_class_methods($actionInterface));

        // mock the result
        $resultInterface = 'AppserverIo\Routlt\Results\ResultInterface';
        $result = $this->getMock($resultInterface, get_class_methods($resultInterface));

        // create a mock instance of the servlet manager instance
        $servletManagerInterface = 'AppserverIo\Routlt\Mock\MockServletContextInterface';
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
            ->method('getActions')
            ->will($this->returnValue(array(HttpProtocol::METHOD_GET => array($actionDescriptor))));
        $pathDescriptor->expects($this->once())
            ->method('getResults')
            ->will($this->returnValue(array($resultDescriptor)));
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
        $objectManagerInterface = 'AppserverIo\Psr\Di\ObjectManagerInterface';
        $objectManager = $this->getMock($objectManagerInterface, get_class_methods($objectManagerInterface));
        $objectManager->expects($this->once())
            ->method('getObjectDescriptors')
            ->will($this->returnValue($objectDescriptors));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getObjectManager', 'getServletContext', 'newActionInstance', 'newResultInstance'));

        // mock object + servlet manager
        $controller->expects($this->once())
            ->method('newActionInstance')
            ->will($this->returnValue($action));
        $controller->expects($this->once())
            ->method('newResultInstance')
            ->will($this->returnValue($result));
        $controller->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));
        $controller->expects($this->exactly(2))
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
     * This tests the initRoutes() method.
     *
     * @return void
     */
    public function testInitRoutesWithServletContextAwareAction()
    {

        // mock the action
        $servletContextAwareInterface = 'AppserverIo\Routlt\Util\ServletContextAware';
        $action = $this->getMock($servletContextAwareInterface, get_class_methods($servletContextAwareInterface));

        // create a mock instance of the servlet manager instance
        $servletManagerInterface = 'AppserverIo\Routlt\Mock\MockServletContextInterface';
        $servletManager = $this->getMock($servletManagerInterface, get_class_methods($servletManagerInterface));

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
            ->method('getActions')
            ->will($this->returnValue(array()));
        $pathDescriptor->expects($this->once())
            ->method('getResults')
            ->will($this->returnValue(array()));
        $pathDescriptor->expects($this->once())
            ->method('getEpbReferences')
            ->will($this->returnValue(array()));
        $pathDescriptor->expects($this->once())
            ->method('getResReferences')
            ->will($this->returnValue(array()));

        // add it to the array with return values
        $objectDescriptors = array($pathDescriptor);

        // create a mock instance of the object manager interface
        $objectManagerInterface = 'AppserverIo\Psr\Di\ObjectManagerInterface';
        $objectManager = $this->getMock($objectManagerInterface, get_class_methods($objectManagerInterface));
        $objectManager->expects($this->once())
            ->method('getObjectDescriptors')
            ->will($this->returnValue($objectDescriptors));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getObjectManager', 'getServletContext', 'newActionInstance', 'newResultInstance'));

        // mock object + servlet manager
        $controller->expects($this->once())
            ->method('newActionInstance')
            ->will($this->returnValue($action));
        $controller->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));
        $controller->expects($this->exactly(1))
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
     * This tests the checkGeneralActionAvailability() method.
     *
     * @return void
     */
    public function testCheckGeneralActionAvailability()
    {

        // create a mock action mapping
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping1 = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));
        $actionMapping2 = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));

        // prepare some mock mappings
        $actionMappings = array(
            HttpProtocol::METHOD_GET => array(
                '/tests' => $actionMapping1
            ),
            HttpProtocol::METHOD_POST => array(
                '/test2' => $actionMapping2
            )
        );

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getActionMappings'));

        // mock object + servlet manager
        $controller->expects($this->exactly(4))
            ->method('getActionMappings')
            ->will($this->returnValue($actionMappings));

        // some positives
        $this->assertTrue($controller->checkGeneralActionAvailability('/tests'));
        $this->assertTrue($controller->checkGeneralActionAvailability('/test2'));
        $this->assertTrue($controller->checkGeneralActionAvailability('/tests/1/resource/2'));

        // and some negative tests
        $this->assertFalse($controller->checkGeneralActionAvailability('/notests/1'));
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
        $providerInterface = 'AppserverIo\Psr\Di\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));

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
        $objectManagerInterface = 'AppserverIo\Psr\Di\ObjectManagerInterface';
        $objectManager = $this->getMock($objectManagerInterface, get_class_methods($objectManagerInterface));
        $objectManager->expects($this->once())
            ->method('getObjectDescriptors')
            ->will($this->returnValue(array()));

        // create a mock instance of the servlet manager instance
        $servletManagerInterface = 'AppserverIo\Routlt\Mock\MockServletContextInterface';
        $servletManager = $this->getMock($servletManagerInterface, get_class_methods($servletManagerInterface));
        $servletManager->expects($this->once())
            ->method('addInitParameter')
            ->with('property.key', 'property-value');

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
        $methods = array('getProvider', 'getSessionManager', 'getRoutes', 'getActionMappingsForServletRequest');

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

        // create a mock action mapping
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));
        $actionMapping->expects($this->once())->method('match')->willReturn(true);
        $actionMapping->expects($this->once())->method('getControllerName')->willReturn('/test');
        $actionMapping->expects($this->once())->method('getMethodName')->willReturn('/index');
        $actionMapping->expects($this->once())->method('getRequestParameters')->willReturn(array());

        // create an array with available routes
        $routes = array('/test' => $action);
        $actionMappings = array('/test' => $actionMapping);

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Psr\Di\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));
        $provider->expects($this->once())->method('injectDependencies');

        // assert that the array with the routes will be loaded
        $controller->expects($this->once())
            ->method('getActionMappingsForServletRequest')
            ->will($this->returnValue($actionMappings));
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));

        // invoke the method we want to test
        $controller->service($servletRequest, $servletResponse);
    }

    /**
     * This tests that a not dispatched request stop request processing.
     *
     * @return void
     * @expectedException AppserverIo\Psr\Servlet\ServletException
     * @expectedExceptionCode 405
     */
    public function testIsNotDispatchedWith405()
    {

        // initialize the array with the methods to mock
        $methods = array('getProvider', 'getSessionManager', 'getRoutes', 'getActionMappings');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/tests/2'));
        $servletRequest->expects($this->exactly(2))
            ->method('getMethod')
            ->will($this->returnValue(HttpProtocol::METHOD_GET));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock action instance
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // create two mock action mappings
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping1 = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));
        $actionMapping2 = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));

        // create an array with available routes
        $routes = array('/tests' => $action);

        // prepare some mock mappings
        $actionMappings = array(
            HttpProtocol::METHOD_POST => array(
                '/tests' => $actionMapping1
            ),
            HttpProtocol::METHOD_GET => array(
                '/test2' => $actionMapping2
            )
        );

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Psr\Di\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));

        // assert that the array with the routes will be loaded
        $controller->expects($this->exactly(2))
            ->method('getActionMappings')
            ->will($this->returnValue($actionMappings));
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));

        // invoke the method we want to test
        $controller->service($servletRequest, $servletResponse);
    }

    /**
     * This tests that a not dispatched request stop request processing.
     *
     * @return void
     * @expectedException AppserverIo\Psr\Servlet\ServletException
     * @expectedExceptionCode 404
     */
    public function testIsNotDispatchedWith404()
    {

        // initialize the array with the methods to mock
        $methods = array('getProvider', 'getSessionManager', 'getRoutes', 'getActionMappings');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', $methods);

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test'));
        $servletRequest->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue(HttpProtocol::METHOD_GET));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');

        // create a mock action instance
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // create two mock action mappings
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping1 = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));
        $actionMapping2 = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));

        // create an array with available routes
        $routes = array('/tests' => $action);
        // prepare some mock mappings
        $actionMappings = array(
            HttpProtocol::METHOD_POST => array(
                '/tests' => $actionMapping1
            ),
            HttpProtocol::METHOD_GET => array(
                '/test2' => $actionMapping2
            )
        );

        // create a mock instance of the DI provider
        $providerInterface = 'AppserverIo\Psr\Di\ProviderInterface';
        $provider = $this->getMock($providerInterface, get_class_methods($providerInterface));

        // assert that the array with the routes will be loaded
        $controller->expects($this->exactly(2))
            ->method('getActionMappings')
            ->will($this->returnValue($actionMappings));
        $controller->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($routes));
        $controller->expects($this->once())
            ->method('getProvider')
            ->will($this->returnValue($provider));

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

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Routlt\Mock\MockHttpServletRequestInterface');

        // mock the methods
        $servletRequest->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        // initialize the controller
        $controller = new ControllerServlet();

        // check the session manager instance
        $this->assertEquals($result, $controller->getSessionManager($servletRequest));
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

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('AppserverIo\Routlt\Mock\MockHttpServletRequestInterface');

        // mock the methods
        $servletRequest->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

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

         // mock a servlet manager
        $servletManager = $this->getMockBuilder('AppserverIo\Routlt\Mock\MockServletContextInterface')
            ->setMethods(get_class_methods('AppserverIo\Routlt\Mock\MockServletContextInterface'))
            ->getMock();

        // mock the methods
        $servletManager->expects($this->once())
            ->method('getApplication')
            ->willReturn($application);

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

    /**
     * Test if the getActionMappingsForServletRequest() returns the action mappings for
     * the given request method.
     *
     * @return void
     */
    public function testGetActionMappingsForServletRequest()
    {

        // create a mock action mapping
        $actionMappingInterface = 'AppserverIo\Routlt\ActionMappingInterface';
        $actionMapping = $this->getMock($actionMappingInterface, get_class_methods($actionMappingInterface));

        // intialize the action mappings
        $actionMappings = array('/test' => $actionMapping);

        // create a mock servlet request instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue(HttpProtocol::METHOD_GET));

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getActionMappings'));
        $controller->expects($this->once())
            ->method('getActionMappings')
            ->will($this->returnValue(array(HttpProtocol::METHOD_GET => $actionMappings)));

        // query whether we found the action mappings for the request method
        $this->assertSame($actionMappings, $controller->getActionMappingsForServletRequest($servletRequest));
    }

    /**
     * Test if the service() method catches a simple exception;
     *
     * @return void
     *
     * @expectedException \AppserverIo\Psr\Servlet\ServletException
     */
    public function testServiceWithServletException()
    {

        // create a mock servlet reques instance
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');

        // create a mock servlet response instance
        $servletResponse = $this->getMockBuilder('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface')
            ->setMethods(get_class_methods('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface'))
            ->getMock();

        // mock the methods
        $servletResponse->expects($this->once())
            ->method('addHeader')
            ->with(HttpProtocol::HEADER_X_POWERED_BY, 'AppserverIo\Routlt\ControllerServlet')
            ->will($this->throwException(new \Exception('Invalid Header')));

        // initialize a new controller instance
        $controller = new ControllerServlet();

        // invoke the service() method
        $controller->service($servletRequest, $servletResponse);
    }

    /**
     * Tests the newResultInstance() method.
     *
     * @return void
     */
    public function testNewResultInstance()
    {

        // create a mock instance of the servlet manager instance
        $servletManager = $this->getMock('AppserverIo\Routlt\Mock\MockServletContextInterface');

        // initialize the controller with mocked methods
        $controller = $this->getMock('AppserverIo\Routlt\ControllerServlet', array('getServletContext'));

        // mock object + servlet manager
        $controller->expects($this->once())
            ->method('getServletContext')
            ->will($this->returnValue($servletManager));

        // create a mock result descriptor
        $resultDescriptorMock = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Description\ResultDescriptorInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the methods
        $resultDescriptorMock->expects($this->exactly(2))
            ->method('getType')
            ->will($this->returnValue($type = 'AppserverIo\Routlt\Results\ServletDispatcherResult'));

        // test that the created instance is of the correct type
        $this->assertInstanceOf($type, $controller->newResultInstance($resultDescriptorMock));
    }
}
