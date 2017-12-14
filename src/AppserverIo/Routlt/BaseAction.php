<?php

/**
 * AppserverIo\Routlt\BaseAction
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

use AppserverIo\Lang\Object;
use AppserverIo\Routlt\Results\ResultInterface;
use AppserverIo\Routlt\Util\ValidationAware;
use AppserverIo\Routlt\Util\DescriptorAware;
use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Psr\Deployment\DescriptorInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This class is the abstract base class for all Actions.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
abstract class BaseAction extends Object implements ActionInterface, ValidationAware, ServletContextAware, DescriptorAware
{

    /**
     * Holds the name of the default method to invoke if the parameter with the method name to invoke is not specified.
     *
     * @var string
     */
    const DEFAULT_METHOD_NAME = 'perform';

    /**
     * The context for the actual request.
     *
     * @var \AppserverIo\Psr\Servlet\ServletContextInterface
     */
    protected $servletContext = null;

    /**
     * The array with the action errors.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * The array with the action results.
     *
     * @var array
     */
    protected $results = array();

    /**
     * The descriptor instance.
     *
     * @var \AppserverIo\Psr\Deployment\DescriptorInterface
     */
    protected $descriptor;

    /**
     * Sets the actual servlet context instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContextInterface $servletContext The servlet context instance
     *
     * @return void
     */
    public function setServletContext(ServletContextInterface $servletContext)
    {
        $this->servletContext = $servletContext;
    }

    /**
     * Returns the servlet context instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContextInterface The servlet context instance
     */
    public function getServletContext()
    {
        return $this->servletContext;
    }

    /**
     * Method that will be invoked before we dispatch the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @see \AppserverIo\Routlt\ActionInterface::preDispatch()
     */
    public function preDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        return;
    }

    /**
     * Method that will be invoked after we've dispatched the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @see \AppserverIo\Routlt\ActionInterface::postDispatch()
     */
    public function postDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        return;
    }

    /**
     * This method returns the default action method name that has to be invoked .
     *
     * @return string The default action method name that has to be invoked
     */
    public function getDefaultMethod()
    {
        return BaseAction::DEFAULT_METHOD_NAME;
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
        $this->getServletContext()->setAttribute($key, $value);
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
        return $this->getServletContext()->getAttribute($key);
    }

    /**
     * Adds the result to the action.
     *
     * @param \AppserverIo\Routlt\Results\ResultInterface $result The result that has to be added
     *
     * @return void
     * @see \AppserverIo\Routlt\ActionInterface::addResult()
     */
    public function addResult(ResultInterface $result)
    {
        $this->results[$result->getName()] = $result;
    }

    /**
     * Tries to find and return the result with the passed name.
     *
     * @param string $name The name of the result to return
     *
     * @return \AppserverIo\Routlt\Results\ResultInterface|null The requested result
     * @see \AppserverIo\Routlt\ActionInterface::findResult()
     */
    public function findResult($name)
    {
        if (isset($this->results[$name])) {
            return $this->results[$name];
        }
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
     * Sets the descriptor instance.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $descriptor The descriptor instance
     *
     * @return void
     */
    public function setDescriptor(DescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * Returns the descriptor instance.
     *
     * @return \AppserverIo\Psr\Deployment\DescriptorInterface The descriptor instance
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }
}
