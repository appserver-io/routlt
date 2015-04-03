<?php

/**
 * AppserverIo\Routlt\Description\ParamsInterceptor
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

namespace AppserverIo\Routlt\Interceptors;

use AppserverIo\Routlt\ActionInterface;
use AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface;

/**
 * Abstract interceptor implementation providing basic interceptor methods.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
abstract class AbstractInterceptor implements InterceptorInterface
{

    /**
     * The key of the servlet request in the method invocation parameters.
     *
     * @var string
     */
    const SERVLET_REQUEST = 'servletRequest';

    /**
     * The key of the servlet response in the method invocation parameters.
     *
     * @var string
     */
    const SERVLET_RESPONSE = 'servletResponse';

    /**
     * The intercepted action instance.
     *
     * @var \AppserverIo\Routlt\ActionInterface
     */
    protected $action;

    /**
     * Sets the actual action instance.
     *
     * @param  \AppserverIo\Routlt\ActionInterface $action The actual action instance
     *
     * @return void
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * Returns the actual action instance.
     *
     * @return \AppserverIo\Routlt\ActionInterface The actual action instance
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * The parameters of the actual method invocation.
     *
     * @var \AppserverIo\Psr\Servlet\ServletRequestInterface
     */
    protected $parameters = array();

    /**
     * Sets the method invocation parameters.
     *
     * @param array $parameters The method invocation parameters
     *
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the requsted method invocation parameter.
     *
     * @param string $name Name of the parameter to return
     *
     * @return mixed|null The requested parameter if available
     */
    public function getParameter($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
    }

    /**
     * Returns the instance of the actual servlet request.
     *
     * @return \AppserverIo\Psr\Servlet\ServletRequestInterface|null The actual servlet request instance
     */
    public function getServletRequest()
    {
        return $this->getParameter(AbstractInterceptor::SERVLET_REQUEST);
    }

    /**
     * Returns the instance of the actual servlet response.
     *
     * @return \AppserverIo\Psr\Servlet\ServletResonseInterface|null The actual servlet response instance
     */
    public function getServletResponse()
    {
        return $this->getParameter(AbstractInterceptor::SERVLET_RESPONSE);
    }

    /**
     * Returns the public methods of the actual action instance.
     *
     * @return array|null The public methods
     */
    public function getActionMethods()
    {
        if (($action = $this->getAction()) != null) {
            return get_class_methods($action);
        }
    }

    /**
     * Executes the custom interceptor functionality.
     *
     * @param AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface $methodInvocation Initially invoked method
     *
     * @return mixed The interceptors return value
     */
    abstract protected function execute(MethodInvocationInterface $methodInvocation);

    /**
     * Method that implements the interceptors functionality.
     *
     * @param AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface $methodInvocation Initially invoked method
     *
     * @return string|null The action result
     */
    public function intercept(MethodInvocationInterface $methodInvocation)
    {

        try {
            // load the action instance
            $this->setAction($methodInvocation->getContext());

            // load the method invocation parameters
            $this->setParameters($methodInvocation->getParameters());

            // execute the custom interceptor functionality
            return $this->execute($methodInvocation);

        } catch (\Exception $e) {
            // add the catched exception
            $this->getAction()->addFieldError('unknown', $e->getMessage());

            // return the key for a failed action invocation
            return ActionInterface::FAILURE;
        }
    }
}
