<?php

/**
 * AppserverIo\Routlt\DispatchActionTest
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

use AppserverIo\Routlt\Util\ContextKeys;

/**
 * This is test implementation for the dispatch action implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class DispatchActionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The dispatch action instance to test.
     *
     * @var \AppserverIo\Routlt\DispatchAction
     */
    protected $action;

    /**
     * Initializes the dispatch action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->action = $this->getMockForAbstractClass('AppserverIo\Routlt\DispatchAction');
    }

    /**
     * This tests the perform() method with dummy action implementation.
     *
     * @return void
     */
    public function testPerform()
    {

        // create a new mock action implementation
        $action = $this->getMock('AppserverIo\Routlt\Mock\MockDispatchAction', array('getServletContext'));

        // set the method name
        $action->setAttribute(ContextKeys::METHOD_NAME, 'indexAction');

        // create a mock servlet request
        $servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue($result = '/index/index'));

        // create a mock servlet response
        $servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface');
        $servletResponse->expects($this->once())
            ->method('appendBodyStream')
            ->with($result);

        // invoke the method we want to test
        $action->perform($servletRequest, $servletResponse);
    }

    /**
     * Test that the getServletRequest() method returns the expected
     * ServletRequest instance.
     *
     * @return void
     */
    public function testGetServletRequest()
    {

        // create and set the mock servlet request
        $this->action->setServletRequest($servletRequest = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface'));

        // query whether the instances are identically
        $this->assertSame($servletRequest, $this->action->getServletRequest());
    }

    /**
     * Test that the getServletResponse() method returns the expected
     * ServletResponse instance.
     *
     * @return void
     */
    public function testGetServletResponse()
    {

        // create and set the mock servlet response
        $this->action->setServletResponse($servletResponse = $this->getMock('AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface'));

        // query whether the instances are identically
        $this->assertSame($servletResponse, $this->action->getServletResponse());
    }

    /**
     * This test checks whether the getDefaultMethod() returns
     * the expected value.
     *
     * @return void
     */
    public function testGetDefaultAction()
    {
        $this->assertSame(sprintf('%sAction', DispatchAction::DEFAULT_METHOD_NAME), $this->action->getDefaultMethod());
    }
}
