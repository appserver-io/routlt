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

use AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface;

/**
 * Interceptor that set's all request parameters to the action by using setter methods.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ParamsInterceptor implements InterceptorInterface
{

    /**
     * Method that implements the interceptors functionality.
     *
     * @param AppserverIo\Psr\MetaobjectProtocol\Aop\MethodInvocationInterface $methodInvocation Initially invoked method
     *
     * @return void
     */
    public function intercept(MethodInvocationInterface $methodInvocation)
    {

        try {
            // load the action instance
            $action = $methodInvocation->getContext();

            // load the actions methods
            $methods = get_class_methods($action);

            // get the servlet request
            $parameters = $methodInvocation->getParameters();
            $servletRequest = $parameters['servletRequest'];

            // try to inject the request parameters by using the class setters
            foreach ($servletRequest->getParameterMap() as $key => $value) {
                // prepare the setter method name
                $methodName = sprintf('set%s', ucfirst($key));

                // query whether the class has the setter implemented
                if (in_array($methodName, $methods) === false) {
                    continue;
                }

                // set the value by using the setter
                $action->$methodName($value);
            }

        } catch (\Exception $e) {

        }
    }
}
