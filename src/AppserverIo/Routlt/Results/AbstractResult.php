<?php

/**
 * AppserverIo\Routlt\Results\AbstractResult
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

use AppserverIo\Routlt\ActionInterface;
use AppserverIo\Routlt\Util\ActionAware;
use AppserverIo\Routlt\Util\DescriptorAware;
use AppserverIo\Routlt\Description\ResultConfigurationDescriptorInterface;
use AppserverIo\Psr\Deployment\DescriptorInterface;

/**
 * Abstract result implementation that provides basic result functionality.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
abstract class AbstractResult implements ResultInterface, ActionAware, DescriptorAware
{

    /**
     * The descriptor instance.
     *
     * @var  \AppserverIo\Routlt\Description\ResultDescriptorInterface
     */
    protected $descriptor;

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
     * The HTTP response code that has to be send.
     *
     * @var string
     */
    protected $code = 200;

    /**
     * Initializes the result from the result configuration descriptor instance.
     *
     * @param \AppserverIo\Routlt\Description\ResultConfigurationDescriptorInterface $resultConfigurationDescriptor The result configuration descriptor
     *
     * @return void
     */
    public function init(ResultConfigurationDescriptorInterface $resultConfigurationDescriptor)
    {

        // initialize the properites
        $this->name = $resultConfigurationDescriptor->getName();
        $this->type = $resultConfigurationDescriptor->getType();
        $this->code = $resultConfigurationDescriptor->getCode();
        $this->result = $resultConfigurationDescriptor->getResult();
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
     * Returns the HTTP response code that has to be send.
     *
     * @return integer The HTTP response code
     */
    public function getCode()
    {
        return $this->code;
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
