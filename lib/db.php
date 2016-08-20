<?php

class DbConnection extends SQLite3
{
  public function __construct()
  {
    $this->open("/home/pi/connectivity.sqlite", SQLITE3_OPEN_READONLY);
  }

  public function select($cols)
  {
    return new DbSelector($this, $cols);
  }
}

class DbSelector
{
  private $db;

  private $_cols;
  private $_from;
  private $_where;
  private $_limit;
  private $_groupBy;
  private $_orderBy;

  public function __construct($db, $cols)
  {
    $this->db    = $db;
    $this->_cols = $cols;
  }

  public function from($from)
  {
    $this->_from = $from;

    return $this;
  }

  public function where($where)
  {
    $this->_where = $where;

    return $this;
  }

  public function limit($limit)
  {
    $this->_limit = $limit;
    
    return $this;
  }

  public function groupBy($groupBy)
  {
    $this->_groupBy = $groupBy;

    return $this;
  }

  public function orderBy($orderBy)
  {
    $this->_orderBy = $orderBy;

    return $this;
  }

  public function __toString()
  {
    $q = "SELECT ";

    $q .= implode($this->_cols, ", ") . " ";

    $q .= "FROM " . $this->_from . " ";

    if ($this->_where)
            $q .= "WHERE " . $this->_where . " ";

    if ($this->_groupBy)
            $q .= "GROUP BY " . $this->_groupBy . " ";

    if ($this->_orderBy)
            $q .= "ORDER BY " . $this->_orderBy . " ";

    if ($this->_limit)
            $q .= "LIMIT " . $this->_limit . " ";

    $q .= ";";

    return $q;
  }

  public function run()
  {
    return $this->db->query("$this");
  }
};

?>
