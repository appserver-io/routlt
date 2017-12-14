<?php

/**
 * AppserverIo\Routlt\Util\DescriptorAware
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
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Routlt\Util;

use AppserverIo\Psr\Deployment\DescriptorInterface;

/**
 * Interface for a descriptor aware class.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://github.com/appserver-io/routlt
 * @link      http://www.appserver.io
 */
interface DescriptorAware
{

    /**
     * Sets the descriptor instance.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $descriptor The descriptor instance
     *
     * @return void
     */
    public function setDescriptor(DescriptorInterface $descriptor);

    /**
     * Returns the descriptor instance.
     *
     * @return \AppserverIo\Psr\Deployment\DescriptorInterface The descriptor instance
     */
    public function getDescriptor();
}
