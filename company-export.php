<?php

//**********************************************************************
//
//	Comp[any exporter main script. Sets up connection and runs the
//	required SQLs. Outputs Jason objects to stdout for piping through
//	to new DB processor.
//
//  History:
//  -------
//  1.0     03Oct GB      Original
//
//**********************************************************************

require_once "table.php";
require_once "sql.php";

$utf8_errors = array();
$report = '';

//**********************************************************************
//
//  Some helpers.
//
//**********************************************************************

function error ($msg)
{
    Logger::logError ($msg);
}

function warning ($msg)
{
    Logger::logWarning ($msg);
}

function info ($msg)
{
    Logger::logInfo ($msg);
}

function stamped ($msg)
{
    return Logger::pfx() . $msg;
}

//**********************************************************************
//
//  Reporting
//
//**********************************************************************

function reportAppend ($msg)
{
	global $report;

	$report .= "\n" . $msg;
}

function reportWrite ($fn)
{
    global $report;
    $res = false;

    if ($fp = fopen ($fn, "a+"))
    {
        $res = fputs ($fp, $report);
				fclose ($fp);
        //chmod ($fn, 0666);
				if ($res)
        	info ('Report written');
				else
        	error ('Could not write report');
    }
    return $res;
}

//**********************************************************************
//
//  Print JSON encoded array of rows
//
//**********************************************************************

function toJson($pid, $rows)
{
	global $utf8_errors;

	$res = json_encode($rows);

	if (json_last_error() === JSON_ERROR_UTF8)
	{
    	error ("JSON UTF-8 Error in id [%d]", $pid);
		$utf8_errors[] = $pid;
	}

	return $res;
}

//**********************************************************************
//
//  Print array of rows
//
//**********************************************************************

function printRows($rows)
{
	$nRows = 0;
	foreach ($rows as $row)
	{
		$nRows++;
		printCols($row);
	}
}

function printCols($row)
{
	$nCols = 0;

	$colNames = array_keys ($row);
	foreach ($row as $col)
	{
		if ($nCols > 0)
			echo "|";
		echo (sprintf ("%s=>%s", $colNames[$nCols], $col));
		$nCols++;
	}
	echo "\n";
}

//**********************************************************************
//
//  Do an UTF8 conversion on a column in a row.
//
//**********************************************************************

function toUTF8($v)
{
    if (isset ($v))
    {
        $s = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $v);
        return utf8_encode ($s);
    }
    return $v;
}

//**********************************************************************
//
//  Process companies
//
//**********************************************************************

function process ()
{
	$profileTable = new Table ('dbo.Profiles',
            array ('legacy_profile_id',), 'intProfileId');

	$sql = getSqlForProfiles();
	$profileTable->executeSQL($sql);
	//$rows = $profileTable->asRows();
	$rows = $profileTable->asArray();
	unset ($profileTable);

	// Preparing the JSON object

	foreach ($rows as $row)
	{
            $row ['profile_description'] = toUTF8 ($row ['profile_description']);
            $row ['profile_title'] = toUTF8 ($row ['profile_title']);
            $row ['referral_code'] = toUTF8 ($row ['referral_code']);

	    $cid = $row['legacy_company_id'];
	    info (sprintf ("Profile for company %d - %s", $cid, $row['trading_name']));
	    echo toJson ($cid, $row), "\n";
//print_r($row);
	}
	
	return count($rows);
}	

//**********************************************************************
//
//  Main
//
//**********************************************************************

function main($logFile, $reportFile)
{
	$lvl = Logger::$DEBUG;
	//$lvl = Logger::$ERROR;
 	if (!Logger::open ($logFile, $lvl))
	{
		echo "Cannot open log file [", $logFile, "]\n";
		exit;
  }

	info ("*****************************");
	info ("Start company profile export");
	info ("*****************************");

	global $utf8_errors;

	$n = process();

	if (count($utf8_errors) > 0)
	{
		info ("UTF8 Conversion errors in the folowing property id's:");
		foreach ($utf8_errors as $s)
			info (sprintf("%d", $s));
	}

	reportAppend ("==================================================");
	reportAppend (stamped (" Company profile export SUMMARY:"));
	reportAppend (sprintf ("Processed %d profiles", $n));
	reportAppend (sprintf ("There were %d UTF8 Conversion errors", count($utf8_errors)));
	reportAppend ("==================================================\n");
	reportWrite ($reportFile);

	info (sprintf ("Company profile exporter finished - [%d] rows exported", $n));
}

if (isset ($argv[1]))
        $logFile = $argv [1];
else
        $logFile = "export.log";

if (isset ($argv[2]))
        $reportFile = $argv [2];
else
        $reportFile = "report.log";

main($logFile, $reportFile)

?>
