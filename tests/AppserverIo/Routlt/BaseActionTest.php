<?php

/**
 * AppserverIo\Routlt\BaseActionTest
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
 * This is test implementation for the abstract base action implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class BaseActionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The abstract action instance to test.
     *
     * @var \AppserverIo\Routlt\BaseAction
     */
    protected $action;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->action = $this->getMockForAbstractClass('AppserverIo\Routlt\BaseAction');
    }

    /**
     * This test checks the preDispatch() method.
     *
     * @return void
     */
    public function testPreDispatch()
    {
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');
        $this->assertNull($this->action->preDispatch($servletRequest, $servletResponse));
    }

    /**
     * This test checks the postDispatch() method.
     *
     * @return void
     */
    public function testPostDispatch()
    {
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');
        $this->assertNull($this->action->postDispatch($servletRequest, $servletResponse));
    }

    /**
     * This test checks the setAttribute() and getAttribute() method.
     *
     * @return void
     */
    public function testSetGetAttribute()
    {

        // initialize the action
        $action = $this->getMockBuilder('AppserverIo\Routlt\BaseAction')
            ->setMethods(array('getServletContext'))
            ->getMockForAbstractClass();

        // add a value to the context
        $action->setAttribute($key = 'testKey', $value = 'testValue');

        // check that the values has been added
        $this->assertSame($value, $action->getAttribute($key));
    }

    /**
     * This test checks whether the getDefaultMethod() returns
     * the expected value.
     *
     * @return void
     */
    public function testGetDefaultAction()
    {
        $this->assertSame(BaseAction::DEFAULT_METHOD_NAME, $this->action->getDefaultMethod());
    }

    /**
     * Check that the method findResult() didn't return a value if the requested one
     * is not available.
     *
     * @return void
     */
    public function testFindResultWithoutResult()
    {
        $this->assertNull($this->action->findResult('test'));
    }

    /**
     * Tests the addResult() and findResult() method.
     *
     * @return void
     */
    public function testAddAndFindResult()
    {

        // create a mock result
        $mockResult = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Results\ResultInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the method
        $mockResult
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        // add the result
        $this->action->addResult($mockResult);

        // check the result
        $this->assertSame($mockResult, $this->action->findResult('test'));
    }

    /**
     * Tests that the addFieldError() works as expected.
     *
     * @return void
     */
    public function testAddFieldErrorHasAndGetErrors()
    {

        // add an error message
        $this->action->addFieldError($key ='test', $value = 'A Message');

        // check that the errors is available
        $this->assertTrue($this->action->hasErrors());
        $this->assertSame(array($key => $value), $this->action->getErrors());
    }
}
