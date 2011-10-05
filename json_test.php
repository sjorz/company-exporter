<?php

//**********************************************************************
//
//  JSON tests.
//
//  History:
//  -------
//  1.0     20Jul11 GB      Original
//
//**********************************************************************

function main()
{
	$prop = array ('id' => 1, 'street_name' => 'Some Road', 'street_no' => 33);
	$f1 = array ('group' => 'SomeGroup', 'name' => 'AirCon', 'interior' => 0);
	$f2 = array ('group' => 'SomeGroup', 'name' => 'Sucutiry', 'interior' => 1);
	$f2 = array ('group' => 'SomeGroup', 'name' => 'Secutiry', 'interior' => 1);
	$f3 = array ('group' => 'AnotherGroup', 'name' => 'Heating', 'interior' => 1);
	$c = array ('first_name' => 'Kees', 'last_name' => 'Kist');
	$l = array ('first_name' => 'Guus', 'last_name' => 'Geluk');
	$i1 = array ('day_of_week' => 3, 'start_time' => '09:30', 'end_time' => '10:30');
	$i2 = array ('day_of_week' => 5, 'start_time' => '14:30', 'end_time' => '16:30');
	$p1 = array ('caption' => 'A nice picture', 'original_url' => '/path/bla.jpg');
	$p2 = array ('caption' => 'A stupid picture', 'original_url' => '/path/blo.jpg');

	$res1 = $prop;
	$res1 ['contact'] = array ($c);
	$res1 ['landlord'] = array ($l);
	$res1 ['features'] = array ($f1, $f2, $f3);
	$res1 ['inspections_times'] = array ($i1, $i2);
	$res1 ['photos'] = array ($p1, $p2);

	echo "================================\n";
	echo json_encode ($res1), "\n";
	echo "================================\n";

	echo "================================\n";
	echo json_encode (array (319430)), "\n";
	echo "================================\n";

	echo "================================\n";
	echo json_encode (array ("legacy_property_id" => 319430)), "\n";
	echo "================================\n";
}

main();

?>
