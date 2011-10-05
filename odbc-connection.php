<?php
//**********************************************************************
//
//  OdbcConnection class. Holds on to the ODBC connector. The class can be
//  used to set up a connection to the database identified by the
//  data source (a ODBC connect string). Once connected the class can
//  execute 
//
//  History:
//  -------
//  1.0     14Aug10 GB      Original
//
//**********************************************************************

class OdbcConnection
{
    private $dataSource;
    private $user;
    private $password;
    private $connection;
    private $connected;

    public  function __construct ($ds, $u, $p)
    {
        if (isset($this))
        {
            $this->dataSource = $ds;
            $this->user = $u;
            $this->password = $p;
            $this->connect ();
        }
    }

    public  function __destruct ()
    {
        $this->close ();
    }

    public  function __toString ()
    {
        return '[' . $this->dataSource . '][' .  $this->user . ']';
    }

    private function error ($msg)
    {
        exit ($this->__toString() . ': ' . $msg);
    }

    //**********************************************************************
    //
    //  Set up the connection. The caller can use the connection
    //  to execute SQL's.
    //
    //**********************************************************************

    public function connect ()
    {
        if ($this->connection)
            return true;

        if (!$this->user || !$this->password || !$this->dataSource)
        {
            $this->error ('Data missing; cannot connect to DB');
            return false;
        }
        if (!$this->connection)
            $this->connection = odbc_connect($this->dataSource,
                                             $this->user, $this->password);
        if (!$this->connection)
            $this->error ('Connect to database failed');

        return true;
    }

    //**********************************************************************
    //
    // Execute an SQL statement
    //
    //**********************************************************************

    public function execute ($sql)
    {
        if (!$this->connection)
            $this->error ('Database does not seem to be connected');
        if (!$resultSet = odbc_exec ($this->connection, $sql))
            return NULL;
        return $resultSet;
    }

    //**********************************************************************
    //
    //  Call a stored procedure. The call syntax of stored procedures is
    //  rather dialect-sensitive. For now, we assume MS SQL Server, but we
    //  may have to generalise this. A derived DB-specific classs is the
    //  obvious way to go to cater for dialects (e.g. mssqlOdbcConnection,
    //  mysqlConnection,. etc). There's event eh difference of ODBC and
    //  native connections. E.g. for mysql we would use the native calls
    //  rather than ODBC. Returns true or false.
    //
    //  Calling convention:
    //  --------------------
    //  callSP (<name>, <args>)
    //  - name      the name of the stored procedure
    //  - args      an associative array of name/value pairs.
    //
    //  Example:
    //  --------
    //  callSP ('DoSomething', array ('myname'=>'Kees', 'yearOfBirth'=>51);
    //  Calls the storeed procedure named 'DoSomething', whi9ch takes
    //  a char paramater 'yname', value 'Kees' and an int parameter
    //  'yearOfBirth, value 51. It translates to the SQL statement:
    //  
    //  execute DoSomething @myname='Kees', yearOfBirth=51
    //
    //**********************************************************************

    public function callSP ($name, $args)
    {
        $sql = 'EXEC ' . $name . ' ';
        $n = 0;
        while (list ($k, $v) = each ($args))
        {
            if ($n++ > 0)
                $sql .= ',';
            $sql .= '@' . $k . '=' . $v . ' ';
        }
        Logger::logDebug ($sql);
        $res = odbc_exec ($this->connection, $sql);
        Logger::logDebug ($res ? "SP executed successfully" :
                                 "SP failed: " . odbc_errormsg());
        return $res;
    }

    //**********************************************************************
    //
    //  The following does not work. It's an experiment using parameterised
    //  stored procedures. The idea is to create the template for the SP only
    //  once, and then repeatedly call the odbc_execute function with the
    //  "prepared" parameterised statement. Efficient, but unfortunately
    //  we can't get it to work as yet.
    //
    //**********************************************************************

    public function callParameterisedSP ($a)
    {
        $preparedSql = odbc_prepare ($this->connection,
            "exec bla @pmcode=?, @description=?'");
        $a = array (56, 'Using parm statement');
        return odbc_execute ($preparedSql, $a);
    }

    //**********************************************************************
    //
    //  Close the connection
    //
    //**********************************************************************

    public function close ()
    {
        if ($this->connected)
            if ($this->connection)
                odbc_close ($this->connection);
    }

}
