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
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Description\AbstractNameAwareDescriptor;

/**
 * Annotation to map a request path info to an action method.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ActionDescriptor extends AbstractNameAwareDescriptor implements ActionDescriptorInterface
{

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
     * The restrictions for the route placeholders.
     *
     * @var array
     */
    protected $restrictions;

    /**
     * The defaults for the route placeholders.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Initializes the descriptor with the default
     * request methods the action is listening to.
     */
    public function __construct()
    {

        // initialize restrictions and defaults
        $this->defaults = array();
        $this->restrictions = array();

        // initialize the request methods
        $this->requestMethods = array(
            HttpProtocol::METHOD_CONNECT,
            HttpProtocol::METHOD_DELETE,
            HttpProtocol::METHOD_GET,
            HttpProtocol::METHOD_HEAD,
            HttpProtocol::METHOD_OPTIONS,
            HttpProtocol::METHOD_POST,
            HttpProtocol::METHOD_PUT,
            HttpProtocol::METHOD_TRACE,
            HttpProtocol::METHOD_PATCH
        );
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
     * Sets the restrictions for the route placeholders.
     *
     * @param array $restrictions The restrictions for the route placeholders
     *
     * @return void
     */
    public function setRestrictions(array $restrictions)
    {
        $this->restrictions = $restrictions;
    }

    /**
     * Returns the restrictions for the route placeholders.
     *
     * @return array The restrictions for the route placeholders
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Sets the defaults for the route placeholders.
     *
     * @param array $defaults The defaults for the route placeholders
     *
     * @return void
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Returns the defaults for the route placeholders.
     *
     * @return array The defaults for the route placeholders
     */
    public function getDefaults()
    {
        return $this->defaults;
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
        if (($nameAttribute = $annotationInstance->getName()) || $nameAttribute === '') {
            $this->setName($nameAttribute);
        } else {
            // if @Annotation(name=****) is NOT SET, we use the method name by default
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

        // initialize the restrictions for the route placeholders
        if (is_array($restrictions = $annotationInstance->getRestrictions())) {
            foreach ($restrictions as $restriction) {
                list($name, $value) = $restriction;
                $this->restrictions[$name] = $value;
            }
        }

        // initialize the defaults for the route placeholders
        if (is_array($defaults = $annotationInstance->getDefaults())) {
            foreach ($defaults as $default) {
                list($name, $value) = $default;
                $this->defaults[$name] = $value;
            }
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
     * Initializes a action configuration instance from the passed configuration node.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $node The configuration node with the action configuration
     *
     * @return \AppserverIo\Routlt\Description\ActionDescriptorInterface The initialized descriptor
     */
    public function fromConfiguration(NodeInterface $node)
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
