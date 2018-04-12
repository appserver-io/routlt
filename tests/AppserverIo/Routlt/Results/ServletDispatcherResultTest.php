<?php

/**
 * AppserverIo\Routlt\Results\ServletDispatcherResultTest
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

use AppserverIo\Routlt\ActionInterface;

/**
 * Test implementation for the ServletDispatcherResult implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ServletDispatcherResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The result instance to test.
     *
     * @var \AppserverIo\Routlt\Results\ServletDispatcherResult
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
        $this->result = new ServletDispatcherResult();
    }

    /**
     * Tests the constructor intialization.
     *
     * @return void
     */
    public function testInit()
    {

        // create a mock result descriptor
        $mockResultConfigurationDescriptor = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Description\ResultConfigurationDescriptorInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the methods
        $mockResultConfigurationDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $mockResultConfigurationDescriptor->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('AppserverIo\Routlt\Results\ServletDispatcherResult'));
        $mockResultConfigurationDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue('/path/to/my_template.dhtml/index/index?test=test'));

        // invoke the init method
        $this->result->init($mockResultConfigurationDescriptor);

        // make some assertions
        $this->assertSame(ActionInterface::SUCCESS, $this->result->getName());
        $this->assertSame('AppserverIo\Routlt\Results\ServletDispatcherResult', $this->result->getType());
        $this->assertSame('/path/to/my_template.dhtml/index/index?test=test', $this->result->getResult());
    }

    /**
     * Tests the result process() method.
     *
     * @return void
     */
    public function testProcess()
    {

        // create a mock result descriptor
        $mockResultConfigurationDescriptor = $this->getMockBuilder($interface = 'AppserverIo\Routlt\Description\ResultConfigurationDescriptorInterface')
            ->setMethods(get_class_methods($interface))
            ->getMock();

        // mock the methods
        $mockResultConfigurationDescriptor->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(ActionInterface::SUCCESS));
        $mockResultConfigurationDescriptor->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('AppserverIo\Routlt\Results\ServletDispatcherResult'));
        $mockResultConfigurationDescriptor->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue('/path/to/my_template.dhtml/index/index?test=test'));

        // invoke the init method
        $this->result->init($mockResultConfigurationDescriptor);

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder($requestInterface = 'AppserverIo\Routlt\Mock\MockHttpServletRequestInterface')
                                   ->setMethods(get_class_methods($requestInterface))
                                   ->getMock();

        // mock the necessary request methods
        $mockServletRequest->expects($this->once())
            ->method('getProposedSessionId')
            ->will($this->returnValue($sessionId = md5(time())));
        $mockServletRequest->expects($this->once())
            ->method('getServletPath')
            ->will($this->returnValue('/path/to/my_template.dhtml'));
        $mockServletRequest->expects($this->once())
            ->method('setRequestUri')
            ->with('/path/to/my_template.dhtml/index/index');
        $mockServletRequest->expects($this->once())
            ->method('setQueryString')
            ->with('test=test');
        $mockServletRequest->expects($this->once())
            ->method('prepare');

        // create a mock servlet response instance
        $mockServletResponse = $this->getMockBuilder($responseInterface = 'AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface')
                                    ->setMethods(get_class_methods($responseInterface))
                                    ->getMock();

        // create a mock servlet instance
        $mockServlet = $this->getMockBuilder($servletInterface = 'AppserverIo\Psr\Servlet\ServletInterface')
                            ->setMethods(get_class_methods($servletInterface))
                            ->getMock();

        // mock the necessary servlet method
        $mockServlet->expects($this->once())
                    ->method('service')
                    ->with($mockServletRequest, $mockServletResponse);

        // create a mock instance of the servlet context instance
        $mockServletContext = $this->getMockBuilder($servletContextInterface = 'AppserverIo\Routlt\Mock\MockServletContextInterface')
                                   ->setMethods(get_class_methods($servletContextInterface))
                                   ->getMock();

        // mock the necessary servlet context method
        $mockServletContext->expects($this->once())
                           ->method('lookup')
                           ->with('/path/to/my_template.dhtml', $sessionId)
                           ->will($this->returnValue($mockServlet));

        // set the servlet context instance
        $this->result->setServletContext($mockServletContext);

        // process the result
        $this->result->process($mockServletRequest, $mockServletResponse);
    }
}
