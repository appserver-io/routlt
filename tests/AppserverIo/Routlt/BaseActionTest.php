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
        $this->action = $this->getMockForAbstractClass('AppserverIo\Routlt\BaseAction', array($this->getMock('AppserverIo\Psr\Context\ContextInterface')));
    }

    /**
     * This test checks the resolved class name.
     *
     * @return void
     */
    public function testGetConstructorAndGetContext()
    {
        $action = $this->getMockForAbstractClass('AppserverIo\Routlt\BaseAction', array($context = $this->getMock('AppserverIo\Psr\Context\ContextInterface')));
        $this->assertSame($context, $action->getContext());
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

        // initialize a context
        $context = $this->getMock('AppserverIo\Psr\Context\ArrayContext');
        $context
            ->expects($this->once())
            ->method('setAttribute')
            ->with($key = 'testKey', $value = 'testValue');
        $context
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($value));

        // initialize the action
        $action = $this->getMockForAbstractClass('AppserverIo\Routlt\BaseAction', array($context));

        // add a value to the context
        $action->setAttribute($key, $value);

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
}
