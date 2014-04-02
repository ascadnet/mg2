<?php

/**
 * Takes an array or object and converts it
 * into pre-formatted and readable text.
 *
 * @param Array/Object  $array
 */
function pa($array)
{
    echo "<PRE>";
    var_dump($array);
    echo "</PRE>";
}

/**
 * Takes an object and/or array
 * and formats it for use as a JSON
 * object throughout the dashboard.
 * If an array is passed in, it is
 * returned as an object.
 *
 * @param Array/Object  $data
 * @return json
 */
function json($data)
{
    if (is_array($data)) {
        if (! array_key_exists('error', $data)) {
            $data['error'] = '0';
        }

        $final = json_decode(json_encode($data), FALSE);
    } else {
        if (! isset($data->error)) {
            $data->error = '0';
        }
        $final = $data;
    }
    return json_encode($final);
}


/**
 * Get a language element.
 *
 * @param $id
 */
function get_language($id)
{
    $session = new Session();
    if (! empty($session->language)) {
        $lang = $session->language;
    } else {
        $lang = \anet\conf\LANGUAGE;
    }
    $query = array(
        'where' => array(
            'code' => $id,
            'lang' => $lang,
        ),
        'limit' => '1',
    );
    $get = new db('language', 'get', $query);
}