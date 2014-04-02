<?php
namespace anet\conf;

/**
 * Primary configuration file for your project.
 *
 * @author      Jon Belelieu
 * @link        http://www.ascadnetworks.com/
 * @license     GNU General Public License v3.0
 * @link        http://www.gnu.org/licenses/gpl.html
 * @date        2013-12-05
 * @version     v1.0
 * @project     ANET Framework
 */

/**
 * Server settings
 */
error_reporting(E_ALL);


/**
 * General settings
 */

// Program language
const LANGUAGE      = 'en';

// Path to your app root.
const ROOT          = 'C:/wamp/www/MemberGuardian2/app';
const BACKEND       = 'C:/wamp/www/MemberGuardian2/app/core/backend';
const FRONTEND      = 'C:/wamp/www/MemberGuardian2/app/core/frontend';

// URLs for your app.
const URL           = 'http://localhost/MemberGuardian2/web';

// Determines what environment we
// are operating under.
// Dev   =  Development
// Prod  =  Production
const ENVIRONMENT   = 'Dev';

// Determines whether the app should
// be available online.
const ACTIVE_WEB    = false;

// Sets the virtual name of admin dashboard
// folder. Cannot be "dashboard".
const ADMIN_FOLDER    = 'admin';


/**
 * Database connection details.
 */

// Type of database. Should be formatted
// according to PDO driver standards:
// http://php.net/manual/en/pdo.drivers.php
const DB_TYPE = 'PDO_MYSQL';

// Host for the database.
const DB_HOST = 'localhost';

// Name of the database
const DB_NAME = '';

// Username for the database.
const DB_USER = 'root';

// Password for the database.
const DB_PASS = '';

// Database access port.
const DB_PORT = '';

// Prefix to append to all table names.
const DB_PREFIX = 'anet_';


/**
 * Custom configuration options.
 */