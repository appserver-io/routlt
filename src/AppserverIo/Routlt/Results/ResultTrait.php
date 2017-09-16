<?php

/**
 * AppserverIo\Routlt\Results\ResultTrait
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
use AppserverIo\Routlt\Description\ResultDescriptorInterface;

/**
 * Trait providing basic result functionality.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
trait ResultTrait
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
     * The HTTP response code that has to be send.
     *
     * @var string
     */
    protected $code = 200;

    /**
     * Initializes the instance with the configured result value.
     *
     * @param \AppserverIo\Routlt\Description\\ResultDescriptorInterface $resultDescriptor The result descriptor instance
     */
    public function __construct(ResultDescriptorInterface $resultDescriptor)
    {
        $this->name = $resultDescriptor->getName();
        $this->type = $resultDescriptor->getType();
        $this->code = $resultDescriptor->getCode();
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
}
