<?php

class MysqlDB {

    private $conn;

    public function __construct() {
        require_once("config.mysqldb.php");
        $this->connect($db_config["hostname"], $db_config["username"], $db_config["password"], $db_config["database"], $db_config["pconnect"]);
    }

    public function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0, $charset = 'utf8') {
        if ($pconnect == 0) {
            $this->conn = mysql_connect($dbhost, $dbuser, $dbpw, true);
        } else {
            $this->conn = mysql_pconnect($dbhost, $dbuser, $dbpw);
        }
        if (!$this->conn) {
            $this->halt("数据库连接失败");
        }
        if (!mysql_select_db($dbname, $this->conn)) {
            $this->halt('数据库选择失败');
        }
        mysql_query("set names " . $charset);
    }

    private function halt($msg) {
        
    }

    /**
     * 从数据表中查找记录
     *
     * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串，
     * 请注意在使用字符串时将需要开发者自行使用escape来对输入值进行过滤
     * @param sort    排序，等同于“ORDER BY ”
     * @param fields    返回的字段范围，默认为返回全部字段的值
     * @param limit    返回的结果数量限制，等同于“LIMIT ”，如$limit = " 3, 5"，即是从第3条记录（从0开始计算）开始获取，共获取5条记录
     *                 如果limit值只有一个数字，则是指代从0条记录开始。
     */
    public function findAll($table, $conditions = null, $sort = null, $fields = null, $limit = null) {
        $where = "";
        $fields = empty($fields) ? "*" : $fields;
        if (is_array($conditions)) {
            $join = array();
            foreach ($conditions as $key => $condition) {
                $condition = $this->escape($condition);
                $join[] = "{$key} = {$condition}";
            }
            $where = "WHERE " . join(" AND ", $join);
        } else {
            if (null != $conditions) {
                $where = "WHERE " . $conditions;
            }
        }
        if (null != $sort) {
            $sort = "ORDER BY {$sort}";
        }
        $sql = "SELECT {$fields} FROM $table {$where} {$sort}";
        if (null != $limit) {
            $sql = $this->setlimit($sql, $limit);
        }

        return $this->getArray($sql);
    }

    /**
     * 从数据表中查找一条记录
     *
     * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串，
     * 请注意在使用字符串时将需要开发者自行使用escape来对输入值进行过滤
     * @param sort    排序，等同于“ORDER BY ”
     * @param fields    返回的字段范围，默认为返回全部字段的值
     */
    public function find($table, $conditions = null, $sort = null, $fields = null) {
        if ($record = $this->findAll($table, $conditions, $sort, $fields, 1)) {
            return array_pop($record);
        } else {
            return FALSE;
        }
    }

    function findSql($sql) {
        return $this->getArray($sql);
    }
    
    	/**
	 * 执行SQL语句，相等于执行新增，修改，删除等操作。
	 *
	 * @param sql 字符串，需要执行的SQL语句
	 */
	public function runSql($sql)
	{
		return $this->exec($sql);
	}

    public function exec($sql) {
        if ($result = mysql_query($sql, $this->conn)) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * 按SQL语句获取记录结果，返回数组
     * 
     * @param sql  执行的SQL语句
     */
    public function getArray($sql) {
        if (!$result = $this->exec($sql))
            return array();
        if (!mysql_num_rows($result))
            return array();
        $rows = array();
        while ($rows[] = mysql_fetch_array($result, MYSQL_ASSOC)) {
            
        }
        mysql_free_result($result);
        array_pop($rows);
        return $rows;
    }

    /**
     * 在数据表中新增一行数据
     *
     * @param row 数组形式，数组的键是数据表中的字段名，键对应的值是需要新增的数据。
     */
    public function create($table, $row) {
        if (!is_array($row))
            return FALSE;
        if (empty($row))
            return FALSE;
        foreach ($row as $key => $value) {
            $cols[] = $key;
            $vals[] = $this->escape($value);
        }
        $col = join(',', $cols);
        $val = join(',', $vals);

        $sql = "INSERT INTO {$table} ({$col}) VALUES ({$val})";
        if (FALSE != $this->exec($sql)) { // 获取当前新增的ID
            if ($newinserid = $this->newinsertid()) {
                return $newinserid;
            }
        }
        return FALSE;
    }

    /**
     * 修改数据，该函数将根据参数中设置的条件而更新表中数据
     * 
     * @param conditions    数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
     * @param row    数组形式，修改的数据，
     *  此参数的格式用法与create的$row是相同的。在符合条件的记录中，将对$row设置的字段的数据进行修改。
     */
    public function update($table, $conditions, $row) {
        $where = "";
        if (empty($row)) {
            return FALSE;
        }
        if (is_array($conditions)) {
            $join = array();
            foreach ($conditions as $key => $condition) {
                $condition = $this->escape($condition);
                $join[] = "{$key} = {$condition}";
            }
            $where = "WHERE " . join(" AND ", $join);
        } else {
            if (null != $conditions) {
                $where = "WHERE " . $conditions;
            }
        }
        foreach ($row as $key => $value) {
            $value = $this->escape($value);
            $vals[] = "{$key} = {$value}";
        }
        $values = join(", ", $vals);
        $sql = "UPDATE {$table} SET {$values} {$where}";
        return $this->exec($sql);
    }

    /**
     * 按条件删除记录
     *
     * @param conditions 数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
     */
    public function delete($table, $conditions) {
        $where = "";
        if (is_array($conditions)) {
            $join = array();
            foreach ($conditions as $key => $condition) {
                $condition = $this->escape($condition);
                $join[] = "{$key} = {$condition}";
            }
            $where = "WHERE ( " . join(" AND ", $join) . ")";
        } else {
            if (null != $conditions) {
                $where = "WHERE ( " . $conditions . ")";
            }
        }
        $sql = "DELETE FROM {$table} {$where}";
        return $this->exec($sql);
    }

    /**
     * 对特殊字符进行过滤
     *
     * @param value  值
     */
    public function escape($value) {
        if (is_null($value))
            return 'NULL';
        if (is_bool($value))
            return $value ? 1 : 0;
        if (is_int($value))
            return (int) $value;
        if (is_float($value))
            return (float) $value;
        if (@get_magic_quotes_gpc())
            $value = stripslashes($value);
        return '\'' . mysql_real_escape_string($value, $this->conn) . '\'';
    }

    /**
     * 格式化带limit的SQL语句
     */
    public function setlimit($sql, $limit) {
        return $sql . " LIMIT {$limit}";
    }

    /**
     * 返回当前插入记录的主键ID
     */
    public function newinsertid() {
        return mysql_insert_id($this->conn);
    }

    function sysdate() {
        return date("Y-m-d H:i:s");
    }

}
