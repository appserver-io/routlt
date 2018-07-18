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
use AppserverIo\Routlt\Annotations\Connect;
use AppserverIo\Routlt\Annotations\Delete;
use AppserverIo\Routlt\Annotations\Get;
use AppserverIo\Routlt\Annotations\Head;
use AppserverIo\Routlt\Annotations\Options;
use AppserverIo\Routlt\Annotations\Patch;
use AppserverIo\Routlt\Annotations\Post;
use AppserverIo\Routlt\Annotations\Put;
use AppserverIo\Routlt\Annotations\Trace;
use AppserverIo\Lang\Reflection\MethodInterface;
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
    protected $requestMethods = array();

    protected $mappedRequestMethods = array(
        HttpProtocol::METHOD_CONNECT => Connect::class,
        HttpProtocol::METHOD_DELETE  => Delete::class,
        HttpProtocol::METHOD_GET     => Get::class,
        HttpProtocol::METHOD_HEAD    => Head::class,
        HttpProtocol::METHOD_OPTIONS => Options::class,
        HttpProtocol::METHOD_POST    => Post::class,
        HttpProtocol::METHOD_PUT     => Put::class,
        HttpProtocol::METHOD_TRACE   => Trace::class,
        HttpProtocol::METHOD_PATCH   => Patch::class
    );

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
        $this->requestMethods = array_keys($this->getMappedRequestMethods());
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
     * Add's the passed key => value pair as restriction.
     *
     * @param string $key   The parameter name to add the restriction for
     * @param string $value The restriction value itself
     *
     * @return void
     */
    public function addRestriction($name, $value)
    {
        $this->restrictions[$name] = $value;
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
     * Add's the passed key => value pair as default.
     *
     * @param string $key   The parameter name to add the default for
     * @param string $value The default value itself
     *
     * @return void
     */
    public function addDefault($name, $value)
    {
        $this->defaults[$name] = $value;
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
     * Return's the array with the reqeust method => annotation class mapping.
     *
     * @return array The mapping
     */
    protected function getMappedRequestMethods()
    {
        return $this->mappedRequestMethods;
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

        // create a new annotation instance
        $annotationInstance = $this->getMethodAnnotation($reflectionMethod, Action::class);

        // query if we've a method with a @Action annotation
        if ($annotationInstance === null && $reflectionMethod->getMethodName() !== 'perform') {
            // if not, do nothing
            return;
        }

        // query whether we've the default perform() method WITHOUT an @Action annotation
        if ($annotationInstance === null && $reflectionMethod->getMethodName() === 'perform') {
            // create a new annotation instance
            $annotationInstance = new Action(array('name' => $reflectionMethod->getMethodName()));
        }

        // load method name
        $this->setMethodName($reflectionMethod->getMethodName());

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
        foreach ($this->getMappedRequestMethods() as $requestMethod => $annotationName) {
            // query whether the reflection method has been annotated
            if ($this->getMethodAnnotation($reflectionMethod, $annotationName)) {
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
                $this->addRestriction($name, $value);
            }
        }

        // initialize the defaults for the route placeholders
        if (is_array($defaults = $annotationInstance->getDefaults())) {
            foreach ($defaults as $default) {
                list($name, $value) = $default;
                $this->addDefault($name, $value);
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
     * of the passed configuration will overwrite this one.
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
                sprintf('You try to merge a action configuration for %s with %s', $actionDescriptor->getMethodName(), $this->getMethodName())
            );
        }

        // merge the name
        if ($name = $actionDescriptor->getName()) {
            $this->setName($name);
        }
    }
}
