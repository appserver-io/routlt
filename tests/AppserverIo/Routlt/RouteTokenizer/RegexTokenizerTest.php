<?php

/**
 * AppserverIo\Routlt\RouteTokenzier\RegexTokenizerTest
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

namespace AppserverIo\Routlt\RouteTokenizer;

/**
 * Test implementation for the RouteTokenizer implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class RegexTokenizerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The tokenizer to test.
     *
     * @var AppserverIo\Routlt\RouteTokenzier\RegexTokenizer
     */
    protected $tokenizer;

    /**
     * Initializes the interceptor to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->tokenizer = new RegexTokenizer();
    }

    /**
     * Tests tokenzing a route without a placeholder.
     *
     * @return void
     */
    public function testMatchWithoutPlaceholder()
    {
        $this->tokenizer->compile('/products');
        $this->assertTrue($this->tokenizer->match('/products'));
        $this->assertEquals('/^\/products$/', $this->tokenizer->getCompiledRegex());
        $this->assertEquals('products', $this->tokenizer->getControllerName());
        $this->assertNull($this->tokenizer->getMethodName());
        $this->assertEquals(array(), $this->tokenizer->getRequestParameters());
    }

    /**
     * Tests tokenzing a not matching route without a placeholder.
     *
     * @return void
     */
    public function testNoMatchWithoutPlaceholder()
    {
        $this->tokenizer->compile('/products');
        $this->assertFalse($this->tokenizer->match('/product'));
        $this->assertEquals('/^\/products$/', $this->tokenizer->getCompiledRegex());
        $this->assertEquals('products', $this->tokenizer->getControllerName());
        $this->assertNull($this->tokenizer->getMethodName());
        $this->assertEquals(array(), $this->tokenizer->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly one placeholder.
     *
     * @return void
     */
    public function testMatchWithOnePlaceholder()
    {
        $this->tokenizer->compile('/products/view/:id');
        $this->assertTrue($this->tokenizer->match('/products/view/1'));
        $this->assertEquals('/^\/products\/view\/(?<id>.*)$/', $this->tokenizer->getCompiledRegex());
        $this->assertEquals('products', $this->tokenizer->getControllerName());
        $this->assertEquals('view', $this->tokenizer->getMethodName());
        $this->assertEquals(array('id' => 1), $this->tokenizer->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly two placeholders.
     *
     * @return void
     */
    public function testMatchWithTwoPlaceholders()
    {
        $this->tokenizer->compile('/cart/add/:sku/:qty');
        $this->assertTrue($this->tokenizer->match('/cart/add/product-1/10'));
        $this->assertEquals('/^\/cart\/add\/(?<sku>.*)\/(?<qty>.*)$/', $this->tokenizer->getCompiledRegex());
        $this->assertEquals('cart', $this->tokenizer->getControllerName());
        $this->assertEquals('add', $this->tokenizer->getMethodName());
        $this->assertEquals(array('sku' => 'product-1', 'qty' => '10'), $this->tokenizer->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly one placeholder with restriction.
     *
     * @return void
     */
    public function testMatchWithOnePlaceholderAndRestriction()
    {
        $this->tokenizer->compile('/products/view/:id', array('id' => '\d+'));
        $this->assertTrue($this->tokenizer->match('/products/view/1'));
        $this->assertEquals('/^\/products\/view\/(?<id>\d+)$/', $this->tokenizer->getCompiledRegex());
        $this->assertEquals('products', $this->tokenizer->getControllerName());
        $this->assertEquals('view', $this->tokenizer->getMethodName());
        $this->assertEquals(array('id' => '1'), $this->tokenizer->getRequestParameters());
    }

    /**
     * Tests tokenzing a route with exactly two placeholders with restrictions.
     *
     * @return void
     */
    public function testMatchWithTwoPlaceholdersAndRestrictions()
    {
        $this->tokenizer->compile('/cart/add/:sku/:qty', array('sku' => 'product\-\d+', 'qty' => '\d+'));
        $this->assertTrue($this->tokenizer->match('/cart/add/product-1/10'));
        $this->assertEquals('/^\/cart\/add\/(?<sku>product\-\d+)\/(?<qty>\d+)$/', $this->tokenizer->getCompiledRegex());
        $this->assertEquals('cart', $this->tokenizer->getControllerName());
        $this->assertEquals('add', $this->tokenizer->getMethodName());
        $this->assertEquals(array('sku' => 'product-1', 'qty' => '10'), $this->tokenizer->getRequestParameters());
    }
}
