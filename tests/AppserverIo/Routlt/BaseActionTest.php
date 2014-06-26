<?php

/**
 * AppserverIo\Routlt\BaseActionTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt;

/**
 * This is test implementation for the abstract base action implementation.
 *
 * @category  Library
 * @package   Routlt
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
class BaseActionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The abstract action instance to test.
     *
     * @var \AppserverIo\Routlt\BaseAction
     */
    protected $action;

    /**
     * Initializes the base context to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->action = $this->getMockForAbstractClass('\AppserverIo\Routlt\BaseAction', new BaseContext());
    }

    /**
     * This test checks the resolved class name.
     *
     * @return void
     */
    public function testGetContext()
    {
        $this->action = $this->getMockForAbstractClass('\AppserverIo\Routlt\BaseAction', $baseContext = new BaseContext());
        $this->assertSame($baseContext, $this->action->getContext());
    }
}
