<?php
namespace anet\lib;

/**
 * Authentication and password class.
 *
 * @author      Jon Belelieu
 * @link        http://www.ascadnetworks.com/
 * @license     GNU General Public License v3.0
 * @link        http://www.gnu.org/licenses/gpl.html
 * @date        2013-12-05
 * @version     v1.0
 * @project     ANET Framework
 */

class Authenticate {

    public function __construct()
    {
        // In case we are not using PHP5.5+, we need to load
        // the a compatibility tool for using the PHP hashing
        // tools.
        require ROOT . '/app/lib/vendor/password_compat/password.php';
    }

    /**
     *
     * @param int $length   Desired length of the password.
     */
    public function generatePassword($length = 12)
	{

	}

    /**
     *
     * @param string $passwordInputted      Plain text inputted password.
     * @param string $passwordHash          Comparison hashed password.
     */
    public function checkPassword($passwordInputted, $passwordHash)
	{

	}

    /**
     *
     *
     * @param string $password  Plain text password.
     * @param string $salt      Salt.
     */
    public function encryptPassword($password, $salt)
	{

	}

	/**
     *
     * @param int $length   Desired length of the salt.
     */
    public function generateSalt($length = 6)
	{

	}

}