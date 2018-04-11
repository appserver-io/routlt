<?php

/**
 * AppserverIo\Routlt\Mock\MockServletContextInterface
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

namespace AppserverIo\Routlt\Mock;

use AppserverIo\Psr\Deployment\DescriptorInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;

/**
 * Mock interface for testing purposes only.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface MockServletContextInterface extends ServletContextInterface
{

    /**
     * Runs a lookup for the servlet with the passed class name and
     * session ID.
     *
     * @param string $servletPath The servlet path
     * @param string $sessionId   The session ID
     * @param array  $args        The arguments passed to the servlet constructor
     *
     * @return \AppserverIo\Psr\Servlet\GenericServlet The requested servlet
     */
    public function lookup($servletPath, $sessionId = null, array $args = array());

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication();

    /**
     * Registers the passed EPB reference in the applications directory.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $epbReference The EPB reference to register
     *
     * @return void
     */
    public function registerReferences(DescriptorInterface $descriptor);

    /**
     * Registers the value with the passed key in the container.
     *
     * @param string $key   The key to register the value with
     * @param object $value The value to register
     *
     * @return void
     */
    public function setAttribute($key, $value);

    /**
     * Returns the attribute with the passed key from the container.
     *
     * @param string $key The key the requested value is registered with
     *
     * @return mixed|null The requested value if available
     */
    public function getAttribute($key);
}
