<?php

/**
 * AppserverIo\Routlt\ActionMappingTest
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

namespace AppserverIo\Routlt;

/**
 * Test implementation for the ActionMapping implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class ActionMappingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The action mapping to test.
     *
     * @var AppserverIo\Routlt\ActionMapping
     */
    protected $actionMapping;

    /**
     * Initializes the action mapping to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->actionMapping = new ActionMapping();
    }

    /**
     * Tests tokenzing a route without a placeholder.
     *
     * @return void
     */
    public function testMatchWithoutPlaceholder()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->compile('/products');
        $this->assertTrue($this->actionMapping->match('/products'));
        $this->assertEquals('/^\/products$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertNull($this->actionMapping->getMethodName());
        $this->assertEquals(array(), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a not matching route without a placeholder.
     *
     * @return void
     */
    public function testNoMatchWithoutPlaceholder()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->compile('/products');
        $this->assertFalse($this->actionMapping->match('/product'));
        $this->assertEquals('/^\/products$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertNull($this->actionMapping->getMethodName());
        $this->assertEquals(array(), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly one placeholder.
     *
     * @return void
     */
    public function testMatchWithOnePlaceholder()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->setMethodName('/view');
        $this->actionMapping->compile('/products/view/:id');
        $this->assertTrue($this->actionMapping->match('/products/view/1'));
        $this->assertEquals('/^\/products\/view\/(?<id>.*)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertEquals('/view', $this->actionMapping->getMethodName());
        $this->assertEquals(array('id' => 1), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly one placeholder and a default value.
     *
     * @return void
     */
    public function testMatchWithOnePlaceholderAndDefaultValue()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->setMethodName('/view');
        $this->actionMapping->compile('/products/view/:id', array(), array('id' => 1));
        $this->assertTrue($this->actionMapping->match('/products/view'));
        $this->assertEquals('/^\/products\/view\/(?<id>.*)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertEquals('/view', $this->actionMapping->getMethodName());
        $this->assertEquals(array('id' => 1), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly two placeholders and default values.
     *
     * @return void
     */
    public function testMatchWithTwoPlaceholdersAndDefaultValues()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->setMethodName('/view');
        $this->actionMapping->compile('/products/view/:id/:qty', array('id' => '\d+', 'qty' => '\d+'), array('id' => 1, 'qty' => 10));
        $this->assertTrue($this->actionMapping->match('/products/view'));
        $this->assertEquals('/^\/products\/view\/(?<id>\d+)\/(?<qty>\d+)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertEquals('/view', $this->actionMapping->getMethodName());
        $this->assertEquals(array('id' => 1, 'qty' => 10), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly two placeholders and default values.
     *
     * @return void
     */
    public function testMatchWithTwoPlaceholdersOneMissingAndDefaultValues()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->setMethodName('/view');
        $this->actionMapping->compile('/products/view/:sku/:qty', array('sku' => '.*', 'qty' => '\d+'), array('qty' => 10));
        $this->assertTrue($this->actionMapping->match('/products/view/product-1'));
        $this->assertEquals('/^\/products\/view\/(?<sku>.*)\/(?<qty>\d+)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertEquals('/view', $this->actionMapping->getMethodName());
        $this->assertEquals(array('sku' => 'product-1', 'qty' => 10), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly two placeholders.
     *
     * @return void
     */
    public function testMatchWithTwoPlaceholders()
    {
        $this->actionMapping->setControllerName('/cart');
        $this->actionMapping->setMethodName('/add');
        $this->actionMapping->compile('/cart/add/:sku/:qty');
        $this->assertTrue($this->actionMapping->match('/cart/add/product-1/10'));
        $this->assertEquals('/^\/cart\/add\/(?<sku>.*)\/(?<qty>.*)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/cart', $this->actionMapping->getControllerName());
        $this->assertEquals('/add', $this->actionMapping->getMethodName());
        $this->assertEquals(array('sku' => 'product-1', 'qty' => '10'), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly one placeholder with restriction.
     *
     * @return void
     */
    public function testMatchWithOnePlaceholderAndRestriction()
    {
        $this->actionMapping->setControllerName('/products');
        $this->actionMapping->setMethodName('/view');
        $this->actionMapping->compile('/products/view/:id', array('id' => '\d+'));
        $this->assertTrue($this->actionMapping->match('/products/view/1'));
        $this->assertEquals('/^\/products\/view\/(?<id>\d+)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/products', $this->actionMapping->getControllerName());
        $this->assertEquals('/view', $this->actionMapping->getMethodName());
        $this->assertEquals(array('id' => '1'), $this->actionMapping->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly two placeholders with restrictions.
     *
     * @return void
     */
    public function testMatchWithTwoPlaceholdersAndRestrictions()
    {
        $this->actionMapping->setControllerName('/cart');
        $this->actionMapping->setMethodName('/add');
        $this->actionMapping->compile('/cart/add/:sku/:qty', array('sku' => 'product\-\d+', 'qty' => '\d+'));
        $this->assertTrue($this->actionMapping->match('/cart/add/product-1/10'));
        $this->assertEquals('/^\/cart\/add\/(?<sku>product\-\d+)\/(?<qty>\d+)$/', $this->actionMapping->getCompiledRegex());
        $this->assertEquals('/cart', $this->actionMapping->getControllerName());
        $this->assertEquals('/add', $this->actionMapping->getMethodName());
        $this->assertEquals(array('sku' => 'product-1', 'qty' => '10'), $this->actionMapping->getRequestParameters());
    }
}
