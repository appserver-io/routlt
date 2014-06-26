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
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt;

use AppserverIo\Routlt\Mock\MockDispatchAction;

/**
 * This is test implementation for the dispatch action implementation.
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
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
        $this->action = $this->getMockForAbstractClass('AppserverIo\Routlt\DispatchAction', array($this->getMock('TechDivision\Context\Context')));
    }

    /**
     * This tests the perform() method with the requested action method not implemented.
     *
     * @expectedException AppserverIo\Routlt\MethodNotFoundException
     * @return void
     */
    public function testPerformWithMethodNotFoundException()
    {

        // create a mock servlet request instance
        $servletRequest = $this->getMock('TechDivision\Servlet\Http\HttpServletRequest');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test'));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('TechDivision\Servlet\Http\HttpServletResponse');

        // invoke the method we want to test
        $this->action->perform($servletRequest, $servletResponse);
    }

    /**
     * This tests the perform() method with dummy action implementation.
     *
     * @return void
     */
    public function testPerform()
    {

        // create a new mock action implementation
        $action = $this->getMock(
            'AppserverIo\Routlt\Mock\MockDispatchAction',
            array('indexAction'),
            array($this->getMock('TechDivision\Context\Context'))
        );
        $action->expects($this->once())
            ->method('indexAction');

        // create a mock servlet request + response instance
        $servletRequest = $this->getMock('TechDivision\Servlet\Http\HttpServletRequest');
        $servletResponse = $this->getMock('TechDivision\Servlet\Http\HttpServletResponse');

        // invoke the method we want to test
        $action->perform($servletRequest, $servletResponse);
    }

    /**
     * This tests the perform() method with dummy action implementation.
     *
     * @return void
     */
    public function testPerformWithCompletePathInfo()
    {

        // create a new mock action implementation
        $action = $this->getMock(
            'AppserverIo\Routlt\Mock\MockDispatchAction',
            array('testAction'),
            array($this->getMock('TechDivision\Context\Context'))
        );
        $action->expects($this->once())
            ->method('testAction');

        // create a mock servlet request instance
        $servletRequest = $this->getMock('TechDivision\Servlet\Http\HttpServletRequest');
        $servletRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test/test'));

        // create a mock servlet response instance
        $servletResponse = $this->getMock('TechDivision\Servlet\Http\HttpServletResponse');

        // invoke the method we want to test
        $action->perform($servletRequest, $servletResponse);
    }
}
