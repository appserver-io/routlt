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

use AppserverIo\Routlt\Annotations\Path;
use AppserverIo\Routlt\Annotations\Result;
use AppserverIo\Routlt\Annotations\Results;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Description\DescriptorReferencesTrait;
use AppserverIo\Description\AbstractReferenceDescriptor;
use AppserverIo\Description\ReferenceDescriptorInterface;

/**
 * Annotation to map a request path info to an action method.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class PathDescriptor extends AbstractReferenceDescriptor implements PathDescriptorInterface, ReferenceDescriptorInterface
{

    /**
     * The trait with the references descriptors.
     *
     * @var AppserverIo\Description\DescriptorReferencesTrait
     */
    use DescriptorReferencesTrait;

    /**
     * The beans class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The array with the action methods.
     *
     * @var array
     */
    protected $actions = array();

    /**
     * The array with the action results.
     *
     * @var array
     */
    protected $results = array();

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
     * @param \AppserverIo\Routlt\Description\ActionDescriptorInterface $action The action method configuration
     *
     * @return void
     */
    public function addAction(ActionDescriptorInterface $action)
    {
        $name = $action->getName();
        foreach ($action->getRequestMethods() as $method) {
            $this->actions[$name][$method] = $action;
        }
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
     * Adds a result action configuration.
     *
     * @param \AppserverIo\Routlt\Description\ResultDescriptorInterface $result The action result configuration
     *
     * @return void
     */
    public function addResult(ResultDescriptorInterface $result)
    {
        $this->results[$result->getName()] = $result;
    }

    /**
     * Sets the array with the action results.
     *
     * @param array $results The action results
     *
     * @return void
     */
    public function setResults(array $results)
    {
        $this->results = $results;
    }

    /**
     * The array with the action results.
     *
     * @return array The action results
     */
    public function getResults()
    {
        return $this->results;
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
        $reflectionClass->addAnnotationAlias(Result::ANNOTATION, Result::__getClass());
        $reflectionClass->addAnnotationAlias(Results::ANNOTATION, Results::__getClass());

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
            // if @Annotation(name=****) is NOT set, we use the class name by default
            $name = strtolower(str_replace('\\', '/', $reflectionClass->getName()));
        }

        // prepare and set the name
        $this->setName(sprintf('/%s', ltrim($name, '/')));

        // we've to check for method annotations that declare the action methods
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($action = ActionDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addAction($action);
            }
        }

        // we've to check for result annotations
        if ($reflectionClass->hasAnnotation(Results::ANNOTATION)) {
            // create the reflection annotation instance
            $resultsAnnotation = $reflectionClass->getAnnotation(Results::ANNOTATION);

            // initialize the @Results annotation instance
            $resultsAnnotationInstance = $resultsAnnotation->newInstance(
                $resultsAnnotation->getAnnotationName(),
                $resultsAnnotation->getValues()
            );

            // load the results
            foreach ($resultsAnnotationInstance->getResults() as $resultAnnotation) {
                $this->addResult(ResultDescriptor::newDescriptorInstance()->fromReflectionAnnotation($resultAnnotation));
            }
        }

        // initialize references from the passed reflection class
        $this->referencesFromReflectionClass($reflectionClass);

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
     * Initializes a bean configuration instance from the passed configuration node.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $node The configuration node with the bean configuration
     *
     * @return \AppserverIo\Routlt\Description\PathDescriptorInterface The initialized descriptor
     */
    public function fromConfiguration(NodeInterface $node)
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

        // merge the result descriptors
        foreach ($pathDescriptor->getResults() as $result) {
            $this->addResult($result);
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
