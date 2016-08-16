<?php

/**
 * AppserverIo\Routlt\ActionMapping
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
 * Tokenize implementation using a regex to parse a route.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ActionMapping implements ActionMappingInterface
{

    /**
     * The template for to create the compiled regex from.
     *
     * @var string
     */
    protected $regexTemplate = '/^%s$/';

    /**
     * The path with the controller/method name.
     *
     * @var string
     */
    protected $path;

    /**
     * The uncompiled expression.
     *
     * @var string
     */
    protected $expression;

    /**
     * The compiled regex, used to extract the variables from the route.
     *
     * @var string
     */
    protected $compiledRegex;

    /**
     * The controller name, extracted from the route.
     *
     * @var string
     */
    protected $controllerName;

    /**
     * The method name, extracted from the route.
     *
     * @var string
     */
    protected $methodName;

   /**
    * The array containing the declared placeholders.
    *
    * @var array
    */
    protected $vars = array();

    /**
     * The array with the default values for the placeholders.
     *
     * @var array
     */
    protected $defaults = array();

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

        // intialize the expression
        $this->expression = $expression;
        $this->defaults = $defaults;

        // extract the placeholders from the expression
        $matches = array();
        preg_match_all('/:\w+/', $this->expression, $matches);
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
        $toCompile = $this->expression;

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

        // intialize the path name
        $this->path = sprintf('%s%s', $this->controllerName, $this->methodName);
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
        $result = preg_match($this->getCompiledRegex(), $this->applyDefaults($route), $matches);

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
     * Return's the path with the controller/method name.
     *
     * @return string The path with the controller/method name
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * If necessary, this method applies the default values to the passed route.
     *
     * @param string $route The route to apply the default values to
     *
     * @return string The route with the default values applied
     */
    protected function applyDefaults($route)
    {

        // query whether or not the route matches
        if (preg_match(sprintf('/%s/', addcslashes($this->path, '/')), $route)) {
            // initialize the array for the appended variables
            $vars = array();

            // try to extract the appended variables
            $varString = ltrim(str_replace($this->path, '', $route), '/');
            if (empty($varString) === false) {
                $vars = explode('/', $varString);
            }

            // iterate over all variables
            foreach ($this->vars as $key => $var) {
                // query whether or not, we've a default value and the variable has NOT been set
                if (isset($this->defaults[$var]) && !isset($vars[$key])) {
                    $vars[$key] = $this->defaults[$var];
                }
            }

            // if we've found vars, append them to the path
            if (sizeof($vars) > 0) {
                $route = sprintf('%s/%s', $this->path, implode('/', $vars));
            }

        }

        // return the route with the defaults applied
        return $route;
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
     * Initializes the action mapping with the passed controller name.
     *
     * @param string $controllerName The controller name found in the route
     *
     * @return void
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
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
     * Initializes the action mapping with the passed method name.
     *
     * @param string $methodName The method name found in the route
     *
     * @return void
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * Return's the method name found in the route.
     *
     * @return string The method name
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
