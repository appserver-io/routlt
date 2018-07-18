<?php

/**
 * AppserverIo\Routlt\Annotations\Result
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
 * Annotation to map a string to a action result.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class Result extends AbstractBeanAnnotation
{

    /**
     * The value of the type attribute.
     *
     * @var string
     */
    protected $type;

    /**
     * The value of the code attribute.
     *
     * @var string
     */
    protected $code;

    /**
     * The value of the result attribute.
     *
     * @var string
     */
    protected $result;

    /**
     * The constructor the initializes the instance with the
     * data passed with the token.
     *
     * @param array $values The annotation values
     */
    public function __construct(array $values = array())
    {

        // set the type attribute, if available
        if (isset($values[AnnotationKeys::TYPE])) {
            $this->type = $values[AnnotationKeys::TYPE];
        }

        // set the code attribute, if available
        if (isset($values[AnnotationKeys::CODE])) {
            $this->code = $values[AnnotationKeys::CODE];
        }

        // set the result attribute, if available
        if (isset($values[AnnotationKeys::RESULT])) {
            $this->result = $values[AnnotationKeys::RESULT];
        }

        // pass the arguements through to the parent instance
        parent::__construct($values);
    }

    /**
     * Returns the value of the type attribute.
     *
     * @return string|null The annotations type attribute
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value of the result attribute.
     *
     * @return string|null The annotations result attribute
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns the value of the code attribute.
     *
     * @return string|null The annotations code attribute
     */
    public function getCode()
    {
        return $this->code;
    }
}
