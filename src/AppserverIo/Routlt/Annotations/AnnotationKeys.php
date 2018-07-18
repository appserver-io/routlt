<?php

/**
 * AppserverIo\Routlt\Annotations\AnnotationKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Routlt\Annotations;

use Prophecy\Doubler\Generator\ClassCodeGenerator;

/**
 * Utility with annotation keys.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 *
 * @Annotation
 */
class AnnotationKeys
{

    /**
     * Key for the annotation property 'name'.
     *
     * @var string
     */
    const NAME = 'name';

    /**
     * Key for the annotation property 'restrictions'.
     *
     * @var string
     */
    const RESTRICTIONS = 'restrictions';

    /**
     * Key for the annotation property 'defaults'.
     *
     * @var string
     */
    const DEFAULTS = 'defaults';

    /**
     * Key for the annotation property 'type'.
     *
     * @var string
     */
    const TYPE = 'type';

    /**
     * Key for the annotation property 'result'.
     *
     * @var string
     */
    const RESULT = 'result';

    /**
     * Key for the annotation property 'results'.
     *
     * @var string
     */
    const RESULTS = 'results';

    /**
     * Key for the annotation property 'code'.
     *
     * @var string
     */
    const CODE = 'code';
}
