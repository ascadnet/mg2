<?php
/**
 * Database Class
 *
 * This class is included in this project
 * but belongs to the "Ascad Networks Framework".
 * While the overall project is copyrighted to
 * "Penn Foster", the contents of this file are
 * distributed under the "GPL3" license:
 * http://www.gnu.org/licenses/gpl.html
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @version     v1.0
 * @project     Penn Foster Forms
 */

class db
{

    protected $connection;
    protected $table;
    protected $command;
    protected $data;
    protected $inclusive;
    protected $binding;
    protected $clean_values;
    public $bridge;
    public $query;
    public $result;
    public $error;

    /**
     * @param string $table Name of the table we are inserting into, without prefix.
     * @param string $command Command we are running
     * @param array $data Data we are inserting, updating, etc.
     *      $data = array(
     *          'keys' => array('first_name','last_name'),
     *          'values' => array('John','Doe'),
     *          'select' => '*',
     *          'where' => array(
     *              'id'=>'1',
     *              'x'=>'>=1',
     *              'y'=>'<=1',
     *          ), // Other options:  = >= <= != > < ~
     *          'order' => '[FIELD_NAME_TO_ORDER_BY] [ASC|DESC]',
     *          'limit' => 'LIMIT CONTROLS',
     *          'join' => array('table2','table3'),
     *          'join_on' => array('table2.id','table3.id'),
     *          'query' => 'FULL QUERY HERE'
     *      );
     */
    function __construct($table,$command,$data,$inclusive = 'AND')
    {
        $this->table = $this->determine_table($table);
        $this->command = $command;
        $this->check_inclusive($inclusive);
        $this->data = $data;
        $this->connect();
        $this->prepare();
    }

    /**
     * Connect to the database
     */
    function connect()
    {
        $this->connection = new PDO(
            "mysql:host=" . PF_MYSQL_HOST . ";
            dbname=" . PF_MYSQL_DB, PF_MYSQL_USER, PF_MYSQL_PASS
        );
    }

    /**
     * Prepare and route the command.
     */
    function prepare()
    {
        if ($this->command == 'insert') {
            $this->db_insert();
        }
        else if ($this->command == 'update') {
            $this->db_update();
        }
        else if ($this->command == 'delete') {
            $this->db_delete();
        }
        else if ($this->command == 'get_rows' || $this->command == 'get_row' || $this->command == 'get') {
            $this->db_get_rows();
        }
        else if ($this->command == 'count') {
            $this->db_count();
        }
        else if ($this->command == 'query') {
            $this->db_run_query();
        }
    }

    /**
     * Prepare a query for inserts
     */
    function prepare_insert()
    {
        // Prep query
        $this->query = "INSERT INTO ";
        $this->query .= "`" . $this->table . "`";
        $this->query .= " (";
        $this->query .= $this->prep_keys();
        $this->query .= ")";
        $this->query .= " VALUES (";
        $this->query .= $this->prep_values();
        $this->query .= ")";
    }

    /**
     * Insert into the database.
     * Required in the data array:
     * 'keys','values'
     */
    function db_insert()
    {
        $this->prepare_insert();
        $bridge = $this->connection->prepare($this->query);
        $result = $bridge->execute($this->binding);
        if (! $result) {
        	$this->bridge = $bridge;
            $this->db_error();
        } else {
            $this->clear_binding();
            $this->error = '0';
            $this->result = $this->connection->lastInsertId();
        }
    }


    /**
     * Prepare a query for updates
     */
    function prepare_update()
    {
        // Prep query
        $this->query = "UPDATE ";
        $this->query .= "`" . $this->table . "`";
        $this->query .= " SET ";
        $this->query .= $this->prep_update_keys();
        $this->query .= " WHERE " . $this->build_where();
        $this->query .= $this->set_limit();
    }

    /**
     * Insert into the database.
     * Required in the data array:
     * 'keys','values'
     */
    function db_update()
    {
        $this->prepare_update();
        $bridge = $this->connection->prepare($this->query);
        $result = $bridge->execute($this->binding);
        if (! $result) {
        	$this->bridge = $bridge;
            $this->db_error();
        } else {
            $this->clear_binding();
            $this->result = $bridge->rowCount();
            $this->error = '0';
        }
    }


    /**
     * Prepare a query for updates
     */
    function prepare_delete()
    {
        // Prep query
        $this->query = "DELETE FROM ";
        $this->query .= "`" . $this->table . "`";
        $this->query .= " WHERE " . $this->build_where();
        $this->query .= $this->set_limit();
    }

    /**
     * Insert into the database.
     * Required in the data array:
     *
     */
    function db_delete()
    {
        $this->prepare_delete();
        $bridge = $this->connection->prepare($this->query);
        $result = $bridge->execute($this->binding);
        if (! $result) {
        	$this->bridge = $bridge;
            $this->db_error();
        } else {
            $this->clear_binding();
            $this->result = $bridge->rowCount();
            $this->error = '0';
        }
    }


