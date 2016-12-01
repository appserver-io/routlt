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
use AppserverIo\Routlt\Util\ActionAware;
use AppserverIo\Routlt\Util\ValidationAware;
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
 * @deprecated Since 2.0.0-alpha5, use AppserverIo\Routlt\Results\RawResult instead
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
     * Trait proving basic result functionality.
     *
     * @var \AppserverIo\Routlt\Results\ResultTrait
     */
    use ResultTrait;

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
