<?php

/**
 * Program management.
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @version     v1.0
 * @project     Penn Foster Forms
 */

class language extends app
{

    protected $id;
    protected $permission;
    protected $primary_table = 'language';
    protected $scope = 'language';
    public $error;
    public $error_details;
    public $ajax_reply;
    public $data;


    function __construct($id = '',$act = 'get',$data_in = array())
    {
        $permissions = new permissions($this->scope,$act,$id);
        if ($permissions->error == '1') {
            $this->error = '1';
            $this->error_details = $permissions->error_details;
        } else {
            // Continue
            $this->id = $id;
            $this->data = $data_in;
            if (! empty($act)) {
                $this->$act();
            }
        }
    }


    function get()
    {
        $query = array(
            'where' => array(
                'id' => $this->id,
            ),
            'limit' => '1',
        );
        $row = new db($this->primary_table,'get_rows',$query);
        if (! empty($row->result)) {
            $this->data = $row->result;
        } else {
            $this->error = '1';
            $this->error_details = 'S004';
        }
    }


    function edit()
    {
        $ignore = array('scope','id');
        $keys = $this->build_keys($this->data,$ignore);
        $query = array(
            'keys' => $keys['keys'],
            'values' => $keys['values'],
            'where' => array(
                'id' => $this->id,
            ),
            'limit' => '1',
        );
        $update = new db($this->primary_table,'update',$query);
        if ($update->error == '1') {
            $this->error = '1';
            $this->error_details = 'S003';
        } else {
            $this->ajax_reply = json_encode($this->data);
        }
    }

    function add()
    {
        $ignore = array('scope','id');
        $keys = $this->build_keys($this->data,$ignore);
        $query = array(
            'keys' => $keys['keys'],
            'values' => $keys['values'],
        );
        $insert = new db($this->primary_table,'insert',$query);
        if ($insert->error == '1') {
            $this->error = '1';
            $this->error_details = 'S005';
        } else {
            $this->ajax_reply = json_encode($this->data);
        }
    }

}
