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
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace Rout\Lt\Controller;

use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

/**
 * This class implements the functionality to invoke a method on its subclass specified
 * by the HTTPServletRequest path info.
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
abstract class DispatchAction extends BaseAction
{

    /**
     * The default action method suffix.
     *
     * @var string
     */
    const ACTION_SUFFIX = 'Action';

    /**
     * The default action delimiter.
     *
     * @var string
     */
    const ACTION_DELIMITER = '/';

    /**
     * Holds the name of the default method to invoke if the paramter with the method name to invoke is not specified.
     *
     * @var string
     */
    const DEFAULT_METHOD_NAME = 'index';

    /**
     * This method implements the functionality to invoke a method implemented in its subclass.
     *
     * The method that should be invoked has to be specified by a HTTPServletRequest parameter
     * which name is specified in the configuration file as parameter for the ActionMapping.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function perform(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // the action delimiter we use to extract the action method name
        $actionDelimiter = $this->getActionDelimiter();

        // load the first part of the path info => that is the action name by default
        list (, $requestedMethodName) = explode($actionDelimiter, trim($servletRequest->getPathInfo(), $actionDelimiter));

        // try to set the default method, if one is specified in the path info
        if ($requestedMethodName == null) {
            $requestedMethodName = $this->getDefaultMethod();
        }

        // if yes, concatenate it to create a valid action name
        $requestedActionMethod = $this->getActionSuffix($requestedMethodName);

        // check if the requested action method is a class method
        if (in_array($requestedActionMethod, get_class_methods($this))) {
            $actionMethod = $requestedActionMethod;
        } else {
            throw new MethodNotFoundException(sprintf('Specified method %s not implemented by class %s', $requestedActionMethod, get_class($this)));
        }

        // invoke the requested action method
        $this->$actionMethod($servletRequest, $servletResponse);
    }

    /**
     * This method returns the default method name we'll invoke if the path info doesn't contain
     * the method name, that'll be the second element, when we explode the path info with a slash.
     *
     * @return string The default action method name that has to be invoked
     */
    protected function getDefaultMethod()
    {
        return DispatchAction::DEFAULT_METHOD_NAME;
    }

    /**
     * This method returns the default action delimtier we use to extract the action method name
     * we've to invoke from the path information.
     *
     * @return string The default action delimiter to extract the method name to be invoked
     */
    protected function getActionDelimiter()
    {
        return DispatchAction::ACTION_DELIMITER;
    }

    /**
     * This method returns the action suffix, or prepends the action suffix to the passed action
     * method name and returns it.
     *
     * @param string $requestedMethodName The action method name to append the suffix to
     *
     * @return string The action suffix or the action method name, prepended with the action suffix
     */
    protected function getActionSuffix($requestedMethodName = null)
    {
        if ($requestedMethodName == null) {
            return DispatchAction::ACTION_SUFFIX;
        }
        return $requestedMethodName . DispatchAction::ACTION_SUFFIX;
    }
}
