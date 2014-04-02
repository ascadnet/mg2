<?php
namespace anet\lib;

/**
 * Database Wrapper
 * This file is a PDO wrapper. It handles all binding
 * and error catching. It currently support MySQL
 * and PostgreSQL.
 *
 * @author      Jon Belelieu
 * @link        http://www.ascadnetworks.com/
 * @license     GNU General Public License v3.0
 * @link        http://www.gnu.org/licenses/gpl.html
 * @date        2013-12-05
 * @version     v1.0
 * @project     ANET Framework
 */

class DB {

    protected $query, $command, $table, $inclusive;
    protected $binding, $returnType, $queryData;
    protected $connection, $STH, $data, $prep, $counting;
    public $error = '0';
    public $errorDetails;

    /**
     * Construct the database request and
     * connect to the database.
     *
     * @param string $table         Name of the table we are querying. Not required
     *                              for complex queries.
     * @param string $command       Name of the command to run. Options are:
     *                              insert      Insert rows.
     *                              update      Update rows.
     *                              delete      Delete rows.
     *                              get_rows    Get multiple rows. Even if only one row
     *                                          matches, you will still receive it in a
     *                                          numbered object or array.
     *                              get         Get a single row.
     *                              count       Retrieve total results as int.
     *                              query       Fully prepare complex query.
     * @param array $queryData      Primary data for the request. The array elements
     *                              vary by command type, so reference the documentation
     *                              or the individual methods for details. An example
     *                              of all possibilities has been included below:
     *
     *      $queryData = array(
     *          'keys' => array('first_name','last_name'),
     *          'values' => array('John','Doe'),
     *          'select' => 'column1,column2',
     *          'where' => array(
     *              'id' => '1',
     *              'x'  => '>=1',
     *              'y'  => '<=1',
     *          ), // Other options:  = >= <= != > < ~
     *          'order' => '[FIELD_NAME_TO_ORDER_BY] [ASC|DESC]',
     *          'limit' => '[LIMIT CONTROLS]', // Example: 0,50
     *          'join' => array('table2','table3'),
     *          'join_main_id' => array('id'),
     *          'join_on' => array('table2.id','table3.id'),
     *          'query' => 'FULL QUERY HERE FRO COMPLEX QUERIES'
     *      );
     *
     * @param string $inclusive     Controls inclusion scope for queries. AND or OR.
     */
    function __construct($table = '', $command = '', $queryData = '', $inclusive = 'AND')
    {
        if (! empty($table)) { $this->setTable($table); }
        if (! empty($queryData)) { $this->setQueryData($queryData); }
        if (! empty($inclusive)) { $this->setInclusive($inclusive); }
        $this->setCommand($command);
        $this->connect();
    }


    /**
     * Sets the table for the query.
     *
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $this->determineTable($table);
    }


    /**
     * Sets the query data array.
     *
     * @param $query_data
     */
    public function setQueryData($queryData)
    {
        $this->data = $queryData;
    }


    /**
     * Set the conditions for a prepared query.
     *
     * @param $inclusive
     */
    public function setInclusive($inclusive)
    {
        switch (strtoupper($inclusive)) {
            case 'OR':
                $this->inclusive = 'OR';
            default:
                $this->inclusive = 'AND';
        }
    }


    /**
     * Sets the command we are running.
     *
     * @param $command
     */
    public function setCommand($command)
    {
        switch (strtolower($command)) {
            case 'insert':
                $this->prep = $this->prepInsert();
                $this->command = 'insert';
                break;
            case 'update':
                $this->prep = $this->prepUpdate();
                $this->command = 'update';
                break;
            case 'delete':
                $this->prep = $this->prepDelete();
                $this->command = 'delete';
                break;
            case 'get_rows':
                $this->returnType = 'multi';
                $this->prep = $this->prepareGet();
                $this->command = 'get';
                break;
            case 'get':
                $this->returnType = 'single';
                $this->counting = true;
                $this->prep = $this->prepareGet();
                $this->command = 'get';
                break;
            default:
                $this->command = 'fullQuery';
        }
    }


