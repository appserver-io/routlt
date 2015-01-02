<?php

/**
 * AppserverIo\Routlt\Description\DirectoryParser
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Library
 * @package    Routlt
 * @subpackage Descriptor
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Routlt\Description;

use AppserverIo\Routlt\Controller;
use AppserverIo\Routlt\Description\PathDescriptor;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Routlt\Annotations\Path;
use AppserverIo\Routlt\Annotations\Action;

/**
 * Parser to parse a directory for annotated action classes.
 *
 * @category   Library
 * @package    Routlt
 * @subpackage Descriptor
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class DirectoryParser
{

    /**
     * Injects the controller instance.
     *
     * @param \AppserverIo\Routlt\Controller $controller The controller instance
     *
     * @return void
     */
    public function injectController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Returns the controller instance.
     *
     * @return \AppserverIo\Routlt\Controller The controller instance
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Parses the passed directory for action classes that has to be registered
     * in the controller servlet.
     *
     * @param string $directory The directory to parse
     *
     * @return void
     */
    public function parse($directory)
    {

        // check if we've found a valid directory
        if (is_dir($directory) === false) {
            return;
        }

        // check directory for classes we want to register
        $phpFiles = $this->globDir($directory . DIRECTORY_SEPARATOR . '*.php');

        // iterate all php files
        foreach ($phpFiles as $phpFile) {

            try {

                // cut off the META-INF directory and replace OS specific directory separators
                $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($directory, '', $phpFile));

                // now cut off the .php extension
                $className = substr($relativePathToPhpFile, 0, -4);

                // we need a reflection class to read the annotations
                $reflectionClass = $this->newReflectionClass($className);

                // load the path descriptor and add it to the controller
                if ($pathDescriptor = PathDescriptor::newDescriptorInstance()->fromReflectionClass($reflectionClass)) {
                    $this->getController()->addPathDescriptor($pathDescriptor);
                }

            } catch (\Exception $e) { // if class can not be reflected continue with next class

                // proceed with the nexet bean
                continue;
            }
        }
    }

    /**
     * Returns a reflection class instance for the passed class name.
     *
     * @param string $className The class name to return the reflection instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {

        // initialize the array with the annotations we want to ignore
        $annotationsToIgnore = array(
            'author',
            'package',
            'license',
            'copyright',
            'param',
            'return',
            'throws',
            'see',
            'link'
        );

        // initialize the array with the aliases for the enterprise bean annotations
        $annotationAliases = array(
            Path::ANNOTATION => Path::__getClass(),
            Action::ANNOTATION  => Action::__getClass()
        );

        // return the reflection class instance
        return new ReflectionClass($className, $annotationsToIgnore, $annotationAliases);
    }

    /**
     * Recursively parses and returns the directories that matches the passed
     * glob pattern.
     *
     * @param string  $pattern The glob pattern used to parse the directories
     * @param integer $flags   The flags passed to the glob function
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     */
    public function globDir($pattern, $flags = 0)
    {

        // parse the first directory
        $files = glob($pattern, $flags);

        // parse all subdirectories
        foreach (glob(dirname($pattern). DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->globDir($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
        }

        // return the array with the files matching the glob pattern
        return $files;
    }
}
