<?php

//**********************************************************************
//
//  ODBC Connection tester. Connects to server defined by $dsn (see
//	/etc/odbc/ini). Username $user, password $pwd.
//
//  History:
//  -------
//  1.0     20Jul11 GB      Original
//
//**********************************************************************

	$dsn = "Rent_Staging";
	$user = "rent_staging";
	$pwd = "GreenFish123";

	//$dsn = "RentTst";
	//$user = "sa";
	//$pwd = "abc123";

	printf ("Connecting to [%s] as [%s][%s]...\n", $dsn, $user, $pwd);

	if (!$connection = odbc_connect ($dsn, $user, $pwd))
	{
		echo "Cannot connect\n";
		exit;
	}
	else
	{
		echo "Connected to rent_staging\n";
	}
	odbc_close ($connection);
	echo "Done";
	
?>
