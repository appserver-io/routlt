<?php

/**
 * AppserverIo\Routlt\Results\RedirectResult
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

use AppserverIo\Routlt\Annotations\Result;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * Result implementation redirects to the page defined as result.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 *
 * @Result(shared=false)
 */
class RedirectResult extends AbstractResult
{

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

        // initialize the location we want to redirect to
        $result = $this->getResult();

        // add the base modifier, if necessary
        if ($baseModifier = $servletRequest->getBaseModifier()) {
            $result = $baseModifier . $result;
        }

        // stop processing the request and redirect to the URL
        $servletRequest->setDispatched(true);
        $servletResponse->setStatusCode($this->getCode());
        $servletResponse->addHeader(Protocol::HEADER_LOCATION, $result);
    }
}
