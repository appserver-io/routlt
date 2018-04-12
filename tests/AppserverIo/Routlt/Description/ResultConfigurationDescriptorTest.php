<?php

/**
 * AppserverIo\Routlt\Description\ResultConfigurationDescriptorTest
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

/**
 * Test implementation for the ResultConfigurationDescriptor implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ResultConfigurationDescriptorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The result descriptor instance to test.
     *
     * @var \AppserverIo\Routlt\Description\ResultConfigurationDescriptor
     */
    protected $descriptor;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->descriptor = new ResultConfigurationDescriptor();
    }

    /**
     * Tests if the deployment initialization from a reflection annotation works as expected.
     *
     * @return void
     */
    public function testFromReflectionAnnotation()
    {

        // create a mock annotation
        $mockAnnotation = $this->getMockBuilder('AppserverIo\Routlt\Annotations\Result')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockAnnotation->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('success'));
        $mockAnnotation->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('success'));
        $mockAnnotation->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue('success'));

        // create a mock reflection annotation
        $mockReflectionAnnotation = $this->getMockBuilder('AppserverIo\Lang\Reflection\ReflectionAnnotation')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockReflectionAnnotation->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($mockAnnotation));

        // initialize the descriptor instance
        $this->descriptor->fromReflectionAnnotation($mockReflectionAnnotation);

        // check the name parsed from the reflection method
        $this->assertSame('success', $this->descriptor->getName());
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
     * Check that the merge() method works as expected.
     *
     * @return void
     */
    public function testMerge()
    {

        // create a mock annotation
        $mockAnnotation = $this->getMockBuilder('AppserverIo\Routlt\Annotations\Result')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockAnnotation->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('success'));
        $mockAnnotation->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('DummyResult'));
        $mockAnnotation->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue('/phtml/test.phtml'));

        // create a mock reflection annotation
        $mockReflectionAnnotation = $this->getMockBuilder('AppserverIo\Lang\Reflection\ReflectionAnnotation')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockReflectionAnnotation->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($mockAnnotation));

        // initialize the descriptor instance
        $this->descriptor->fromReflectionAnnotation($mockReflectionAnnotation);

        // create the descriptor we want to merge
        $descriptor = new ResultConfigurationDescriptor();
        $descriptor->setName('success');
        $descriptor->setType('OtherResult');
        $descriptor->setResult('/phtml/another_test.phtml');

        // merge the descriptors
        $this->descriptor->merge($descriptor);

        // check the method name and that the name has been overwritten
        $this->assertSame('success', $this->descriptor->getName());
        $this->assertSame('OtherResult', $this->descriptor->getType());
        $this->assertSame('/phtml/another_test.phtml', $this->descriptor->getResult());
    }

    /**
     * Check that the merge() method with descriptor containing an other
     * method name throws an Exception.
     *
     * @return void
     * @expectedException AppserverIo\Routlt\Description\DescriptorException
     */
    public function testMergeWithNotMatchingDescriptor()
    {

        // create a mock annotation
        $mockAnnotation = $this->getMockBuilder('AppserverIo\Routlt\Annotations\Result')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockAnnotation->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('success'));
        $mockAnnotation->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('DummyResult'));
        $mockAnnotation->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue('/phtml/test.phtml'));

        // create a mock reflection annotation
        $mockReflectionAnnotation = $this->getMockBuilder('AppserverIo\Lang\Reflection\ReflectionAnnotation')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the methods
        $mockReflectionAnnotation->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($mockAnnotation));

        // initialize the descriptor instance
        $this->descriptor->fromReflectionAnnotation($mockReflectionAnnotation);

        // create the descriptor we want to merge
        $descriptor = new ResultConfigurationDescriptor();
        $descriptor->setName('failure');
        $descriptor->setType('OtherResult');
        $descriptor->setResult('/phtml/another_test.phtml');

        // merge the descriptors
        $this->descriptor->merge($descriptor);
    }
}
