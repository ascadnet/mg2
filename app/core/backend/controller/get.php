<?php

namespace anet\core\backend\controller;

class get
{

    private $object;
    public $data;

    function __construct($scope, $id)
    {
        if ($this->object = new $scope($id, 'get')) {
            $this->data = $this->object->data;
        } else {
            $this->data = '';
        }
    }

}
