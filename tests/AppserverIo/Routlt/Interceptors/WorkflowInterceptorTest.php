<?php

/**
 * AppserverIo\Routlt\Interceptors\WorkflowInterceptorTest
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
use AppserverIo\Routlt\Util\Validateable;
use AppserverIo\Routlt\Util\ValidationAware;
use AppserverIo\Routlt\Results\ResultInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\Http\Request;

/**
 * Test implementation for the WorkflowInterceptor implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class WorkflowInterceptorTest extends \PHPUnit_Framework_TestCase implements ActionInterface, ValidationAware, Validateable
{

    /**
     * The interceptor instance to test.
     *
     * @var \AppserverIo\Routlt\Interceptors\WorkflowInterceptor
     */
    protected $interceptor;

    /**
     * The array with the action errors.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Initializes the interceptor to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->interceptor = new WorkflowInterceptor();
    }

    /**
     * Tests the default interceptor functionality.
     *
     * @return void
     */
    public function testExecuteWithSuccess()
    {

        // create a method invocation mock
        $mockMethodInvocation = $this->getMock('AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface');

        // mock the methods
        $mockMethodInvocation->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this));
        $mockMethodInvocation->expects($this->once())
            ->method('proceed')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $mockMethodInvocation->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array()));

        // invoke the interceptor functionality and check the result
        $this->assertSame(ActionInterface::SUCCESS, $this->interceptor->intercept($mockMethodInvocation));
    }

    /**
     * Tests the default interceptor functionality.
     *
     * @return void
     */
    public function testExecuteWithFailure()
    {

        // create a method invocation mock
        $mockMethodInvocation = $this->getMock('AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface');

        // mock the methods
        $mockMethodInvocation->expects($this->exactly(2))
            ->method('getContext')
            ->will($this->returnValue($this));
        $mockMethodInvocation->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array()));

        // we add an error message to simulate an error
        $this->addFieldError('test', 'A Message');

        // invoke the interceptor functionality and check the result
        $this->assertSame(ActionInterface::FAILURE, $this->interceptor->intercept($mockMethodInvocation));
    }

    /**
     * Returns the servlet response instance.
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface The request instance
     */
    public function getServletRequest()
    {
        return new Request();
    }

    /**
     * The validation method that implements the action's validation method.
     *
     * @return void
     * @see AppserverIo\Routlt\Util\Validatable::validate()
     */
    public function validate()
    {
    }

    /**
     * Method that will be invoked before we dispatch the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function preDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
    }

    /**
     * All classes extending this class must implement the perform() method.
     *
     * This method implements the complete functionality of the action and have to return an initialized
     * ActionForward object that is necessary for further application flow controlled by the
     * ActionController.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
    */
    public function perform(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
    }

    /**
     * Method that will be invoked after we've dispatched the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
    */
    public function postDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
    }

    /**
     * This method returns the default method name we'll invoke if the path info doesn't contain
     * the method name, that'll be the second element, when we explode the path info with a slash.
     *
     * @return string The default action method name that has to be invoked
     */
    public function getDefaultMethod()
    {
        return 'indexAction';
    }

    /**
     * Adds the result to the action.
     *
     * @param \AppserverIo\Routlt\Results\ResultInterface $result
     *
     * @return void
     */
    public function addResult(ResultInterface $result)
    {
    }

    /**
     * Tries to find and return the result with the passed name.
     *
     * @param string $name The name of the result to return
     *
     * @return \AppserverIo\Routlt\Results\ResultInterface|null The requested result
     */
    public function findResult($name)
    {
    }

    /**
     * Adds a field error with the passed name and message.
     *
     * @param string $name    The name to add the message with
     * @param string $message The message to add
     *
     * @return void
     * @see \AppserverIo\Routlt\Util\ValidationAware::addFieldError()
     */
    public function addFieldError($name, $message)
    {
        $this->errors[$name] = $message;
    }

    /**
     * Returns TRUE if validation found errors, else FALSE.
     *
     * @return boolean TRUE if validation found errors, else FALSE
     * @see \AppserverIo\Routlt\Util\ValidationAware::hasErrors()
     */
    public function hasErrors()
    {
        return sizeof($this->errors) > 0;
    }

    /**
     * Returns the array with action errors.
     *
     * @return The array with action errors
     * @see \AppserverIo\Routlt\Util\ValidationAware::getErrors()
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
