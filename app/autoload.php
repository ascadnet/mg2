<?php

/**
 * Autoloader functions.
 * Built according to the PSR standards.
 *
 * @author      Jon Belelieu
 * @link        http://www.ascadnetworks.com/
 * @license     GNU General Public License v3.0
 * @link        http://www.gnu.org/licenses/gpl.html
 * @date        2013-12-05
 * @version     v1.0
 * @project     ANET Framework
 */

spl_autoload_register('autoloadCore');
spl_autoload_register('autoloadLib');


/**
 * Load a library. First try vendor libraries,
 * then move on the local libraries.
 *
 * @param $className String     Name of the class we are loading.
 *                              Should include a qualified namespace.
 */
function autoloadLib($className)
{
    // Get the filename
    $fileName = getFilename(ltrim($className, '\\'));
    // Append app root.
    $check = anet\conf\ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $fileName;
    if (is_readable($check)) {
        require $check;
    } else {
        $aName = explode(DIRECTORY_SEPARATOR, $fileName);
        $lib_try = array_pop($aName);
        $check = anet\conf\ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $lib_try;
        if (is_readable($check)) {
            require $check;
        }
    }
}


/**
 * Load a core class.
 *
 * @param $className String     Name of the class we are loading.
 *                              Should include a qualified namespace.
 */
function autoloadCore($className)
{
    // Remove the vendor name for
    // core classes
    $exp = explode(DIRECTORY_SEPARATOR, $className);
    array_shift($exp);
    $className = implode(DIRECTORY_SEPARATOR, $exp);
    // Get the filename
    $fileName = getFilename(ltrim($className, '\\'));
    // Append app root.
    $check = anet\conf\ROOT . DIRECTORY_SEPARATOR . $fileName;
    if (is_readable($check)) {
        require $check;
    }
}


/**
 * Get the filename to load from the
 * namespace provided. Basically took
 * the PSR standard autoloader and
 * isolated this snippet to make it
 * reusable among the different autoloaders.
 *
 * @param $className
 *
 * @return string
 */
function getFilename($className)
{
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    return $fileName;
}