<?php

/**
 * AppserverIo\Routlt\PhtmlServlet
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

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * A simple implementation of a servlet that executes PHTML files that has
 * to be specified as servlet path.
 *
 * The PHTML files are processed in the scope of the service() method. This
 * gives developers access to all variables in the method's scope as to all
 * members of the servlet.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class PhtmlServlet extends HttpServlet
{

    /**
     * The string for the X-POWERED-BY header.
     *
     * @var string
     */
    protected $poweredBy;

    /**
     * The path to the actual web application -> the base directory for PHTML templates.
     *
     * @var string
     */
    protected $webappPath;

    /**
     * Returns the string for the X-POWERED-BY header.
     *
     * @return string The X-POWERED-BY header
     */
    public function getPoweredBy()
    {
        return $this->poweredBy;
    }

    /**
     * Returns the path to the web application.
     *
     * @return string The path to the web application
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $servletConfig The configuration to initialize the servlet with
     *
     * @return void
     */
    public function init(ServletConfigInterface $servletConfig)
    {

        // pre-initialize the X-POWERED-BY header
        $this->poweredBy = get_class($this);

        // pre-initialize the path to the web application
        $this->webappPath = $servletConfig->getWebappPath();
    }

    /**
     * Delegates to HTTP method specific functions like doPost() for POST e.g.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response sent back to the client
     *
     * @return void
     *
     * @throws \AppserverIo\Psr\Servlet\ServletException If no action has been found for the requested path
     */
    public function service(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse)
    {

        // pre-initialize the X-POWERED-BY header
        $poweredBy = $this->getPoweredBy();

        // append an existing X-POWERED-BY header if available
        if ($servletRequest->hasHeader(HttpProtocol::HEADER_X_POWERED_BY)) {
            $poweredBy .= ', ' . $servletRequest->getHeader(HttpProtocol::HEADER_X_POWERED_BY);
        }

        // set the X-POWERED-BY header
        $servletResponse->addHeader(HttpProtocol::HEADER_X_POWERED_BY, $poweredBy);

        // servlet path === relative path to the template name
        $template = $servletRequest->getServletPath();

        // check if the template is available
        if (file_exists($pathToTemplate = $this->getWebappPath() . $template) === false) {
            throw new ServletException(sprintf('Requested template \'%s\' is not available', $template));
        }

        // process the template
        ob_start();
        require $pathToTemplate;

        // add the servlet name to the response
        $servletResponse->appendBodyStream(ob_get_clean());
    }
}
