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
 */
class Result extends AbstractBeanAnnotation
{

    /**
     * The annotation to define a servlets routing.
     *
     * @var string
     */
    const ANNOTATION = 'Result';

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
     * Returns the value of the type attribute.
     *
     * @return string|null The annotations type attribute
     */
    public function getType()
    {
        if (isset($this->values['type'])) {
            return $this->values['type'];
        }
    }

    /**
     * Returns the value of the result attribute.
     *
     * @return string|null The annotations result attribute
     */
    public function getResult()
    {
        if (isset($this->values['result'])) {
            return $this->values['result'];
        }
    }

    /**
     * Returns the value of the code attribute.
     *
     * @return string|null The annotations code attribute
     */
    public function getCode()
    {
        if (isset($this->values['code'])) {
            return $this->values['code'];
        }
    }
}
