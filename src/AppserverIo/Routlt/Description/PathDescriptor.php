<?php

/**
 * AppserverIo\Routlt\Description\PathDescriptor
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

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\ReflectionAnnotation;
use AppserverIo\Routlt\Annotations\Path;
use AppserverIo\Routlt\Description\DescriptorException;
use AppserverIo\Routlt\Description\ActionDescriptorInterface;
use AppserverIo\Description\EpbReferenceDescriptor;
use AppserverIo\Description\ResReferenceDescriptor;
use AppserverIo\Psr\Servlet\GenericServlet;
use AppserverIo\Psr\EnterpriseBeans\Description\EpbReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\ResReferenceDescriptorInterface;

/**
 * Annotation to map a request path info to an action method.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class PathDescriptor implements PathDescriptorInterface
{

    /**
     * The bean name.
     *
     * @var string
     */
    protected $name;

    /**
     * The beans class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The array with the EPB references.
     *
     * @var array
     */
    protected $epbReferences = array();

    /**
     * The array with the resource references.
     *
     * @var array
     */
    protected $resReferences = array();

    /**
     * The array with the action methods.
     *
     * @var array
     */
    protected $actions = array();

    /**
     * Sets the bean name.
     *
     * @param string $name The bean name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the bean name.
     *
     * @return string The bean name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the beans class name.
     *
     * @param string $className The beans class name
     *
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the beans class name.
     *
     * @return string The beans class name
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Adds a action method configuration.
     *
     * @param \AppserverIo\Routlt\Description\ActionDescriptorInterface $action The action method onfiguration
     *
     * @return void
     */
    public function addAction(ActionDescriptorInterface $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * Sets the array with the action methods.
     *
     * @param array $actions The action methods
     *
     * @return void
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * The array with the action methods.
     *
     * @return array The action methods
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Adds a EPB reference configuration.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface $epbReference The EPB reference configuration
     *
     * @return void
     */
    public function addEpbReference(EpbReferenceDescriptorInterface $epbReference)
    {
        $this->epbReferences[$epbReference->getName()] = $epbReference;
    }

    /**
     * Sets the array with the EPB references.
     *
     * @param array $epbReferences The EPB references
     *
     * @return void
     */
    public function setEpbReferences(array $epbReferences)
    {
        $this->epbReferences = $epbReferences;
    }

    /**
     * The array with the EPB references.
     *
     * @return array The EPB references
     */
    public function getEpbReferences()
    {
        return $this->epbReferences;
    }

    /**
     * Adds a resource reference configuration.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ResReferenceDescriptorInterface $resReference The resource reference configuration
     *
     * @return void
     */
    public function addResReference(ResReferenceDescriptorInterface $resReference)
    {
        $this->resReferences[$resReference->getName()] = $resReference;
    }

    /**
     * Sets the array with the resource references.
     *
     * @param array $resReferences The resource references
     *
     * @return void
     */
    public function setResReferences(array $resReferences)
    {
        $this->resReferences = $resReferences;
    }

    /**
     * The array with the resource references.
     *
     * @return array The resource references
     */
    public function getResReferences()
    {
        return $this->resReferences;
    }

    /**
     * Returns an array with the merge EBP and resource references.
     *
     * @return array The array with the merge all bean references
     */
    public function getReferences()
    {
        return array_merge($this->epbReferences, $this->resReferences);
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Routlt\Description\PathDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new PathDescriptor();
    }

    /**
     * Returns a new annotation instance for the passed reflection class.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The reflection annotation
     */
    protected function newAnnotationInstance(ClassInterface $reflectionClass)
    {
        return $reflectionClass->getAnnotation(Path::ANNOTATION);
    }

    /**
     * Initializes the bean configuration instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Routlt\Description\PathDescriptorInterface The initialized descriptor
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // add the annotation alias to the reflection class
        $reflectionClass->addAnnotationAlias(Path::ANNOTATION, Path::__getClass());

        // query if we've an action
        if ($reflectionClass->implementsInterface('AppserverIo\Routlt\ActionInterface') === false &&
            $reflectionClass->toPhpReflectionClass()->isAbstract() === false) {
            // if not, do nothing
            return;
        }

        // query if we've a servlet with a @Path annotation
        if ($reflectionClass->hasAnnotation(Path::ANNOTATION) === false) {
            // if not, do nothing
            return;
        }

        // create a new annotation instance
        $reflectionAnnotation = $this->newAnnotationInstance($reflectionClass);

        // load class name
        $this->setClassName($reflectionClass->getName());

        // initialize the annotation instance
        $annotationInstance = $reflectionAnnotation->newInstance(
            $reflectionAnnotation->getAnnotationName(),
            $reflectionAnnotation->getValues()
        );

        // load the default name to register in naming directory
        if ($nameAttribute = $annotationInstance->getName()) {
            $name = $nameAttribute;
        } else {
            // if @Annotation(name=****) is NOT set, we use the short class name by default
            $name = lcfirst(str_replace('Controller', '', $reflectionClass->getShortName()));
        }

        // prepare and set the name
        $this->setName(sprintf('/%s*', ltrim($name, '/')));

        // we've to check for method annotations that declare the action methods
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($action = ActionDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addAction($action);
            }
        }

        // we've to check for property annotations that references EPB or resources
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            // load the EPB references
            if ($epbReference = EpbReferenceDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty)) {
                $this->addEpbReference($epbReference);
            }

            // load the resource references
            if ($resReference = ResReferenceDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty)) {
                $this->addResReference($resReference);
            }
        }

        // we've to check for method annotations that references EPB or resources
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            // load the EPB references
            if ($epbReference = EpbReferenceDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addEpbReference($epbReference);
            }

            // load the resource references
            if ($resReference = ResReferenceDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addResReference($resReference);
            }
        }

        // return the instance
        return $this;
    }

    /**
     * Initializes a bean configuration instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean configuration
     *
     * @return \AppserverIo\Routlt\Description\PathDescriptorInterface The initialized descriptor
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Routlt\Description\PathDescriptorInterface $pathDescriptor The configuration to merge
     *
     * @return void
     * @throws \AppserverIo\Routlt\Description\DescriptorException Is thrown if the passed descriptor has a different class name
     */
    public function merge(PathDescriptorInterface $pathDescriptor)
    {

        // check if the classes are equal
        if ($this->getClassName() !== $pathDescriptor->getClassName()) {
            throw new DescriptorException(
                sprintf('You try to merge a path configuration for % with %s', $pathDescriptor->getClassName(), $this->getClassName())
            );
        }

        // merge the name
        if ($name = $pathDescriptor->getName()) {
            $this->setName($name);
        }

        // merge the action method descriptors
        foreach ($pathDescriptor->getActions() as $action) {
            $this->addAction($action);
        }

        // merge the EPB references
        foreach ($pathDescriptor->getEpbReferences() as $epbReference) {
            $this->addEpbReference($epbReference);
        }

        // merge the EPB references
        foreach ($pathDescriptor->getResReferences() as $resReference) {
            $this->addResReference($resReference);
        }
    }
}
