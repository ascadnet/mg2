<?php

namespace anet\lib;

class App
{

    /**
     * @param $code Gets language based on ID and displays error.
     * @param $custom_text Overrides language text.
     */
    function throw_error($code,$custom_text = '')
    {
        if (empty($custom_text)) {
            $error = $this->get_language($code);
            $custom_text = $error['text'];
        } else {
            $error['title'] = '';
        }
        $changes = array(
            'title' => $error['title'],
            'message' => $custom_text,
        );
        echo new template('error',$changes);
        exit;
    }


    /**
     * @param string $code Language code we are retrieving.
     * @return array 'text','title'
     */
    function get_language($code)
    {
        $query = array(
            'select' => 'text',
            'where' => array(
                'id' => $code,
            ),
            'limit' => '1',
        );
        $row = new db('language','get_rows',$query);
        return $row->result;
    }


    /**
     * @param array $array Array that we are converting into keys and values.
     * @param array $ignore Array of items to ignore.
     */
    function build_keys($array,$ignore = array('scope'))
    {
        $keys = array();
        $values = array();
        foreach ($array as $key => $value) {
            if (! in_array($key,$ignore)) {
                $keys[] = $key;
                $values[] = $value;
            }
        }
        return array(
            'keys' => $keys,
            'values' => $values,
        );
    }

    /**
     * @param string $id Get an option from the database.
     */
    function option($id)
    {
        $query = array(
            'where' => array(
                'id' => $id,
            ),
            'limit' => '1',
        );
        $opt = new db('options','get',$query);
        if ($opt->error != '1') {
            return $opt->result['value'];
        } else {
            return false;
        }
    }


}
