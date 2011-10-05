<?php
//**********************************************************************
//
//  Logger class. Can be used to append messages to a log file.
//  Implemented a singleton, so that only one istance per session can
//  exist.
//
//  History:
//  -------
//  1.0     19Aug10 GB      Original
//
//**********************************************************************

class Logger
{
    public static   $INFO = 0;
    public static   $ERROR = 1;
    public static   $WARN = 2;
    public static   $DEBUG = 3;
    public static   $TRACE = 4;

    private static  $inst;

    private static  $fileName;
    private static  $fp;
    private static  $level;

    private  function __construct ($fn)
    {
    }

    public  function __destruct ()
    {
        fclose (self::$fp);
    }

    public  function __toString ()
    {
        return 'Logger: [' . self::$fileName . ']';
    }

    public static function open ($fn, $lvl) 
    {
        self::$fileName = $fn;
        if (self::$fp = fopen ($fn, "a+"))
        {
            //chmod ($fn, 0666);
            self::$level = $lvl;
            return true;
        }
        return false;
    }

    public static function instance() 
    {
        if (!isset(self::$inst))
        {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public static function setLevel ($lvl) 
    {
        if ($lvl >= self::$INFO && $lvl <= self::$TRACE)
            self::$level = $lvl;
    }

    private function pfx ()
    {
        $dt = new DateTime ('now', new DateTimeZone ('Australia/Perth'));
        return ($dt->format ('dMy H:i:s') . ': ');
    }

    private function log ($msg)
    {
        if (!fputs (self::$fp, self::pfx() . $msg . "\n"))
        {
            echo '<h2>Write to log failed for some reason</h2>';
        }
        fflush (self::$fp);
    }

    public function logInfo ($msg)
    {
        self::log ('[INFO] ' . $msg);
    }

    public function logTrace ($msg)
    {
        if (self::$level >= self::$DEBUG)
            self::log ('[TRACE] ' . $msg);
    }

    public function logDebug ($msg)
    {
        if (self::$level >= self::$DEBUG)
            self::log ('[DEBUG] ' . $msg);
    }

    public function logWarning ($msg)
    {
        if (self::$level >= self::$WARN)
            self::log ('[WARN] ' . $msg);
    }

    public function logError ($msg)
    {
        if (self::$level >= self::$ERROR)
            self::log ('[ERROR] ' . $msg);
    }
}
