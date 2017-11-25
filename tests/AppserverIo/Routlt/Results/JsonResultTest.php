<?php

/**
 * AppserverIo\Routlt\Results\JsonResultTest
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

namespace AppserverIo\Routlt\Results;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Routlt\ActionInterface;
use AppserverIo\Routlt\Util\ValidationAware;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Test implementation for the JsonResult implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class JsonResultTest extends \PHPUnit_Framework_TestCase implements ActionInterface, ValidationAware
{

    /**
     * The result instance to test.
     *
     * @var \AppserverIo\Routlt\Results\JsonResult
     */
    protected $result;

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
        $this->result = new JsonResult();
    }

    /**
     * Tests the init() method.
     *
     * @return void
     */
    public function testInit()
    {

        // create a mock result descriptor
        $mockResultDescriptor = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Description\ResultDescriptorInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the methods
        $mockResultDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $mockResultDescriptor->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('AppserverIo\Routlt\Results\JsonResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(null));

        // invoke the init method
        $this->result->init($mockResultDescriptor);

        // make some assertions
        $this->assertSame(ActionInterface::SUCCESS, $this->result->getName());
        $this->assertSame('AppserverIo\Routlt\Results\JsonResult', $this->result->getType());
        $this->assertNull($this->result->getResult());
    }

    /**
     * Tests the result process() method.
     *
     * @return void
     */
    public function testProcess()
    {

        // create a mock result descriptor
        $mockResultDescriptor = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Description\ResultDescriptorInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the methods
        $mockResultDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $mockResultDescriptor->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('AppserverIo\Routlt\Results\JsonResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(null));

        // invoke the init method
        $this->result->init($mockResultDescriptor);

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder($requestInterface = 'AppserverIo\Routlt\Mock\MockHttpServletRequestInterface')
                                   ->setMethods(get_class_methods($requestInterface))
                                   ->getMock();

        // mock the necessary request methods
        $mockServletRequest->expects($this->once())
            ->method('getAttribute')
            ->with(JsonResult::DATA)
            ->will(
                $this->returnValue(
                    $result = array(
                        'dummyValue' => 'test',
                        'noSetterAvailable' => 100,
                        'throwException' => 'Another Test Value'
                    )
                )
            );

        // create a mock servlet response instance
        $mockServletResponse = $this->getMockBuilder($responseInterface = 'AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface')
                                    ->setMethods(get_class_methods($responseInterface))
                                    ->getMock();

        // mock the necessary response methods
        $mockServletResponse->expects($this->once())
            ->method('addHeader')
            ->with(HttpProtocol::HEADER_CONTENT_TYPE, 'application/json');
        $mockServletResponse->expects($this->once())
            ->method('appendBodyStream')
            ->with(json_encode($result));

        // set the action instance
        $this->result->setAction($this);

        // process the result
        $this->result->process($mockServletRequest, $mockServletResponse);
    }

    /**
     * Tests the result process() method with action errors.
     *
     * @return void
     */
    public function testProcessWithErrors()
    {

        // create a mock result descriptor
        $mockResultDescriptor = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Description\ResultDescriptorInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the methods
        $mockResultDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $mockResultDescriptor->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('AppserverIo\Routlt\Results\JsonResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(null));

        // invoke the init method
        $this->result->init($mockResultDescriptor);

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder($requestInterface = 'AppserverIo\Routlt\Mock\MockHttpServletRequestInterface')
                                   ->setMethods(get_class_methods($requestInterface))
                                   ->getMock();

        // create a mock servlet response instance
        $mockServletResponse = $this->getMockBuilder($responseInterface = 'AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface')
                                    ->setMethods(get_class_methods($responseInterface))
                                    ->getMock();

        // we need some errors
        $this->errors = array(
            'dummyValue' => 'test',
            'noSetterAvailable' => 100,
            'throwException' => 'Another Test Value'
        );

        // mock the necessary response methods
        $mockServletResponse->expects($this->once())
            ->method('addHeader')
            ->with(HttpProtocol::HEADER_CONTENT_TYPE, 'application/json');
        $mockServletResponse->expects($this->once())
            ->method('appendBodyStream')
            ->with(json_encode($this->errors));

        // set the action instance
        $this->result->setAction($this);

        // process the result
        $this->result->process($mockServletRequest, $mockServletResponse);
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
     * @return array The array with action errors
     * @see \AppserverIo\Routlt\Util\ValidationAware::getErrors()
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
