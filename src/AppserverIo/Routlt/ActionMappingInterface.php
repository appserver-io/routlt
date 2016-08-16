<?php

/**
 * AppserverIo\Routlt\ActionMappingInterface
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

namespace AppserverIo\Routlt;

/**
 * Interface for all route tokenizer implementations.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface ActionMappingInterface
{

    /**
     * Compiles the passed route expression into a valid regex.
     *
     * @param string $expression   The expression to use
     * @param array  $requirements The requirements for the expression
     * @param array  $defaults     The default values for the found variables
     *
     * @return void
     */
    public function compile($expression, array $requirements = array(), array $defaults = array());

    /**
     * Return's the regex build from the expression.
     *
     * @return string The regex
     */
    public function getCompiledRegex();

    /**
     * Tokenizes the passed route by using the tokenizers expression.
     *
     * @param string $route The route to be parsed
     *
     * @return boolean TRUE if the passed route matches the expression, else FALSE
     */
    public function match($route);

    /**
     * Return's the controller name found in the route.
     *
     * @return string The controller name
     */
    public function getControllerName();

    /**
     * Return's the method name found in the route.
     *
     * @return string The method name
     */
    public function getMethodName();

    /**
     * Return's the extracted request parameters found in the route.
     *
     * @return array The extracted request parameters
     */
    public function getRequestParameters();
}
