<?php

/**
 * AppserverIo\Routlt\Interceptors\AbstractInterceptorTest
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

namespace AppserverIo\Routlt\Interceptors;

use AppserverIo\Routlt\ActionInterface;
use AppserverIo\Routlt\Util\ValidationAware;
use AppserverIo\Routlt\Results\ResultInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Test implementation for the AbstractInterceptorTest implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class AbstractInterceptorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The interceptor instance to test.
     *
     * @var \AppserverIo\Routlt\Interceptors\AbstractInterceptor
     */
    protected $interceptor;

    /**
     * Initializes the interceptor to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->interceptor = $this->getMockForAbstractClass('AppserverIo\Routlt\Interceptors\AbstractInterceptor');
    }

    /**
     * Tests the default interceptor functionality with a success.
     *
     * @return void
     */
    public function testExecuteWithSuccess()
    {

        // mock the execute method
        $this->interceptor->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(ActionInterface::SUCCESS));

        // create a mock servlet request + response instance
        $mockServletRequest = $this->getMock('AppserverIo\Appserver\ServletEngine\Http\Request');
        $mockServletResponse = $this->getMock('AppserverIo\Appserver\ServletEngine\Http\Response');

        // create a method invocation mock
        $mockMethodInvocation = $this->getMock('AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface');

        // mock the methods
        $mockMethodInvocation->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->getMock('AppserverIo\Routlt\ActionInterface')));
        $mockMethodInvocation->expects($this->once())
            ->method('getParameters')
            ->will(
                $this->returnValue(
                    array(
                        AbstractInterceptor::SERVLET_REQUEST => $mockServletRequest,
                        AbstractInterceptor::SERVLET_RESPONSE => $mockServletResponse
                    )
                )
            );

        // invoke the interceptor functionality and check the result
        $this->assertSame(ActionInterface::SUCCESS, $this->interceptor->intercept($mockMethodInvocation));
    }

    /**
     * Tests the default interceptor functionality with a failure.
     *
     * @return void
     */
    public function testExecuteWithFailure()
    {

        // create a method invocation mock
        $mockMethodInvocation = $this->getMock('AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface');

        // create a mock action
        $mockAction = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockMethodInvocation->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($mockAction));
        $mockMethodInvocation->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array()));

        // mock the execute method
        $this->interceptor->expects($this->once())
            ->method('execute')
            ->will($this->throwException(new \Exception($message = 'Test Exception Message')));

        // invoke the interceptor functionality and check the result
        $this->assertSame(ActionInterface::FAILURE, $this->interceptor->intercept($mockMethodInvocation));
    }

    /**
     * Tests the setter/getter for the parameters.
     *
     * @return void
     */
    public function testSetGetParameters()
    {
        $this->interceptor->setParameters($parameters = array($key = 'key' => $value = 'value'));
        $this->assertSame($value, $this->interceptor->getParameter($key));
    }

    /**
     * Tests the getter for the servlet request/response instances.
     *
     * @return void
     */
    public function testGetServletRequestAndResponse()
    {

        // create a mock servlet request + response instance
        $mockServletRequest = $this->getMock('AppserverIo\Appserver\ServletEngine\Http\Request');
        $mockServletResponse = $this->getMock('AppserverIo\Appserver\ServletEngine\Http\Response');

        // create a method invocation mock
        $parameters = array(
            AbstractInterceptor::SERVLET_REQUEST => $mockServletRequest,
            AbstractInterceptor::SERVLET_RESPONSE => $mockServletResponse
        );

        // set the parameters
        $this->interceptor->setParameters($parameters);

        // test that servlet request/response are available
        $this->assertSame($mockServletRequest, $this->interceptor->getServletRequest());
        $this->assertSame($mockServletResponse, $this->interceptor->getServletResponse());
    }

    /**
     * Tests the setter/getter for empty parameters.
     *
     * @return void
     */
    public function testSetGetWithEmptyParameters()
    {
        $this->interceptor->setParameters($parameters = array());
        $this->assertNull($this->interceptor->getParameter('key'));
    }

    /**
     * Tests the setter/getter for the action.
     *
     * @return void
     */
    public function testSetGetAction()
    {
        $this->interceptor->setAction($action = $this->getMock('AppserverIo\Routlt\ActionInterface'));
        $this->assertSame($action, $this->interceptor->getAction());
    }

    /**
     * Tests the getter for the action methods if no action is available.
     *
     * @return void
     */
    public function testGetActionMethodsWithoutAction()
    {
        $this->assertNull($this->interceptor->getActionMethods());
    }
}
