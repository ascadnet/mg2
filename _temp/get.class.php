<?php
/**
 * Gets content for population of a popup form.
 * It does this by checking if a class exists
 * for the current scope, and running that
 * scope's get() function to return data.
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @version     v1.0
 * @project     Penn Foster Forms
 */

class get
{

    private $object;
    public $data;

    function __construct($scope,$id)
    {
        if ($this->object = new $scope($id,'get')) {
            $this->data = $this->object->data;
        } else {
            $this->data = '';
        }
    }

}
