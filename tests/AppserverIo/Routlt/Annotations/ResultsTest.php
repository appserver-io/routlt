<?php

/**
 * AppserverIo\Routlt\Annotations\ResultsTest
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
 * Test implementation for the @Results annotation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ResultsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The path annotation instance to test.
     *
     * @var \AppserverIo\Routlt\Annotations\Results
     */
    protected $annotation;

    /**
     * Initializes the base action to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->annotation = new Results('Results', array());
    }

    /**
     * This test checks that no results are resolved.
     *
     * @return void
     */
    public function testGetResults()
    {
        $this->assertNull($this->annotation->getResults());
    }

    /**
     * This test checks that results are resolved.
     *
     * @return void
     */
    public function testGetResultsWithData()
    {

        // prepare a result
        $results = array(
            new Result('Result', array('name' => 'success', 'type' => 'DummyResult', 'result' => 'my_template.phtml'))
        );

        // set the result
        $this->annotation->setValue(0, $results);
        $this->assertSame($results, $this->annotation->getResults());
    }

    /**
     * This test checks the resolved class name.
     *
     * @return void
     */
    public function testGetClass()
    {
        $this->assertSame('AppserverIo\Routlt\Annotations\Results', $this->annotation->__getClass());
    }
}
