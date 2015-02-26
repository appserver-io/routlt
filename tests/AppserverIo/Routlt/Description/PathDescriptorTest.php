<?php

/**
 * AppserverIo\Routlt\Description\PathDescriptorTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt\Description;

use AppserverIo\Routlt\ActionInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Description\EpbReferenceDescriptor;
use AppserverIo\Description\ResReferenceDescriptor;

/**
 * Test implementation for the PathDescriptor implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 *
 * @Path(name="/index")
 */
class PathDescriptorTest extends \PHPUnit_Framework_TestCase implements ActionInterface
{

    /**
     * The action descriptor instance to test.
     *
     * @var \AppserverIo\Routlt\Description\PathDescriptor
     */
    protected $descriptor;

    /**
     * Dummy bean reference.
     *
     * @EnterpriseBean(name="SessionBean")
     */
    protected $dummyEnterpriseBean;

    /**
     * Dummy resource reference.
     *
     * @Resource(name="Application")
     */
    protected $dummyResource;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->descriptor = new PathDescriptor();
    }

    /**
     * Injects the dummy bean instance.
     *
     * @param mixed $dummyEnterpriseBean The dummy bean
     *
     * @return void
     * @EnterpriseBean(name="SessionBean")
     */
    public function injectDummyEnterpriseBean($dummyEnterpriseBean)
    {
        $this->dummyEnterpriseBean = $dummyEnterpriseBean;
    }

    /**
     * Injects the dummy resource instance.
     *
     * @param mixed $dummyResource The dummy resource
     *
     * @return void
     * @Resource(name="Application")
     */
    public function injectDummyResource($dummyResource)
    {
        $this->dummyResource = $dummyResource;
    }

    /**
     * Tests the static newDescriptorInstance() method.
     *
     * @return void
     */
    public function testNewDescriptorInstance()
    {
        $this->assertInstanceOf(
            'AppserverIo\Routlt\Description\PathDescriptor',
            PathDescriptor::newDescriptorInstance()
        );
    }

    /**
     * Test the fromDeploymentDescriptor() method with is actually not implemented.
     *
     * @return void
     */
    public function testFromDeploymentDescriptor()
    {
        $this->assertNull($this->descriptor->fromDeploymentDescriptor(new \SimpleXMLElement('<root/>')));
    }

    /**
     * Tests if the deployment initialization from a reflection class works as expected.
     *
     * @return void
     */
    public function testFromReflectionClass()
    {

        // initialize the annotation aliases
        $aliases = array(
            Resource::ANNOTATION => Resource::__getClass(),
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass()
        );

        // create a reflection class
        $reflectionClass = new ReflectionClass(__CLASS__, array(), $aliases);

        // initialize the descriptor instance from the reflection class
        $this->descriptor->fromReflectionClass($reflectionClass);

        // check the name parsed from the reflection class
        $this->assertSame('/index*', $this->descriptor->getName());
    }

    /**
     * Tests if the deployment initialization from a reflection class with a @Path
     * annotation without name attribute works as expected.
     *
     * @return void
     */
    public function testFromReflectionClassWithAnnotationWithMissingNameAttribute()
    {

        // initialize the annotation aliases
        $aliases = array(
            Resource::ANNOTATION => Resource::__getClass(),
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass()
        );

        // create a reflection class from a mock controller with a @Path annotation without name attribute
        $reflectionClass = new ReflectionClass('AppserverIo\Routlt\Description\Mock\MockAction', array(), $aliases);

        // initialize the descriptor instance from the reflection class
        $this->descriptor->fromReflectionClass($reflectionClass);

        // check the name parsed from the reflection class
        $this->assertSame('/mock*', $this->descriptor->getName());
    }

    /**
     * Tests if the deployment initialization from a reflection class with
     * a missing @Path annotation doesn't work.
     *
     * @return void
     */
    public function testFromReflectionClassWithMissingAnnotation()
    {

        // initialize the annotation aliases
        $aliases = array(
            Resource::ANNOTATION => Resource::__getClass(),
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass()
        );

        // create a reflection class from the BaseAction
        $reflectionClass = new ReflectionClass('AppserverIo\Routlt\BaseAction', array(), $aliases);

        // check that the descriptor has not been initialized
        $this->assertNull($this->descriptor->fromReflectionClass($reflectionClass));
    }

    /**
     * Tests if the deployment initialization from a reflection class that
     * is NO action doesn't work.
     *
     * @return void
     */
    public function testFromReflectionClassWithNoActionImplementation()
    {

        // initialize the annotation aliases
        $aliases = array(
            Resource::ANNOTATION => Resource::__getClass(),
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass()
        );

        // create a reflection class from the \stdClass
        $reflectionClass = new ReflectionClass('\stdClass', array(), $aliases);

        // check that the descriptor has not been initialized
        $this->assertNull($this->descriptor->fromReflectionClass($reflectionClass));
    }

    /**
     * Check that the merge() method works as expected.
     *
     * @return void
     */
    public function testMerge()
    {

        // initialize the annotation aliases
        $aliases = array(
            Resource::ANNOTATION => Resource::__getClass(),
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass()
        );

        // create a reflection class
        $reflectionClass = new ReflectionClass(__CLASS__, array(), $aliases);

        // initialize the descriptor instance from reflection class
        $this->descriptor->fromReflectionClass($reflectionClass);

        // initialize the descriptor to merge
        $descriptorToMerge = $this->getMockBuilder('AppserverIo\Routlt\Description\PathDescriptor')
            ->setMethods(
                array(
                    'getName',
                    'getActions',
                    'getClassName',
                    'getResReferences',
                    'getEpbReferences'
                )
            )
            ->getMock();

        // create a action descriptor
        $action = new ActionDescriptor();
        $action->setName('/test');
        $action->setMethodName('someMethod');

        // create a resource descriptor
        $resReference = new ResReferenceDescriptor();
        $resReference->setName('SomeResource');

        // create an EPB descriptor
        $epbReference = new EpbReferenceDescriptor();
        $epbReference->getName('UserSessionBean');

        // mock the methods
        $descriptorToMerge->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('/anotherIndex'));
        $descriptorToMerge->expects($this->once())
            ->method('getActions')
            ->will($this->returnValue(array($action)));
        $descriptorToMerge->expects($this->once())
            ->method('getClassName')
            ->will($this->returnValue(__CLASS__));
        $descriptorToMerge->expects($this->once())
            ->method('getEpbReferences')
            ->will($this->returnValue(array($epbReference)));
        $descriptorToMerge->expects($this->once())
            ->method('getResReferences')
            ->will($this->returnValue(array($resReference)));

        // merge the descriptors
        $this->descriptor->merge($descriptorToMerge);

        // check the merge values
        $this->assertSame('/anotherIndex', $this->descriptor->getName());
        $this->assertSame(__CLASS__, $this->descriptor->getClassName());
        $this->assertCount(2, $this->descriptor->getActions());
        $this->assertCount(4, $this->descriptor->getReferences());
        $this->assertCount(2, $this->descriptor->getResReferences());
        $this->assertCount(2, $this->descriptor->getEpbReferences());

        // load the actions
        $actions = $this->descriptor->getActions();

        // check that the method name has been overwritten
        $this->assertEquals($action->getMethodName(), $actions['/test']->getMethodName());
    }

    /**
     * Tests if the merge method fails with an exception if the class
     * name doesn't match when try to merge to descriptor instances.
     *
     * @return void
     * @expectedException AppserverIo\Routlt\Description\DescriptorException
     */
    public function testMergeWithNotMatchingDescriptor()
    {

        // initialize the annotation aliases
        $aliases = array(
            Resource::ANNOTATION => Resource::__getClass(),
            EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass()
        );

        // create a reflection class
        $reflectionClass = new ReflectionClass(__CLASS__, array(), $aliases);

        // initialize the descriptor instance from reflection class
        $this->descriptor->fromReflectionClass($reflectionClass);

        // initialize the descriptor to merge
        $descriptorToMerge = $this->getMockBuilder('AppserverIo\Routlt\Description\PathDescriptor')
            ->setMethods(array('getClassName'))
            ->getMock();

        // mock the getClassName() method
        $descriptorToMerge->expects($this->exactly(2))
            ->method('getClassName')
            ->will($this->returnValue('UnknownClass'));

        // merge the descriptors
        $this->descriptor->merge($descriptorToMerge);
    }

    /**
     * Tests the setter/getter for the EPB references.
     *
     * @return void
     */
    public function testSetGetEpbReferences()
    {
        $this->descriptor->setEpbReferences($epbReferences = array(new \stdClass()));
        $this->assertSame($epbReferences, $this->descriptor->getEpbReferences());
    }

    /**
     * Tests the setter/getter for the resource references.
     *
     * @return void
     */
    public function testSetGetResReferences()
    {
        $this->descriptor->setResReferences($resReferences = array(new \stdClass()));
        $this->assertSame($resReferences, $this->descriptor->getResReferences());
    }

    /**
     * Tests the setter/getter for the actions.
     *
     * @return void
     */
    public function testSetGetActions()
    {
        $this->descriptor->setActions($actions = array(new \stdClass()));
        $this->assertSame($actions, $this->descriptor->getActions());
    }

    /**
     * Method that will be invoked before we dispatch the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function preDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

    }

    /**
     * All classes extending this class must implement the perform() method.
     *
     * This method implements the complete functionality of the action and have to return an initialized
     * ActionForward object that is necessary for further application flow controlled by the
     * ActionController.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
    */
    public function perform(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

    }

    /**
     * Method that will be invoked after we've dispatched the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
    */
    public function postDispatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

    }

    /**
     * @Action(name="/test")
     */
    public function nonameAction()
    {
        // dummy method with a @Action annotation
    }

    /**
     * @Action
     */
    public function someAction()
    {
        // dummy method with a @Action annotation
    }
}
