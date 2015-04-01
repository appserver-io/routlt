<?php

/**
 * AppserverIo\Routlt\Annotations\ResultTest
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

namespace AppserverIo\Routlt\Annotations;

/**
 * Test implementation for the @Result annotation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The path annotation instance to test.
     *
     * @var \AppserverIo\Routlt\Annotations\Result
     */
    protected $annotation;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->annotation = new Result('Result', array('name' => 'success', 'type' => 'AppserverIo\Routlt\Results\JsonResult', 'result' => '/phtml/my_template.phtml'));
    }

    /**
     * This test checks the resolved name.
     *
     * @return void
     */
    public function testGetName()
    {
        $this->assertSame('success', $this->annotation->getName());
    }

    /**
     * This test checks the resolved type.
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertSame('AppserverIo\Routlt\Results\JsonResult', $this->annotation->getType());
    }

    /**
     * This test checks the resolved result.
     *
     * @return void
     */
    public function testGetResult()
    {
        $this->assertSame('/phtml/my_template.phtml', $this->annotation->getResult());
    }

    /**
     * This test checks a that the resolved name is NULL.
     *
     * @return void
     */
    public function testGetNameWithoutValue()
    {
        $annotation = new Result('Result', array());
        $this->assertNull($annotation->getName());
    }

    /**
     * This test checks a that the resolved type is NULL.
     *
     * @return void
     */
    public function testGetTypeWithoutValue()
    {
        $annotation = new Result('Result', array());
        $this->assertNull($annotation->getType());
    }

    /**
     * This test checks a that the resolved result is NULL.
     *
     * @return void
     */
    public function testGetResultWithoutValue()
    {
        $annotation = new Result('Result', array());
        $this->assertNull($annotation->getResult());
    }

    /**
     * This test checks the resolved class name.
     *
     * @return void
     */
    public function testGetClass()
    {
        $this->assertSame('AppserverIo\Routlt\Annotations\Result', $this->annotation->__getClass());
    }
}