    /**
     * Prepare a query for inserts
     */
    function prepare_get_row($counting = '0')
    {
        // Where submitted?
        if (! empty($this->data['query'])) {
            $this->query = $this->data['query'];
        } else {
            if (empty($this->data['where'])) {
                $this->data['where'] = '1';
            }
            // Prep query
            $this->query = $this->build_join($counting);
            $this->query .= " WHERE " . $this->build_where();

            if ($counting != '1') {
                $this->query .= $this->set_order();
                $this->query .= $this->set_limit();
            }
        }
    }


    /**
     * Get a single row from the database
     */
    function db_get_rows()
    {
        $this->prepare_get_row();
        $bridge = $this->connection->prepare($this->query);
        $result = $bridge->execute($this->binding);
        if (! $result) {
        	$this->bridge = $bridge;
            $this->db_error();
        } else {
            $bridge->setFetchMode(PDO::FETCH_ASSOC);
            if ($bridge->rowCount() == 1) {
                $this->result = $bridge->fetch();
            } else {
                while ($row = $bridge->fetch()) {
                    $this->result[] = $row;
                }
            }
            $this->clear_binding();
            $this->error = '0';
        }
    }

    /**
     * Count rows in the database.
     */
    function db_count()
    {
        $this->prepare_get_row('1');
        $bridge = $this->connection->prepare($this->query);
        $result = $bridge->execute($this->binding);
        if (! $result) {
        	$this->bridge = $bridge;
            $this->db_error();
        } else {
            $this->result = $bridge->fetchColumn();
            $this->clear_binding();
            $this->error = '0';
        }
    }


    /**
     * Run a query.
     * For advanced users.
     */
    function db_run_query()
    {
        if (! empty($this->data['query'])) {
            $this->query = $this->data['query'];
            $bridge = $this->connection->prepare($this->query);
            $result = $bridge->execute();
            if (! $result) {
        	$this->bridge = $bridge;
                $this->db_error();
            } else {
                $this->result = $result;
                $this->error = '0';
                $this->clear_binding();
            }
        }
    }


    /**
     * Sanitize without adding to bindings
     */
    function sanitize($string)
    {
        $clean = $this->connection->quote($string);
        return trim($clean,"'");
    }


    /**
     * Clear binding.
     */
    function clear_binding()
    {
        $this->binding = array();
        $this->clean_values = '';
    }


    /**
     * Throw a DB error.
     */
    function db_error()
    {
        // Flag error
        $this->error = '1';
        $details = $this->bridge->errorInfo();
        // Populate result
        $this->result = "<div style=\"";
        $this->result .= system_styles();
        $this->result .= "\"><div style=\"padding:24px;\">Execution stopped: Invalid MySQL query!<hr />";
        $this->result .= "<h1>Query</h1>";
        $this->result .= $this->query;
        $this->result .= "<hr />";
        $this->result .= ltrim($this->clean_values,',');
        $this->result .= "<hr />";
        $this->result .= "<h1>Error</h1>";
        $this->result .= '<ul>';
        if (! empty($details['0'])) {
            $this->result .= '<li>' . $details['0'] . '</li>';
        }
        if (! empty($details['1'])) {
            $this->result .= '<li>' . $details['1'] . '</li>';
        }
        if (! empty($details['2'])) {
            $this->result .= '<li>' . $details['2'] . '</li>';
        }
        $this->result .= '</ul>';
        $this->result .= "</div></div>";
        // Clear binding
        $this->clear_binding();
    }


    function set_limit()
    {
        if (! empty($this->data['limit'])) {
            return " LIMIT " . $this->data['limit'];
        } else {
            return '';
        }
    }


    function set_order()
    {
        if (! empty($this->data['order'])) {
            return " ORDER BY " . $this->data['order'];
        } else {
            return '';
        }
    }


    function build_join($counting)
    {
        $statement = "SELECT ";
        if (! empty($this->data['join'])) {
            if ($counting == '1') {
                $statement .= " COUNT(*)";
            } else {
                if (! empty($this->data['keys'])) {
                    $statement .= $this->prep_join_keys();
                } else {
                    $statement .= '*';
                }
            }
            $statement .= " FROM `" . $this->table . "`";
            $statement .= " JOIN ";
            $statement .= $this->prep_join_tables();
        } else {
            if ($counting == '1') {
                $statement .= " COUNT(*)";
            } else {
                if (! empty($this->data['keys'])) {
                    $statement .= $this->prep_keys();
                } else {
                    $statement .= "*";
                }
            }
            $statement .= " FROM `" . $this->table . "`";
        }
        return $statement;
    }


