<?php

/**
 * AppserverIo\Routlt\Results\RawResult
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Routlt\Results;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Routlt\ActionInterface;
use AppserverIo\Routlt\Util\ActionAware;
use AppserverIo\Routlt\Util\EncodingAware;
use AppserverIo\Routlt\Util\ValidationAware;
use AppserverIo\Routlt\Util\DefaultHeadersAware;
use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Routlt\Description\ResultDescriptorInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * Result implementation that supports action based default headers and encoding.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 * @deprecated Since 2.0.0-alpha5, use AppserverIo\Routlt\Results\RawResult instead
 */
class RawResult implements ResultInterface, ActionAware
{

    /**
     * The action result name.
     *
     * @var string
     */
    protected $name;

    /**
     * The action result type.
     *
     * @var string
     */
    protected $type;

    /**
     * The action result value.
     *
     * @var array
     */
    protected $result;

    /**
     * Initializes the instance with the configured result value.
     *
     * @param \AppserverIo\Routlt\Results\ResultDescriptorInterface $resultDescriptor The result descriptor instance
     */
    public function __construct(ResultDescriptorInterface $resultDescriptor)
    {
        $this->name = $resultDescriptor->getName();
        $this->type = $resultDescriptor->getType();
        $this->result = $resultDescriptor->getResult();
    }

    /**
     * Returns the action result name.
     *
     * @return string The action result name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the action result type.
     *
     * @return string The action result type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the action result value.
     *
     * @return string The action result value
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Sets the actual action instance.
     *
     * @param \AppserverIo\Routlt\ActionInterface $action The action instance
     *
     * @return void
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * Returns the action instance.
     *
     * @return \AppserverIo\Routlt\ActionInterface The action instance
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Processes an action result by dispatching the configured servlet.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response sent back to the client
     *
     * @return void
     */
    public function process(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse)
    {

        // load the action instance
        $action = $this->getAction();

        // add the actions default headers to the response
        if ($action instanceof DefaultHeadersAware && $action->hasDefaultHeaders()) {
            foreach ($action->getDefaultHeaders() as $name => $value) {
                $servletResponse->addHeader($name, $value);
            }
        }

        // query whether the action contains errors or not
        if ($action instanceof ValidationAware && $action->hasErrors()) {
            $bodyContent = $action->getErrors();
        } else {
            $bodyContent = $action->getAttribute($this->getResult());
        }

        // query whether the action requires content encoding or not
        if ($action instanceof EncodingAware) {
            $bodyContent = $action->encode($bodyContent);
        }

        // set the encoded body content
        $servletResponse->appendBodyStream($bodyContent);
    }
}
