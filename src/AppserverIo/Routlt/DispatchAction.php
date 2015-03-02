<?php

/**
 * AppserverIo\Routlt\DispatchAction
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
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This class implements the functionality to invoke a method on its subclass specified
 * by the HTTPServletRequest path info.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
abstract class DispatchAction extends BaseAction implements DispatchActionInterface
{

    /**
     * The default action method suffix.
     *
     * @var string
     */
    const ACTION_SUFFIX = 'Action';

    /**
     * Holds the name of the default method to invoke if the parameter with the method name to invoke is not specified.
     *
     * @var string
     */
    const DEFAULT_METHOD_NAME = 'index';

    /**
     * Implemented to comply witht the interface.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function perform(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // load the requested method name from the context
        $methodName = $this->getAttribute(ContextKeys::METHOD_NAME);

        // invoke the requested action method
        $this->$methodName($servletRequest, $servletResponse);
    }

    /**
     * This method returns the default method name we'll invoke if the path info doesn't contain
     * the method name, that'll be the second element, when we explode the path info with a slash.
     *
     * @return string The default action method name that has to be invoked
     */
    public function getDefaultMethod()
    {
        return $this->getActionSuffix(DispatchAction::DEFAULT_METHOD_NAME);
    }

    /**
     * This method returns the action suffix, or prepends the action suffix to the passed action
     * method name and returns it.
     *
     * @param string $requestedMethodName The action method name to append the suffix to
     *
     * @return string The action suffix or the action method name, prepended with the action suffix
     */
    protected function getActionSuffix($requestedMethodName)
    {
        return $requestedMethodName . DispatchAction::ACTION_SUFFIX;
    }
}
