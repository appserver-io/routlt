<?php

/**
 * AppserverIo\Routlt\Results\ResultInterface
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

use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * The interface for all result implementations.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
interface ResultInterface
{

    /**
     * Returns the result name.
     *
     * @return string The result name
     */
    public function getName();

    /**
     * Returns the action result type.
     *
     * @return string The action result type
     */
    public function getType();

    /**
     * Returns the action result value.
     *
     * @return string The action result value
     */
    public function getResult();

    /**
     * Initializes the result from the result descriptor instance.
     *
     * @return void
     */
    public function init();

    /**
     * Processes an action result by dispatching the configured servlet.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response sent back to the client
     *
     * @return void
     */
    public function process(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse);
}