    function prep_join_tables()
    {
        $joins = '';
        $pos = 0;
        foreach ($this->data['join'] as $aTable) {
            if ($pos != 0) {
                $sani_table = $this->sanitize($this->determine_table($aTable));
                $joins .= " `" . $sani_table . "`";
                $joins .= " ON " . $this->table . '.' . $this->data['join_on']['0'] . '=' . $sani_table . '.' . $this->sanitize($this->data['join_on'][$pos]);
            }
            $pos++;
        }
        return $joins;
    }


    function prep_keys()
    {
        $keys = '';
        foreach ($this->data['keys'] as $aKey) {
            $keys .= ",`" . $this->sanitize($aKey) . "`";
        }
        return substr($keys,1);
    }


    function prep_join_keys()
    {
        $keys = '';
        foreach ($this->data['keys'] as $aKey => $table) {
            $keys .= "," . $this->sanitize($table) . '.' . $this->sanitize($aKey) . "";
        }
        return substr($keys,1);
    }


    function check_inclusive($inclusive) {
        if (strtoupper(trim($inclusive)) == 'AND') {
            $this->inclusive = 'AND';
        }
        else if (strtoupper(trim($inclusive)) == 'OR') {
            $this->inclusive = 'OR';
        }
        else {
            $this->inclusive = 'AND';
        }
    }


    /**
     * @param array $force_array
     *              This function accepts arrays within the $this->data['where'] clause.
     *              In the event that an array is being used, we force the program to use
     *              that instead of the standard $this->data['where'].
     *              This feature comes in handy when dealing with where clauses that have
     *              multiple conditions on the same key.
     *              Example:
     *              $this->data['where'] = array(
                        'name' => 'John',
     *                  'date' => array('
     *                      '>=2010-01-01',
     *                      '<=2013-02-08',
     *                  '),
     *              );
     *              Result:
     *              `name`='John' AND `date`>='2010-01-01' AND `date`<='2013-02-08'
     * @param string $force_key
     *              When dealing with a nested array in the $this->data['where'] clause,
     *              we need to force a key name for the program to use.
     * @return string
     */
    function build_where($force_array = '',$force_key = '')
    {
        $where = '';
        if (! empty($force_array)) {
            $use_array = $force_array;
        } else {
            $use_array = $this->data['where'];
        }
        if (empty($use_array) || $use_array == '1') {
            return '1';
        } else {
            foreach ($use_array as $key => $value) {
                if (! empty($force_key)) {
                    $key = $force_key;
                }
                if (is_array($value)) {
                    $where .= " " . $this->inclusive . " " . $this->build_where($value,$key);
                } else {
                    $where .= " " . $this->inclusive . " ";
                    $check_table = explode('.',$key);
                    $eq = $this->determine_eq($value);
                    if (! empty($check_table['1'])) {
                        $where .= "" . $this->determine_table($check_table['0']) . '.' . $this->sanitize($check_table['1']) . $eq['0'] . "'" . $this->sanitize($eq['1']) . "'";
                    } else {
                        $where .= "`" . $this->sanitize($key) . "`" . $eq['0'] . "'" . $this->sanitize($eq['1']) . "'";
                    }
                }
            }
            if ($this->inclusive == 'AND') {
                return substr($where,5);
            } else {
                return substr($where,4);
            }
        }
    }


    function determine_eq($value)
    {
        if (substr($value,0,2) == '!=') { return array('!=',substr($value,2)); }
        else if (substr($value,0,2) == '>=') { return array('>=',substr($value,2)); }
        else if (substr($value,0,2) == '<=') { return array('<=',substr($value,2)); }
        else if (substr($value,0,2) == '~') { return array(' LIKE ',substr($value,1)); }
        else if (substr($value,0,1) == '>') { return array('>',substr($value,1)); }
        else if (substr($value,0,1) == '<') { return array('<',substr($value,1)); }
        else if (substr($value,0,1) == '=') { return array('=',substr($value,1)); }
        else { return array('=',$value); }
    }


    function prep_values()
    {
        $values = '';
        foreach ($this->data['values'] as $aValue) {
            $this->binding[] = $aValue;
            $values .= ",?";
            $this->clean_values .= ",'" . $aValue . "'";
        }
        return substr($values,1);
    }



    function prep_update_keys()
    {
        $up_statement = '';
        $pos = 0;
        foreach ($this->data['keys'] as $aKey) {
            $this->binding[] = $this->data['values'][$pos];
            $up_statement .= ",`" . $this->sanitize($aKey) . "`=?";
            $pos++;
        }
        return substr($up_statement,1);
    }


    /**
     * @param string $table Determine if a prefix is needed for MySQL tables.
     */
    function determine_table($table)
    {
        if (defined('PF_PREFIX')) {
            return PF_PREFIX . $table;
        } else {
            return $table;
        }
    }

}
