# How to use Sqlite #

## Tutorial ##
  * [SQLite Tutorial](http://souptonuts.sourceforge.net/readme_sqlite_tutorial.html)

## Create Tables ##

```
sqlite> CREATE TABLE tbl2 (
   ...>   f1 INTEGER primary key AUTOINCREMENT,
   ...>   f2 TEXT,
   ...>   f3 REAL
   ...> );
sqlite>
```


## PHP SQLite Class ##
```
/**
* connection to a sqlite database
*
* @abstract
*
* @author      roc  <roc@linuxaid.com.cn>
* @copyright   copyright (c) 2005 roc
*
* @package     kernel
* @subpackage  database
*/
class SQLiteDatabase
{
    /**
     * Database connection
     * @var resource
     */
    var $conn;

    /**
     * connect to the database
     *
     * @param bool $selectdb select the database now?
     * @return bool successful?
     */
    function connect($selectdb = true)
    {
        $this->conn = @sqlite_open(DB_FILE_NAME, 0666, $sqliteerror);
   
        if (!$this->conn) {
            return false;
        }
        return true;
    }

    /**
     * generate an ID for a new row
     *
     * This is for compatibility only. Will always return NULL, because SQLite supports
     * autoincrement for primary keys.
     *
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because sqlite has support for autoincrement
     */
    function genId($sequence)
    {
        return ' NULL '; // will use auto_increment
    }

    function IsGenId($id)
    {
        if (empty($id)) {
            return true;
        }
        if ($id == 0) {
            return true;
        }
        if (trim($id) == 'NULL') {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get a result row as an enumerated array
     *
     * @param resource $result
     * @return array
     */
    function fetchRow($result)
    {
        return @sqlite_fetch_array($result,SQLITE_NUM);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @return array
     */
    function fetchArray($result)
    {
        $arr = sqlite_fetch_array($result,SQLITE_ASSOC);
        foreach ($arr as $key => $value) {
            $i = strpos($key,'.');
            if ($i) {
                $r[substr($key,$i+1)] = $value;
            }else{
                $r[$key] = $value;
            }
        }
        return $r;
    }

    /**
     * Fetch a result row as an associative array
     *
     * @return array
     */
    function fetchBoth($result)
    {
        return @sqlite_fetch_array( $result, SQLITE_BOTH );
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int
     */
    function getInsertId()
    {
        return sqlite_last_insert_rowid($this->conn);
    }

    /**
     * Get number of rows in result
     *
     * @param resource query result
     * @return int
     */
    function getRowsNum($result)
    {
        return @sqlite_num_rows($result);
    }

    /**
     * Get number of affected rows
     *
     * @return int
     */
    function getAffectedRows()
    {
        return sqlite_changes($this->conn);
    }

    /**
     * Close SQLite connection
     *
     */
    function close()
    {
        sqlite_close($this->conn);
    }

    /**
     * will free all memory associated with the result identifier result.
     *
     * @param resource query result
     * @return bool TRUE on success or FALSE on failure.
     */
    function freeRecordSet($result)
    {
        $result = nil;
        return true;
    }

    /**
     * Returns the text of the error message from previous SQLite operation
     *
     * @return bool Returns the error text from the last SQLite function, or '' (the empty string) if no error occurred.
     */
    function error()
    {
        return @sqlite_error_string(sqlite_last_error($this->conn));
    }

    /**
     * Returns the numerical value of the error message from previous SQLite operation
     *
     * @return int Returns the error number from the last SQLite function, or 0 (zero) if no error occurred.
     */
    function errno()
    {
        return @sqlite_last_error($this->conn);
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     *
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    function quoteString($str)
    {
         $str = "'".sqlite_escape_string($str)."'";
         return $str;
    }

    /**
     * perform a query on the database
     *
     * @param string $sql a valid SQLite query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    function &queryF($sql, $limit=0, $start=0)
    {
        if ( !empty($limit) ) {
            if (empty($start)) {
                $start = 0;
            }
            $sql = $sql. ' LIMIT '.(int)$start.', '.(int)$limit;
        }
        $result =@sqlite_query($sql, $this->conn);
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }

}
?>
```