<?php

/**
 * AppserverIo\Routlt\RouteTokenizer\TokenizerInterface
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
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt\RouteTokenizer;

/**
 * Interface for all route tokenizer implementations.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface TokenizerInterface
{

    /**
     * Initializes the tokenizer with an expression
     * used to tokenize the route.
     *
     * @param string $expression The expression to use
     *
     * @return void
     */
    public function init($expression);

    /**
     * Tokenizes the passed route by using the tokenizers expression.
     *
     * @param string $route The route to be parsed
     */
    public function tokenize($route);

    /**
     * Return's the controller name found in the route.
     *
     * @return string The controller name
     */
    public function getControllerName();

    /**
     * Return's the method name found in the route.
     */
    public function getMethodName();

    /**
     * Return's the extracted request parameters found in the route.
     *
     * @return array The extracted request parameters
     */
    public function getRequestParameters();
}
