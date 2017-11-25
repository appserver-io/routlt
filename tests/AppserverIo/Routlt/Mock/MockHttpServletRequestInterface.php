<?php

/**
 * AppserverIo\Routlt\Mock\MockHttpServletRequestInterface
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

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * Mock interface for testing purposes only.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface MockHttpServletRequestInterface extends HttpServletRequestInterface
{

    /**
     * Prepares the request instance.
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the request can't be prepared, because no file handle exists
     */
    public function prepare();

    /**
     * Sets the query string of the actual request.
     *
     * @param string $queryString The query string
     *
     * @return void
     */
    public function setQueryString($queryString);

    /**
     * Sets the part of this request's URL from the protocol name up to the query string in the first line of the HTTP request.
     *
     * @param string $requestUri The request URI
     *
     * @return void
     */
    public function setRequestUri($requestUri);

    /**
     * Adds the attribute with the passed name to this context.
     *
     * @param string $key   The key to add the value with
     * @param mixed  $value The value to add to the context
     *
     * @return void
     */
    public function setAttribute($key, $value);

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key);

    /**
     * Returns the context that allows access to session and
     * server information.
     *
     * @return \AppserverIo\Psr\Context\ContextInterface The request context
     */
    public function getContext();
}
