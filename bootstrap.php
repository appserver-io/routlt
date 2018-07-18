<?php

/**
 * boostrap.php
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

use Doctrine\Common\Annotations\AnnotationRegistry;

// configure the autoloader
$loader = require 'vendor/autoload.php';
$loader->add('AppserverIo\\Routlt', 'src');

// load the annotation registries for the annotation reader
AnnotationRegistry::registerAutoloadNamespaces(
    array(
        'AppserverIo\Routlt\Annotations' => __DIR__ . '/src',
        'AppserverIo\Psr\EnterpriseBeans\Annotations' => __DIR__ . '/vendor/appserver-io-psr/epb/src'
    )
);
