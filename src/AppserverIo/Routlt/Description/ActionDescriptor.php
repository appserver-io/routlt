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
 * @category   Library
 * @package    Routlt
 * @subpackage Descriptor
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Routlt\Description;

use AppserverIo\Routlt\Annotations\Action;
use AppserverIo\Lang\Reflection\MethodInterface;
use AppserverIo\Lang\Reflection\ReflectionAnnotation;

/**
 * Annotation to map a request path info to an action method.
 *
 * @category   Library
 * @package    Routlt
 * @subpackage Descriptor
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
        if ($reflectionMethod->hasAnnotation(Action::ANNOTATION) === false) { // if not, do nothing
            return;
        }

        // create a new annotation instance
        $reflectionAnnotation = $this->newAnnotationInstance($reflectionMethod);

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
        } else { // if @Annotation(name=****) is NOT set, we use the method name by default
            $this->setName(lcfirst(str_replace('Action', '', $reflectionMethod->getMethodName())));
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
