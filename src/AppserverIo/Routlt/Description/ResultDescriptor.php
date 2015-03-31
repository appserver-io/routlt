<?php

/**
 * AppserverIo\Routlt\Description\ResultDescriptor
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

use AppserverIo\Routlt\Annotations\Result;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\ReflectionAnnotation;
use AppserverIo\Lang\Reflection\AnnotationInterface;

/**
 * Descriptor implementation for a action result.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ResultDescriptor implements ResultDescriptorInterface
{

    /**
     * The arction result name.
     *
     * @var string
     */
    protected $name;

    /**
     * The action result type.
     *
     * @var string
     */
    protected $type;

    /**
     * The action result value.
     *
     * @var array
     */
    protected $result;

    /**
     * Sets the action result name.
     *
     * @param string $name The action result name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the action result name.
     *
     * @return string The action result name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the action result type.
     *
     * @param string $name The action result type
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the action result type.
     *
     * @return string The action result type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the action result value.
     *
     * @param string $name The action result value
     *
     * @return void
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Returns the action result value.
     *
     * @return string The action result value
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Routlt\Description\ResultDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new ResultDescriptor();
    }

    /**
     * Initializes the result configuration instance from the passed reflection annotation instance.
     *
     * @param \AppserverIo\Lang\Reflection\AnnotationInterface $reflectionAnnotation The reflection annotation with the result configuration
     *
     * @return \AppserverIo\Routlt\Description\ResultDescriptorInterface The initialized descriptor
     */
    public function fromReflectionAnnotation(AnnotationInterface $reflectionAnnotation)
    {

        // initialize the annotation instance
        $annotationInstance = $reflectionAnnotation->newInstance(
            $reflectionAnnotation->getAnnotationName(),
            $reflectionAnnotation->getValues()
        );

        // initialize the descriptor properties from the annotation values
        $this->setName($annotationInstance->getName());
        $this->setType($annotationInstance->getType());
        $this->setResult($annotationInstance->getResult());

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
     * @param \AppserverIo\Routlt\Description\ResultDescriptorInterface $resultDescriptor The configuration to merge
     *
     * @return void
     * @throws \AppserverIo\Routlt\Description\DescriptorException Is thrown if the passed descriptor has a different method name
     */
    public function merge(ResultDescriptorInterface $resultDescriptor)
    {

        // check if the classes are equal
        if ($this->getName() !== $resultDescriptor->getName()) {
            throw new DescriptorException(
                sprintf('You try to merge a result configuration for % with %s', $resultDescriptor->getName(), $this->getName())
            );
        }

        // merge the type
        if ($type = $resultDescriptor->getType()) {
            $this->setType($type);
        }

        // merge the result
        if ($result = $resultDescriptor->getResult()) {
            $this->setResult($result);
        }
    }
}
