<?php

/**
 * AppserverIo\Routlt\ActionInterface
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

use AppserverIo\Routlt\Results\ResultInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This is the interface for all actions.
 *
 * Every action in a project has to implement this interface.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface ActionInterface
{

    /**
     * Default constant for the default input template.
     *
     * @var string
     */
    const INPUT = 'input';

    /**
     * Default constant for a successfull action invocation.
     *
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * Default constant for a failed action invocation.
     *
     * @var string
     */
    const FAILURE = 'failure';

    /**
     * Adds the result to the action.
     *
     * @param \AppserverIo\Routlt\Results\ResultInterface $result The result that has to be added
     *
     * @return void
     * @see \AppserverIo\Routlt\ActionInterface::addResult()
     */
    public function addResult(ResultInterface $result);

    /**
     * Tries to find and return the result with the passed name.
     *
     * @param string $name The name of the result to return
     *
     * @return \AppserverIo\Routlt\Results\ResultInterface|null The requested result
     */
    public function findResult($name);

    /**
     * This method returns the default action method name that has to be invoked .
     *
     * @return string The default action method name that has to be invoked
     */
    public function getDefaultMethod();

    /**
     * Method that will be invoked before we dispatch the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function preDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse);

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
    public function perform(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse);

    /**
     * Method that will be invoked after we've dispatched the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function postDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse);
}
