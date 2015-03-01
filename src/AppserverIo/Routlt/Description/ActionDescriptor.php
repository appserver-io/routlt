<?php

/**
 * AppserverIo\Routlt\Description\ActionDescriptor
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

namespace AppserverIo\Routlt\Description;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Routlt\Annotations\Action;
use AppserverIo\Lang\Reflection\MethodInterface;
use AppserverIo\Lang\Reflection\ReflectionAnnotation;

/**
 * Annotation to map a request path info to an action method.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ActionDescriptor implements ActionDescriptorInterface
{

    /**
     * The path info.
     *
     * @var string
     */
    protected $name;

    /**
     * The action method name.
     *
     * @var string
     */
    protected $methodName;

    /**
     * The request methods the action is listening to.
     *
     * @var array
     */
    protected $requestMethods;

    /**
     * Initializes the descriptor with the default
     * request methods the action is listening to.
     */
    public function __construct()
    {

        // initialize the request methods
        $this->requestMethods = array(
            HttpProtocol::METHOD_CONNECT,
            HttpProtocol::METHOD_DELETE,
            HttpProtocol::METHOD_GET,
            HttpProtocol::METHOD_HEAD,
            HttpProtocol::METHOD_OPTIONS,
            HttpProtocol::METHOD_POST,
            HttpProtocol::METHOD_PUT,
            HttpProtocol::METHOD_TRACE
        );
    }

    /**
     * Sets the action path info.
     *
     * @param string $name The action path info
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the path info.
     *
     * @return string The path info
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the action method name.
     *
     * @param string $methodName The action method name
     *
     * @return void
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * Returns the action method name.
     *
     * @return string The action method name
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Sets the request methods the action is listening to.
     *
     * @param array $requestMethods The request methods
     *
     * @return void
     */
    public function setRequestMethods(array $requestMethods)
    {
        $this->requestMethods = $requestMethods;
    }

    /**
     * Returns the request methods the action is listening to.
     *
     * @return array The request methods
     */
    public function getRequestMethods()
    {
        return $this->requestMethods;
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Routlt\Description\ActionDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new ActionDescriptor();
    }

    /**
     * Returns a new annotation instance for the passed reflection method.
     *
     * @param \AppserverIo\Lang\Reflection\MethodInterface $reflectionMethod The reflection method with the action configuration
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The reflection annotation
     */
    protected function newAnnotationInstance(MethodInterface $reflectionMethod)
    {
        return $reflectionMethod->getAnnotation(Action::ANNOTATION);
    }

    /**
     * Initializes the action configuration instance from the passed reflection method instance.
     *
     * @param \AppserverIo\Lang\Reflection\MethodInterface $reflectionMethod The reflection method with the action configuration
     *
     * @return \AppserverIo\Routlt\Description\ActionDescriptorInterface The initialized descriptor
     */
    public function fromReflectionMethod(MethodInterface $reflectionMethod)
    {

        // add the annotation alias to the reflection method
        $reflectionMethod->addAnnotationAlias(Action::ANNOTATION, Action::__getClass());

        // query if we've a method with a @Action annotation
        if ($reflectionMethod->hasAnnotation(Action::ANNOTATION) === false &&
            $reflectionMethod->getMethodName() !== 'perform') {
            // if not, do nothing
            return;

        // query whether we've the default perform() method WITHOUT an @Action annotation
        } elseif ($reflectionMethod->hasAnnotation(Action::ANNOTATION) === false &&
            $reflectionMethod->getMethodName() === 'perform') {
            // create an annotation instance manually
            $reflectionAnnotation = new ReflectionAnnotation(Action::__getClass());

        } else {
            // create a new annotation instance by default
            $reflectionAnnotation = $this->newAnnotationInstance($reflectionMethod);
        }

        // load method name
        $this->setMethodName($reflectionMethod->getMethodName());

        // initialize the annotation instance
        $annotationInstance = $reflectionAnnotation->newInstance(
            $reflectionAnnotation->getAnnotationName(),
            $reflectionAnnotation->getValues()
        );

        // load the default name to register in naming directory
        if ($nameAttribute = $annotationInstance->getName()) {
            $this->setName($nameAttribute);
        } else {
            // if @Annotation(name=****) is NOT set, we use the method name by default
            $this->setName('/' . lcfirst(str_replace('Action', '', $reflectionMethod->getMethodName())));
        }

        // initialize the array for the annotated request methods
        $annotatedRequestMethods = array();

        // parse the method for annotated request methods
        foreach ($this->getRequestMethods() as $requestMethod) {
            // prepare the annotation name, e. g. POST -> Post
            $annotationName = ucfirst(strtolower($requestMethod));

            // query whether the reflection method has been annotated
            if ($reflectionMethod->hasAnnotation($annotationName)) {
                array_push($annotatedRequestMethods, $requestMethod);
            }
        }

        // query whether at least one annotated request method has been found
        if (sizeof($annotatedRequestMethods) > 0) {
            // if yes, override the default request methods
            $this->setRequestMethods($annotatedRequestMethods);
        }

        // return the instance
        return $this;
    }

    /**
     * Initializes a action configuration instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the action configuration
     *
     * @return \AppserverIo\Routlt\Description\ActionDescriptorInterface The initialized descriptor
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Routlt\Description\ActionDescriptorInterface $actionDescriptor The configuration to merge
     *
     * @return void
     * @throws \AppserverIo\Routlt\Description\DescriptorException Is thrown if the passed descriptor has a different method name
     */
    public function merge(ActionDescriptorInterface $actionDescriptor)
    {

        // check if the classes are equal
        if ($this->getMethodName() !== $actionDescriptor->getMethodName()) {
            throw new DescriptorException(
                sprintf('You try to merge a action configuration for % with %s', $actionDescriptor->getMethodName(), $this->getMethodName())
            );
        }

        // merge the name
        if ($name = $actionDescriptor->getName()) {
            $this->setName($name);
        }
    }
}
