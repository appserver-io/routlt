<?php

/**
 * AppserverIo\Routlt\Results\RawResultTest
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
use AppserverIo\Routlt\Util\EncodingAware;
use AppserverIo\Routlt\Util\DefaultHeadersAware;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Test implementation for the RawResult implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class RawResultTest extends \PHPUnit_Framework_TestCase implements ActionInterface, ValidationAware, EncodingAware, DefaultHeadersAware
{

    /**
     * The result instance to test.
     *
     * @var \AppserverIo\Routlt\Results\RawResult
     */
    protected $result;

    /**
     * The array with the action errors.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * The array with the context attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * The default result value.
     *
     * @var string
     */
    const RESULT = 'my_view_data';

    /**
     * Initializes the interceptor to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->result = new RawResult();
    }

    /**
     * Tests the init() method.
     *
     * @return void
     */
    public function testinit()
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
            ->will($this->returnValue('AppserverIo\Routlt\Results\RawResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(RawResultTest::RESULT));

        // set the descriptor instance
        $this->result->setDescriptor($mockResultDescriptor);

        // invoke the init method
        $this->result->init();

        // make some assertions
        $this->assertSame(ActionInterface::SUCCESS, $this->result->getName());
        $this->assertSame(RawResultTest::RESULT, $this->result->getResult());
        $this->assertSame('AppserverIo\Routlt\Results\RawResult', $this->result->getType());
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
            ->will($this->returnValue('AppserverIo\Routlt\Results\RawResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(RawResultTest::RESULT));

        // set the descriptor instance
        $this->result->setDescriptor($mockResultDescriptor);

        // invoke the init method
        $this->result->init();

        // we add a dummy result value
        $this->setAttribute(RawResultTest::RESULT, $result = array('key' => 'value'));

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder($requestInterface = 'AppserverIo\Routlt\Mock\MockHttpServletRequestInterface')
                                   ->setMethods(get_class_methods($requestInterface))
                                   ->getMock();

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
            ->will($this->returnValue('AppserverIo\Routlt\Results\RawResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(RawResultTest::RESULT));

        // set the descriptor instance
        $this->result->setDescriptor($mockResultDescriptor);

        // invoke the init method
        $this->result->init();

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
     * Tests the result process() method without any data in the result
     *
     * @return void
     */
    public function testProcessWithoutAnyData()
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
            ->will($this->returnValue('AppserverIo\Routlt\Results\RawResult'));
        $mockResultDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue(RawResultTest::RESULT));

        // set the descriptor instance
        $this->result->setDescriptor($mockResultDescriptor);

        // invoke the init method
        $this->result->init();

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder($requestInterface = 'AppserverIo\Routlt\Mock\MockHttpServletRequestInterface')
                                   ->setMethods(get_class_methods($requestInterface))
                                   ->getMock();

        // create a mock servlet response instance
        $mockServletResponse = $this->getMockBuilder($responseInterface = 'AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface')
                                    ->setMethods(get_class_methods($responseInterface))
                                    ->getMock();

        // mock the methods
        $mockServletResponse->expects($this->once())
                            ->method('addHeader')
                            ->with(HttpProtocol::HEADER_CONTENT_TYPE, 'application/json');
        $mockServletResponse->expects($this->once())
                            ->method('appendBodyStream')
                            ->with(null);

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

    /**
     * Attaches the passed value with passed key in the context of the actual request.
     *
     * @param string $key   The key to attach the data under
     * @param mixed  $value The data to be attached
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns the data with the passed key from the context of the actual request.
     *
     * @param string $key The key to return the data for
     *
     * @return mixed The requested data
     */
    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Encodes the passed data, to JSON format for example, and returns it.
     *
     * @param string $data The data to be encoded
     *
     * @return array The encoded data.
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * Returns TRUE if the action has default headers.
     *
     * @return boolean TRUE if the action has default headers, else FALSE
     */
    public function hasDefaultHeaders()
    {
        return sizeof($this->getDefaultHeaders()) > 0;
    }

    /**
     * Returns the array with action's default headers.
     *
     * @return array The array with action's default headers
     */
    public function getDefaultHeaders()
    {
        return array(HttpProtocol::HEADER_CONTENT_TYPE => 'application/json');
    }
}
