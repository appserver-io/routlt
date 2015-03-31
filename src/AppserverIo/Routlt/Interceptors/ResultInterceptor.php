<?php

/**
 * AppserverIo\Routlt\Description\ResultInterceptor
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
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ResultInterceptor implements InterceptorInterface
{

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

            error_log(__METHOD__ . '::' . __LINE__);

            // get the servlet request/response
            $parameters = $methodInvocation->getParameters();
            $servletRequest = $parameters['servletRequest'];
            $servletResponse = $parameters['servletResponse'];

            // load the action instance
            $action = $methodInvocation->getContext();

            // proceed invocation chain
            $result = $methodInvocation->proceed();

            // process the result if available
            if ($instance = $action->findResult($result)) {
                $instance->process($servletRequest, $servletResponse);
            }

            return $result;

        } catch (\Exception $e) {
            error_log($e);
        }
    }
}
