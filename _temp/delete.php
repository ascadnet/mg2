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

require "../../system/app.php";
$app = new App();

if (empty($_POST['scope'])) {
    $language = new language('S002');
    echo "0+++" . $language;
    exit;
} else {
    $scope = new $_POST['scope']('', '', $_POST);
    if (! method_exists($scope,'delete')) {
        $language = new language('S003');
        echo "0+++" . $language;
        exit;
    } else {
        $failed = '';
        $worked = '';
        foreach ($_POST['id'] as $id => $yes_no) {
            $del = $scope->delete($id);
            if ($del == 1) {
                $worked[] = $id;
            } else {
                $failed[] = $id;
            }
        }
        echo "1+++" . json_encode($worked) . "+++" . json_encode($failed);
        exit;
    }
}
