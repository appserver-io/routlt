<?php

/**
 * AppserverIo\Routlt\Annotations\Path
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

use AppserverIo\Psr\EnterpriseBeans\Annotations\AbstractBeanAnnotation;

/**
 * Annotation to map a request path info to an action class.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("results", type="array<AppserverIo\Routlt\Annotations\Result>"),
 * })
 */
class Path extends AbstractBeanAnnotation
{

    /**
     * The annotation to define a servlets routing.
     *
     * @var string
     */
    const ANNOTATION = 'Path';

    /**
     * The array with the action results.
     *
     * @var array<AppserverIo\Routlt\Annotations\Result>
     */
    protected $results = array();

    /**
     * This method returns the class name as
     * a string.
     *
     * @return string
     */
    public static function __getClass()
    {
        return __CLASS__;
    }

    /**
     * The constructor the initializes the instance with the
     * data passed with the token.
     *
     * @param array $values The annotation values
     */
    public function __construct(array $values = array())
    {

        // set the inner result annotations, if available
        if (isset($values[AnnotationKeys::RESULTS])) {
            $this->results = $values[AnnotationKeys::RESULTS];
        }

        // pass the values through to the parent class
        parent::__construct($values);
    }

    /**
     * Returns the array with the inner result annotations.
     *
     * @return array<AppserverIo\Routlt\Annotations\Result> The inner result annotations
     */
    public function getResults()
    {
        return $this->results;
    }
}
