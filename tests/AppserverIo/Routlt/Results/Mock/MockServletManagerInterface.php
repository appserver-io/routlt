<?php

/**
 * AppserverIo\Routlt\Results\Mock\MockServletManagerInterface
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

namespace AppserverIo\Routlt\Results\Mock;

use AppserverIo\Psr\Servlet\ServletContextInterface;

/**
 * This a helper interface, because the ServletManager implementation is a \Stackable
 * what makes method mocking impossible.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface MockServletManagerInterface extends ServletContextInterface
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
}