    /**
     * Connects to the database.
     * Currently support MySQL and PostgreSQL.
     * Actual database used for this project
     * should be set in /app/conf/config.php.
     */
    public function connect()
    {
        switch (\anet\conf\DB_TYPE) {
            case 'PDO_PGSQL':
                $this->connection = new \PDO(
                    "pgsql:host=" . \anet\conf\DB_HOST . ";
                    dbname=" . \anet\conf\DB_NAME . ";
                    user=" . \anet\conf\DB_USER . ";
                    password=" . \anet\conf\DB_PASS
                );
                break;
            default:
                $this->connection = new \PDO(
                    "mysql:host=" . \anet\conf\DB_HOST . ";
                    dbname=" . \anet\conf\DB_NAME . ",
                    " . \anet\conf\DB_USER . ",
                    " . \anet\conf\DB_PASS
                );
        }
    }


    /**
     * Disconnects from the database.
     */
    public function disconnect()
    {
        $this->connection = null;
        $this->binding = null;
    }


    /**
     * Determine the correct table name for the
     * request. Essentially ensures that the
     * prefix has been properly appended to the
     * table name, if there is one.
     *
     * @param $table
     *
     * @return string
     */
    protected function determineTable($table)
    {
        return \anet\conf\DB_PREFIX . str_replace(\anet\conf\DB_PREFIX, '', $table);
    }


    /**
     * Set the limit on the query.
     *
     * @return string
     */
    function setLimit()
    {
        if (! empty($this->data['limit'])) {
            return " LIMIT " . $this->data['limit'];
        } else {
            return '';
        }
    }


    /**
     * Set the order on a query.
     *
     * @return string
     */
    function setOrder()
    {
        if (! empty($this->data['order'])) {
            return " ORDER BY " . str_replace('`', '', $this->data['order']);
        } else {
            return '';
        }
    }


    /**
     * Prepare the command but
     * hold off on running it.
     */
    public function execute()
    {
        $this->command();
    }


    /**
     * Insert data into the database.
     */
    protected function insert()
    {
        // Complete the bindings
        $this->completeBinding();
        // Run the query.
        $result = $this->STH->execute();
        if (! $result) {
            $this->dbError();
        } else {
            $this->result = $result;
            $this->disconnect();
        }
    }

    /**
     * Prepare an insert query.
     */
    protected function prepInsert()
    {
        // Build the insert.
        $prep = $this->prepKeys();
        $this->query = "INSERT INTO ";
        $this->query .= $this->table;
        $this->query .= " (";
        $this->query .= $prep['keys'];
        $this->query .= ")";
        $this->query .= " VALUES (";
        $this->query .= $prep['values'];
        $this->query .= ")";
        // Complete the bindings
        $this->STH = $this->connection->prepare($this->query);
    }


    /**
     * Execute a delete query.
     */
    protected function delete()
    {
        // Complete the bindings
        $this->completeBinding();
        // Run the query.
        $result = $this->STH->execute();
        if (! $result) {
            $this->dbError();
        } else {
            $this->result = $this->STH->rowCount();
            $this->disconnect();
        }
    }

    /**
     * Prepare a delete query.
     */
    protected function prepDelete()
    {
        // Determine Where
        $where = $this->prepWhere();
        // Build the query
        $this->query = "DELETE FROM ";
        $this->query .= $this->table;
        $this->query .= " WHERE " . $where;
        $this->query .= $this->setLimit();
        // Complete the bindings
        $this->STH = $this->connection->prepare($this->query);
        return true;
    }


    /**
     * Execute an update query.
     */
    protected function update()
    {
        // Complete the bindings
        $this->completeBinding();
        // Run the query.
        $result = $this->STH->execute();
        if (! $result) {
            $this->dbError();
        } else {
            $this->result = $this->STH->rowCount();
            $this->disconnect();
        }
    }

