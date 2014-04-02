<?php

/**
 *
 *
 * @project     SREDengine
 * @link        http://www.sredengine.com/
 *
 * This file is built on the "Ascad Framework". All
 * elements of the Ascad Framework remain copyrighted
 * to their respective owner.
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @license     Commercial
 * @link        http://www.ascadnetworks.com/Framework/License
 * @date        4/22/13
 * @version     v1.0
 * @project     Ascad Framework
 */

require "../system/app.php";
$app = new App();

// Check the session
$session = new session();
if ($session->session_error == '1') {
    header('Location: https://www.sredengine.com/Clients/?se=ses_home_error');
    exit;
}

define('PF_USER',$session->user_id);
define('PF_CLIENT',$session->client_id);
define('PF_USER_LANGUAGE',$session->language);

// Permission to load this page?
$disallowed = array( );
if (
    $session->permissions['id'] == '1' ||
    $session->permissions['id'] == '5'
) {
    // Nothing yet...
}
else if ($session->permissions['id'] == '2') {
    $disallowed = array(
        'Plan',
        'Uncertainty',
        'User',
        'Project',
        'Uncertainty',
        'Settings',
    );
}
else if ($session->permissions['id'] == '3') {
    $disallowed = array(
        'Plan',
        'User',
        'Settings',
    );
}
else if ($session->permissions['id'] == '4') {
    $disallowed = array(
        'Employee',
        'Subcontractor',
        'Material',
        'Plan',
        'Report',
        'User',
        'Settings',
    );
}

// Load the correct page.
$site = new site('',$session);

if (in_array($site->folder, $disallowed)) {
    header('Location: /app/admin');
    exit;
}

echo $site->content;
exit;