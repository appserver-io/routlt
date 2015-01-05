<?php

/**
 * AppserverIo\Routlt\Util\ServletContextAware
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

namespace AppserverIo\Routlt\Util;

use AppserverIo\Psr\Servlet\ServletContext;

/**
 * Interface for a servlet context aware controller servlet
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface ServletContextAware
{

    /**
     * Sets the actual servlet context instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContext $servletContext The servlet context instance
     *
     * @return void
     */
    public function setServletContext(ServletContext $servletContext);

    /**
     * Returns the servlet context instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContext The servlet context instance
     */
    public function getServletContext();
}
