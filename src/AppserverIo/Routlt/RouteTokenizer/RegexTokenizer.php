<?php

/**
 * AppserverIo\Routlt\RouteTokenizer\RegexTokenizer
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
 * Tokenize implementation using a regex to parse a route.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class RegexTokenizer implements TokenizerInterface
{

    protected $regexTemplate = '/^%s$/';

    protected $regex = null;

    protected $controllerName = null;

    protected $methodName = null;

    protected $requestParameters = array();

    /**
     * Initializes the tokenizer with an expression
     * used to tokenize the route.
     *
     * @param string $expression   The expression to use
     * @param array  $requirements The requirements for the expression
     * @param array  $defaults     The default values for the found variables
     *
     * @return void
     */
    public function init($expression, array $requirements = array(), array $defaults = array())
    {

        $matches = array();

        preg_match_all('/:\w+/', $expression, $matches);

        $vars = reset($matches);

        array_walk($vars, function(&$match) { $match = ltrim($match, ':'); });

        $compiled = $expression;

        foreach ($vars as $var) {

            $sequence = '.*';

            if (isset($requirements[$var])) {
                $sequence = $requirements[$var];
            }

            $compiled = str_replace(sprintf(':%s', $var), sprintf('(?<%s>%s)', $var, $sequence), $compiled);
        }

        $length = strpos($expression, '/:');
        if ($length === false) {
            $length = strlen($expression);
        }

        $path = substr($expression, 0, $length);

        if ($pos = strrpos($path, '/')) {
            $this->controllerName = ltrim(substr($path, 0, $pos), '/');
            $this->methodName = ltrim(substr($path, $pos), '/');
        } else {
            $this->controllerName = ltrim(substr($path, 0), '/');
        }

        $this->vars = $vars;
        $this->regex = sprintf($this->regexTemplate, addcslashes($compiled, '/'));
    }

    /**
     * Return's the regex build from the expression.
     *
     * @return string The regex
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Tokenizes the passed route by using the tokenizers expression.
     *
     * @param string $route The route to be parsed
     *
     * @return void
     */
    public function tokenize($route)
    {

        $matches = array();

        preg_match($this->regex, $route, $matches);

        foreach ($this->vars as $var) {
            if (isset($matches[$var])) {
                $this->requestParameters[$var] = $matches[$var];
            }
        }
    }

    /**
     * Return's the controller name found in the route.
     *
     * @return string The controller name
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * Return's the method name found in the route.
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Return's the extracted request parameters found in the route.
     *
     * @return array The extracted request parameters
     */
    public function getRequestParameters()
    {
        return $this->requestParameters;
    }
}
