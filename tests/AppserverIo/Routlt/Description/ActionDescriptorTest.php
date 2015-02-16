<?php

/**
 * AppserverIo\Routlt\Description\ActionDescriptorTest
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

use AppserverIo\Lang\Reflection\ReflectionClass;
/**
 * Test implementation for the ActionDescriptor implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ActionDescriptorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The action descriptor instance to test.
     *
     * @var \AppserverIo\Routlt\Description\ActionDescriptor
     */
    protected $descriptor;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->descriptor = new ActionDescriptor();
    }

    /**
     * Tests if the deployment initialization from a reflection method works as expected.
     *
     * @return void
     */
    public function testFromReflectionMethod()
    {

        // create a reflection class and load the reflection method
        $reflectionClass = new ReflectionClass(__CLASS__);
        $reflectionMethod = $reflectionClass->getMethod('nonameAction');

        // initialize the descriptor instance
        $this->descriptor->fromReflectionMethod($reflectionMethod);

        // check the name parsed from the reflection method
        $this->assertSame('/test', $this->descriptor->getName());
    }

    /**
     * @Action(name="/test")
     */
    public function nonameAction()
    {
        // dummy method with a @Action annotation
    }
}
