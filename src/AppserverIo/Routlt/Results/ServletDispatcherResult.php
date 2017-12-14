<?php

/**
 * AppserverIo\Routlt\Results\ServletDispatcherResult
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

use AppserverIo\Routlt\Util\ServletContextAware;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * Result implementation that dispatches another servlet.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ServletDispatcherResult extends AbstractResult implements ServletContextAware
{

    /**
     * The servlet context instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContextInterface
     */
    protected $servletContext;

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
     * Processes an action result by dispatching the configured servlet.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response sent back to the client
     *
     * @return void
     */
    public function process(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse)
    {

        // initialize the variables for the path and the query string
        $path = null;
        $query = null;

        // load result and session-ID
        extract(parse_url($this->getResult()));

        // initialize the request URI
        if (!empty($path)) {
            $servletRequest->setRequestUri($path);
        }

        // initialize the query string
        if (!empty($query)) {
            $servletRequest->setQueryString($query);
        }

        // prepare the request with the new data
        $servletRequest->prepare();

        // load the servlet path and session-ID
        $servletPath = $servletRequest->getServletPath();
        $sessionId = $servletRequest->getProposedSessionId();

        // load and process the servlet
        $servlet = $this->getServletContext()->lookup($servletPath, $sessionId);
        $servlet->service($servletRequest, $servletResponse);
    }
}
