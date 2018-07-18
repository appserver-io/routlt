<?php

/**
 * AppserverIo\Routlt\Annotations\Action
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

/**
 * Annotation to map a request path info to an action method.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Action
{

    /**
     * The value of the name attribute.
     *
     * @var string
     */
    protected $name;

    /**
     * The value of the restrictions attribute.
     *
     * @var string
     */
    protected $restrictions;

    /**
     * The value of the defaults attribute.
     *
     * @var string
     */
    protected $defaults;

    /**
     * The constructor the initializes the instance with the
     * data passed with the token.
     *
     * @param array $values The annotation values
     */
    public function __construct(array $values = array())
    {

        // set the name attribute, if available
        if (isset($values[AnnotationKeys::NAME])) {
            $this->name = $values[AnnotationKeys::NAME];
        }

        // set the restrictions attribute, if available
        if (isset($values[AnnotationKeys::RESTRICTIONS])) {
            $this->restrictions = $values[AnnotationKeys::RESTRICTIONS];
        }

        // set the defaults attribute, if available
        if (isset($values[AnnotationKeys::DEFAULTS])) {
            $this->defaults = $values[AnnotationKeys::DEFAULTS];
        }
    }

    /**
     * Returns the value of the name attribute.
     *
     * @return string|null The annotations name attribute
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of the restrictions attribute.
     *
     * @return string|null The annotations restrictions attribute
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Returns the value of the defaults attribute.
     *
     * @return string|null The annotations defaults attribute
     */
    public function getDefaults()
    {
        return $this->defaults;
    }
}
