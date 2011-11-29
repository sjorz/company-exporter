<?php
//**********************************************************************
//
//  Table class. Represents a data table and can do simple oprations like
//  showing in HTML table, searching a partiucular row/fiels, etc.
//  A table object is created by obtaining a result set from the ODBC
//  connection. The result set is a snapshot; a refresh function is
//  available to re-read the results set.
//
//  History:
//  -------
//  1.0     14Aug10 GB      Original
//
//**********************************************************************

require_once "odbc-connection.php";
require_once "log.php";

class Table
{
    private $tableName;
    private $resultSet;
    private $dbConnector;
    private $columns;
    private $columnHeaders;
    private $hiddenColumns;
    private $references;
    private $sortColumn;
    private $theme;
    private $dataSource;
  private $rowCount;

    public  function __construct ($n, $c = "", $s = "")
    {
        if (isset($this))
        {
          $ds = "Rent_Production";
          $user = "rentLive";
      		$pwd = "Consult23";

          $ds = "Rent_Staging";
					$user = "rent_staging";
					$pwd = "GreenFish123";

					//$ds = "RentTst";
					//$user = "sa";
					//$pwd = "abc123";

            $this->dataSource = $ds;
            $this->tableName = $n;
      if ($c == "")
        $this->columns = NULL;
      else
        $this->columns = $c;
            $this->sortColumn = $s;
            $this->dbConnector = new OdbcConnection ($ds, $user, $pwd);
            $this->columnHeaders = $c;
            $this->hiddenColumns = array ();
            $this->references = array ();
            $this->rowCount = 0;
        }
    }

    public  function __destruct ()
    {
    }

    public  function __toString ()
    {
        return 'Table: [' . $this->tableName . ']';
    }

    private function error ($msg)
    {
        Logger::logError ($msg);
    }

    //**********************************************************************
    //
    //  Set column headers. If omitted, the column names will be used
    //
    //**********************************************************************

    public function setColumnHeaders ($h)
    {
        $this->columnHeaders = $h;
    }

    //**********************************************************************
    //
    //  Set hidden column. Hidden columns won't be printed.
    //
    //**********************************************************************

    public function setHiddenColumns ($c)
    {
        $this->hiddenColumns = $c;
    }

    //**********************************************************************
    //
    //  Set hidden column. Ref columns are shown as links to the page
    //  defined by $url, with the value of the column as an argument.
    //
    //**********************************************************************

    public function setReference ($cName, $url)
    {
        $this->references [$cName] = $url;
    }

    //**********************************************************************
    //
    //  Execute an SQL
    //
    //**********************************************************************

    public function executeSQL ($sql)
    {
        Logger::logDebug ($sql);
        if (!$this->resultSet = $this->dbConnector->execute ($sql))
        {
            Logger::logError ("Error in SQL");
            $this->error ($sql);
            return false;
        }
        return true;
    }

    //**********************************************************************
    //
    //  Return the number of rows in the result set
    //
    //**********************************************************************

    public function getRowCount()
    {
    return $this->rowCount;
  }

    //**********************************************************************
    //
    //  Read a snapshot of the entire table. Only read specified columns.
    //
    //  NOTE: odbc_num_rows returns -1 for mnost drivers, so it's no use
    //  for use to count the rows returned by a query.
    //
    //**********************************************************************

    public function readAll ()
    {
        $sql = "SELECT ";
    if (is_null($this->columns))
    {
          $sql .= "* ";
    }
    else
    {
          $i = 0;
          foreach ($this->columns as $column)
          {
              if ($i > 0)
                  $sql.= ',';
              $sql .= $column;
              $i++;
          }
    }
        $sql .= ' from ' . $this->tableName;
        if (strlen ($this->sortColumn) > 0)
            $sql .= ' order by ' . $this->sortColumn;
        return $this->executeSQL ($sql);
    }

    public function readGroupBy ()
    {
        $sql = "SELECT ";
        $i = 0;
        foreach ($this->columns as $column)
        {
            if ($i > 0)
                $sql.= ',';
            $sql .= $column;
            $i++;
        }
        $sql .= ' from ' . $this->tableName;
        if (strlen ($this->sortColumn) > 0)
            $sql .= ' group by ' . $this->sortColumn;
        return $this->executeSQL ($sql);
    }