    /**
     * Prepare an update statement.
     */
    protected function prepUpdate()
    {
        // Prep the keys and the where statement.
        $prep = $this->prepKeys();
        $where = $this->prepWhere();
        // Build the query.
        $this->query = "UPDATE ";
        $this->query .= $this->table;
        $this->query .= " SET ";
        $this->query .= $prep['update'];
        $this->query .= " WHERE " . $where;
        $this->query .= $this->setLimit();
        // Complete the bindings
        $this->STH = $this->connection->prepare($this->query);
    }


    protected function count()
    {

    }


    protected function get()
    {
        // Execute it.

        if ($this->returnType == 'single') {

        } else {

        }
    }

    /**
     * Prepare a query for inserts
     */
    function prepareGet($counting = '0')
    {
        // Where submitted?
        if (empty($this->data['where'])) {
            $this->data['where'] = '1';
        }
        // Prep query
        $this->query = $this->buildJoin();
        $this->query .= " WHERE " . $this->prepWhere();
        $this->query .= $this->setOrder();
        $this->query .= $this->setLimit();
    }


    /**
     * Builds a basic join query. If a join was not
     * specified, it will return a more standard
     * SELECT statement.
     *
     * @return string
     */
    function buildJoin()
    {
        $statement = "SELECT ";
        if (! empty($this->data['join'])) {
            if ($this->counting) {
                $statement .= " COUNT(*)";
            } else {
                if (! empty($this->data['keys'])) {
                    $statement .= $this->prepJoinKeys();
                } else {
                    if (! empty($this->data['select'])) {
                        $statement .= $this->data['select'];
                    } else {
                        $statement .= "*";
                    }
                }
            }
            $statement .= " FROM " . $this->table;
            $statement .= $this->joinType();
            $statement .= $this->prepJoinTables();
        } else {
            if ($this->counting) {
                $statement .= " COUNT(*)";
            } else {
                if (! empty($prep['keys'])) {
                    $statement .= $this->prepJoinKeys();
                } else {
                    if (! empty($this->data['select'])) {
                        $statement .= $this->data['select'];
                    } else {
                        $statement .= "*";
                    }
                }
            }
            $statement .= " FROM " . $this->table;
        }
        return $statement;
    }


    /**
     * Determines the type of join we are employing.
     *
     * @return string   Join statement for query.
     */
    function joinType()
    {
        switch ($this->data['join_type']) {
            case 'left':
                return ' LEFT JOIN ';
            case 'right':
                return ' RIGHT JOIN ';
            case 'inner':
                return ' INNER JOIN ';
            case 'cross':
                return ' CROSS JOIN ';
            case 'outer':
                return ' OUTER JOIN ';
            case 'full_outer':
                return ' FULL OUTER JOIN ';
            case 'left_outer':
                return ' LEFT OUTER JOIN ';
            default:
                return ' JOIN ';
        }
    }

    /**
     * Prepare the list of tables that
     * we are joining together.
     *
     * @return string
     */
    function prepJoinTables()
    {
        $joins = '';
        $pos = 0;
        foreach ($this->data['join'] as $aTable) {
            $sani_table = $this->determineTable($aTable);
            $joins .= " `" . $sani_table . "`";
            $joins .= " ON " . $this->table . '.' . $this->data['join_main_id']['0'] . '=' . $sani_table . '.' . $this->data['join_on'][$pos];
            $pos++;
        }
        return $joins;
    }


    /**
     * For more complex queries,
     * this function will run them
     * and return the result.
     */
    protected function fullQuery()
    {
        // Complete the bindings
        $this->STH = $this->connection->prepare($this->data['query']);

        $this->binding = $this->data['bindings'];
        $this->completeBinding();

    }


    /**
     * Retrieve the data returned from
     * the database.
     *
     * @param string $format
     *
     * @return array|string
     */
    public function data($format = 'object')
    {
        switch ($format) {
            case 'array':
                return (array)$this->result;
            case 'json':
                return json_encode($this->result);
            case 'xml':
                return XMLSerializer::generateValidXmlFromObj($this->result);
            default:
                return $this->result;
        }
    }


