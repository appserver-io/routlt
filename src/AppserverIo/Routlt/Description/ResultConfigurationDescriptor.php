<?php

/**
 * AppserverIo\Routlt\Description\ResultConfigurationDescriptor
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
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Description\AbstractNameAwareDescriptor;

/**
 * Descriptor implementation for a result configuration.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class ResultConfigurationDescriptor extends AbstractNameAwareDescriptor implements ResultConfigurationDescriptorInterface
{

    /**
     * The action result type.
     *
     * @var string
     */
    protected $type;

    /**
     * The action result value.
     *
     * @var string
     */
    protected $result;

    /**
     * The HTTP response code that has to be send.
     *
     * @var string
     */
    protected $code = 200;

    /**
     * Sets the action result type.
     *
     * @param string $type The action result type
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
     * @param string $result The action result value
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
     * Sets the HTTP response code that has to be send.
     *
     * @param integer $code The HTTP response code
     *
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Returns the HTTP response code that has to be send.
     *
     * @return integer The HTTP response code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Routlt\Description\ResultDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new ResultConfigurationDescriptor();
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
        return $reflectionClass->getAnnotation(Result::ANNOTATION);
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
    }

    /**
     * Initializes the result configuration instance from the passed result annotation instance.
     *
     * @param \AppserverIo\Routlt\Annotations\Result $annotation The result annotation with the result configuration
     *
     * @return \AppserverIo\Routlt\Description\ResultDescriptorInterface The initialized descriptor
     */
    public function fromAnnotation(Result $annotation)
    {

        // initialize the descriptor properties from the annotation values
        $this->setName($annotation->getName());
        $this->setType($annotation->getType());
        $this->setResult($annotation->getResult());

        // set the HTTP response code if given
        if ($code = $annotation->getCode()) {
            $this->setCode($code);
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
     * @param \AppserverIo\Routlt\Description\ResultConfigurationDescriptorInterface $resultConfigurationDescriptor The configuration to merge
     *
     * @return void
     * @throws \AppserverIo\Routlt\Description\DescriptorException Is thrown if the passed descriptor has a different method name
     */
    public function merge(ResultConfigurationDescriptorInterface $resultConfigurationDescriptor)
    {

        // check if the classes are equal
        if ($this->getName() !== $resultConfigurationDescriptor->getName()) {
            throw new DescriptorException(
                sprintf('You try to merge a path result configuration for % with %s', $resultConfigurationDescriptor->getName(), $this->getName())
            );
        }

        // merge the type
        if ($type = $resultConfigurationDescriptor->getType()) {
            $this->setType($type);
        }

        // merge the result
        if ($result = $resultConfigurationDescriptor->getResult()) {
            $this->setResult($result);
        }

        // merge the code
        if ($code = $resultConfigurationDescriptor->getCode()) {
            $this->setCode($code);
        }
    }
}
