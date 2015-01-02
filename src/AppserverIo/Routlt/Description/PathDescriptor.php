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

use AppserverIo\Psr\Servlet\GenericServlet;
use AppserverIo\Routlt\Annotations\Path;
use AppserverIo\Routlt\Description\ActionDescriptorInterface;
use AppserverIo\Routlt\Description\DescriptorException;
use AppserverIo\Lang\Reflection\ClassInterface;
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

        // query if we've an action
        if ($reflectionClass->implementsInterface('AppserverIo\Routlt\Action') === false &&
            $reflectionClass->toPhpReflectionClass()->isAbstract() === false) { // if not, do nothing
            return;
        }

        // query if we've a servlet with a @Path annotation
        if ($reflectionClass->hasAnnotation(Path::ANNOTATION) === false) { // if not, do nothing
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
        } else { // if @Annotation(name=****) is NOT set, we use the short class name by default
            $name = lcfirst(str_replace('Action', '', $reflectionClass->getShortName()));
        }

        // prepare and set the name
        $this->setName(sprintf('/%s*', ltrim($name, '/')));

        // we've to check for method annotations that declare the action methods
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($action = ActionDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addAction($action);
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
    }
}