    /**
     * After we have prepared the query, we complete
     * the process before execution by completing the
     * bindings. That was established in $this->prepKeys();
     */
    protected function completeBinding()
    {
        if (! empty($this->binding)) {
            foreach ($this->binding as $key => $value) {
                $this->STH->bindParam(':' . $key, $value);
            }
        }
    }

    /**
     * Prepare a where statement.
     *
     * @return string
     */
    protected function prepWhere()
    {
        $where = '';
        if (empty($this->data['where']) || $this->data['where'] == '1') {
            return '1';
        } else {
            foreach ($this->data['where'] as $key => $value) {
                if (is_array($value)) {
                    $where .= " " . $this->inclusive . " (";
                    $where .= $this->buildInternal($key, $value);
                    $where .= " )";
                } else {
                    $where .= " " . $this->inclusive . " ";
                    $check_table = explode('.', $key);
                    $eq = $this->determineEq($value);
                    if (! empty($check_table['1'])) {
                        $this->binding[':' . $check_table['1']] = $eq['1'];
                        $where .= $this->determineTable($check_table['0']) . '.' . $check_table['1'] . $eq['0'] . " :" . $check_table['1'];
                    } else {
                        $this->binding[':' . $key] = $eq['1'];
                        $where .= $key . $eq['0'] . " :" . $key;
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


    /**
     * @param $value    String  Value received from the query array.
     *
     * @return array    Correct value for use in MySQL query.
     */
    protected function determineEq($value)
    {
        if (substr($value,0,2) == '!=') { return array('!=',substr($value,2)); }
        else if (substr($value,0,2) == '>=') { return array('>=',substr($value,2)); }
        else if (substr($value,0,2) == '<=') { return array('<=',substr($value,2)); }
        else if (substr($value,0,1) == '~') { return array(' LIKE ',substr($value,1)); }
        else if (substr($value,0,1) == '>') { return array('>',substr($value,1)); }
        else if (substr($value,0,1) == '<') { return array('<',substr($value,1)); }
        else if (substr($value,0,1) == '=') { return array('=',substr($value,1)); }
        else { return array('=',$value); }
    }


    /**
     *
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    protected function buildInternal($key, $value)
    {
        $where = '';
        foreach ($value as $entry) {
            $det_eq = $this->determineEq($entry);
            $where .= " OR " . $key . $det_eq['0'] . ":" . $key;
            $this->binding[':' . $key] = $det_eq['1'];
        }
        return substr($where,4);
    }


    /**
     * Prep that keys, values, and bindings
     * for this upcoming query.
     *
     * @return array
     */
    protected function prepKeys()
    {
        $keys = '';
        $values = '';
        $update = '';
        $up = 0;
        foreach ($this->data['keys'] as $key) {
            $keys .= ', ' . $key;
            $values .= ', :' . $key;
            $update .= ", " . $key . " = :" . $key;
            $this->binding[$key] = $this->data['values'][$up];
            $up++;
        }
        return array(
            'keys' => substr($keys, 2),
            'values' => substr($values, 2),
            'update' => substr($update, 2),
        );
    }


    /**
     * @return string
     */
    function prepJoinKeys()
    {
        $keys = '';
        foreach ($this->data['keys'] as $aKey => $table) {
            if (! empty($table)) {
                $keys .= "," . $this->determineTable($table) . '.' . $aKey;
            } else {
                $keys .= "," . $aKey;
            }
        }
        return substr($keys,1);
    }


    /**
     * Sanitize without adding to bindings.
     * Very rarely used and not very safe.
     */
    public function sanitize($string)
    {
        return trim($this->connection->quote($string), "'");
    }


    /**
     * Process the error.
     */
    protected function dbError()
    {
        $this->error = '1';
        $details = $this->connection->errorInfo();
        $this->errorDetails = new \stdClass();
        $this->errorDetails->msg1 = $details['0'];
        $this->errorDetails->msg2 = $details['1'];
        $this->errorDetails->msg3 = $details['2'];
    }


}