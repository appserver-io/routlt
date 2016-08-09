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

    /**
     * The template for to create the compiled regex from.
     *
     * @var string
     */
    protected $regexTemplate = '/^%s$/';

    /**
     * The compiled regex, used to extract the variables from the route.
     *
     * @var string
     */
    protected $compiledRegex = null;

    /**
     * The controller name, extracted from the route.
     *
     * @var string
     */
    protected $controllerName = null;

    /**
     * The method name, extracted from the route.
     *
     * @var string
     */
    protected $methodName = null;

    /**
     * The request parameters, extracted from the route.
     *
     * @var array
     */
    protected $requestParameters = array();

    /**
     * Compiles the passed route expression into a valid regex.
     *
     * @param string $expression   The expression to use
     * @param array  $requirements The requirements for the expression
     * @param array  $defaults     The default values for the found variables
     *
     * @return void
     */
    public function compile($expression, array $requirements = array(), array $defaults = array())
    {

        // extract the placeholders from the expression
        $matches = array();
        preg_match_all('/:\w+/', $expression, $matches);
        $vars = reset($matches);

        // remove the leading colon from the variable names
        array_walk(
            $vars,
            function(&$match) {
                $match = ltrim($match, ':');
            }
        );

        // initialize the member with the found variable names
        $this->vars = $vars;

        // initialize the string that has to be compiled
        $toCompile = $expression;

        // try to compile the regex group expression for each variable
        foreach ($this->vars as $var) {
            // by default the sequence to use is .*
            $sequence = '.*';
            // check if a sequence for the variable has been passed
            if (isset($requirements[$var])) {
                $sequence = $requirements[$var];
            }
            // replace the placeholder with the regex group expression
            $toCompile = str_replace(sprintf(':%s', $var), sprintf('(?<%s>%s)', $var, $sequence), $toCompile);
        }

        // initialize the member with the compiled regex
        $this->compiledRegex = sprintf($this->regexTemplate, addcslashes($toCompile, '/'));

        // try to find the position of the first variable
        $length = strpos($expression, '/:');
        if ($length === false) {
            $length = strlen($expression);
        }

        // separate controller/method name from the expression
        $path = substr($expression, 0, $length);

        // extract controller and method name by the last / found in the path
        if ($pos = strrpos($path, '/')) {
            $this->controllerName = substr($path, 1, $pos - 1);
            $this->methodName = substr($path, $pos + 1);
        } else {
            $this->controllerName = substr($path, 1);
        }
    }

    /**
     * Tokenizes the passed route by using the tokenizers expression.
     *
     * @param string $route The route to be parsed
     *
     * @return boolean TRUE if the passed route matches the expression, else FALSE
     */
    public function match($route)
    {

        // initialize the array for the variable matches
        $matches = array();

        // execute the regex and extract the variables
        $result = preg_match($this->getCompiledRegex(), $route, $matches);

        // append the variable values to the request parameters
        foreach ($this->vars as $var) {
            if (isset($matches[$var])) {
                $this->requestParameters[$var] = $matches[$var];
            }
        }

        // return TRUE if the compiled regex matched
        if ($result === 1) {
            return true;
        }

        // else we return FALSE
        return false;
    }

    /**
     * Return's the regex build from the expression.
     *
     * @return string The regex
     */
    public function getCompiledRegex()
    {
        return $this->compiledRegex;
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