    //**********************************************************************
    //
    //  Read a snapshot of the table with a where clause
    //
    //**********************************************************************

    public function read ($where)
    {
        $sql = "SELECT ";
    if (is_null ($this->columns))
    {
          $sql .= "* ";
    }
    else
    {
          $i = 0;
          foreach ($this->columns as $column)
          {
              if ($i > 0)
                  $sql.= ',';
              $sql .= $column;
              $i++;
          }
    }
        $sql .= ' from ' . $this->tableName;
        $sql .= ' where ' . $where;
        if (strlen ($this->sortColumn) > 0)
            $sql .= ' order by ' . $this->sortColumn;

        Logger::logDebug ($sql);
        if (!$this->resultSet = $this->dbConnector->execute ($sql))
        {
            $this->error ($sql);
            return false;
        }
        return true;
    }

    //**********************************************************************
    //
    //  Get a particular column value from an earlier query.
    //
    //**********************************************************************

    public function getValue ($col)
    {
        odbc_fetch_row ($this->resultSet);
        return odbc_result ($this->resultSet, $col);
    }

    //**********************************************************************
    //
    //  Helper - Test whether column is hidden
    //
    //**********************************************************************

    private function hideColumn ($i)
    {
        if (count ($this->hiddenColumns) > $i)
            return $this->hiddenColumns [$i];
        return false;
    }

    //**********************************************************************
    //
    //  As text
    //
    //**********************************************************************

    public function doPrint ()
    {
        $n = 0;

        while (odbc_fetch_row ($this->resultSet))
        {
            $i = 0;
            foreach ($this->columns as $column)
            {
                if (!$this->hideColumn ($i++))
                {
                    $val = odbc_result ($this->resultSet, $column);
                    printf ("%s=>%s", $column, $val);
          if ($i > 0)
            echo "|";
                }
            }
            echo "\n";
            $n++;
        }
    $this->rowCount = $n;
    }

    //**********************************************************************
    //
    //  Return the results as an array of rows, where each row is
    //  an associative arrey of column name and value pairs.
    //
    //**********************************************************************

    public function asArray ()
    {
        $res = array();
        $n = 0;                     // Only for debugging
        while ($r = odbc_fetch_array ($this->resultSet))
        {
            $res [] = $r;
            $n++;
        }
        Logger::logDebug ('Returned [' . $n . '] rows');
        return $res;
    }

    //**********************************************************************
    //
    //  Return the results as an array (key is col name), separated by a
    //  column delimiter, $colDelim. If more than one row is read, each row
    //  is appended in the string, separated by the record delimiter $rowDelim
    //
    //**********************************************************************

    public function toValueString ($rowDelim, $colDelim)
    {
        $res = "";
        $nRows = 0;
        while (odbc_fetch_row ($this->resultSet))
        {
            if (strlen ($res) > 0)
                $res .= $rowDelim;
            for ($i = 0; $i < odbc_num_fields ($this->resultSet); $i++)
            {
                $res .= trim (odbc_result ($this->resultSet, $i + 1)) .
                        $colDelim;
            }
            $nRows++;
        }
        Logger::logDebug ('Returned [' . $nRows . '] rows');
        return $res;
    }

    //**********************************************************************
    //
    //  Return the results as an array of rows, where each row is and
    //  indexed array of values.
    //
    //**********************************************************************

    public function asIndexedArray ()
    {
        $i = 0;
        while (odbc_fetch_into ($this->resultSet, $r))
        {
            $a [$i++] = $r;
        }
        Logger::logDebug ('Returned [' . $i . '] rows');
        return $a;
    }

    //**********************************************************************
    //
    //  Call a stored procedure; delegate this to the connection class.
    //  See connection.php for details
    //
    //**********************************************************************

    public function callSP ($n, $a)
    {
        return $this->dbConnector->callSP ($n, $a);
    }
}
