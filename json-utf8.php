<?php

//**********************************************************************
//
//  JSON UTF-8 handling.
//
//  History:
//  -------
//  1.0     27Jul11 GB      Original
//
//**********************************************************************

function main()
{
	$s = 'A classic beachfront home with direct beach access, huge saltwater pool, polished timber floors and tropical dér.    Full width deck for barbecues and outdoor dining to the sounds of the surf.  More outdoor sun and fun on the pool terrace with bar room and pool lighting by night.  Tranquil, relaxing and fenced area for the dog.  Lock up garage.<br><br>Accommodates 6.  1 King, 1 Queen, 2 Singles.<br>Includes: Microwave, Dishwasher, TV, DVD, Stereo/CD player, Washing Machine, Dryer, Box fan x 2, Column heater, Fireplace, Deck furniture, Outdoor furniture, BBQ, 2 toilets, Pool.<br><br>Low $1450pw<br>Mid $2100pw<br>Peak $3000pw<br>Minimum booking/tariff 3 days<br>Minimum Easter booking/tariff 7 days<br>Linen included - Complimentary chilled wine on arrival<br><br>Check availability at http://www.peregianbeachholiday.com.au/holiday/Availability';
	$a ['photos'] = utf8_encode ($s);

	echo "================================\n";
	echo json_encode ($a), "\n";
	echo "================================\n";
}

main();

?>
