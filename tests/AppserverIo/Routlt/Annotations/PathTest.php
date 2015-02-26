<?php

/**
 * AppserverIo\Routlt\Annotations\PathTest
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
 * Test implementation for the @Path annotation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class PathTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The path annotation instance to test.
     *
     * @var \AppserverIo\Routlt\Annotations\Path
     */
    protected $annotation;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->annotation = new Path('Path', array('name' => '/index'));
    }

    /**
     * This test checks the resolved name.
     *
     * @return void
     */
    public function testGetName()
    {
        $this->assertSame('/index', $this->annotation->getName());
    }

    /**
     * This test checks the resolved class name.
     *
     * @return void
     */
    public function testGetClass()
    {
        $this->assertSame('AppserverIo\Routlt\Annotations\Path', $this->annotation->__getClass());
    }
}
