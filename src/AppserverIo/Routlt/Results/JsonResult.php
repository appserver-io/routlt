<?php

/**
 * AppserverIo\Routlt\Results\JsonResult
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
use AppserverIo\Routlt\Util\ValidationAware;
use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Routlt\Description\ResultDescriptorInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * Result implementation that JSON encodes the response body.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class JsonResult implements ResultInterface, ActionAware
{

    /**
     * The key for the data that has to be JSON encoded.
     *
     * @var string
     */
    const DATA = 'json-result.data';

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
     * @param string $result The result to be handled
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

        // query whether the action contains errors or not
        if ($action instanceof ValidationAware && $action->hasErrors()) {
            $content = $action->getErrors();
        } else {
            $content = $servletRequest->getAttribute(JsonResult::DATA);
        }

        // add the header for the JSON content type
        $servletResponse->addHeader(HttpProtocol::HEADER_CONTENT_TYPE, 'application/json');

        // append the JSON encoded content to the servlet response
        $servletResponse->appendBodyStream(json_encode($content));
    }
}
