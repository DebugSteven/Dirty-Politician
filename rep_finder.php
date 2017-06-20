<?php

/*
File Name : rep_finder.php
Description: This file formats the street address and city of the user and then carries out the queries to first find the user's precinct and then the lesiglative districts of the user. The districts are passed back to the driver file.
Author: J Haigh
Author URI: https://debugsteven.github.io/
*/

require_once(ABSPATH . 'wp-config.php');

function user_queries($street_address, $city) {

    // format the user's address for precinct query
    list($street_num, $street_name) = explode(" ", $street_address, 2);
    $street_name = formatStreetName($street_name);
    $city_stmt = formatCity($city);
    
    // calls the function to return precinct object from user's address	   
    $precinct_result = precinct_finder($street_num, $street_name, $city_stmt);

    // iterates over the precinct_result obj for user's precinct number
    foreach($precinct_result as $i => $precinct_result_row) {
        foreach($precinct_result_row as $row_name => $precinct_value) {
            // echo $precinct_value; 
        }
    }

    // create array to hold district values
    $dis = [];

    // call the function to return the district object from user's precinct
    $district_result = district_finder($precinct_value);

    // iterates over the district_result obj for user's district numbers
    foreach($district_result as $j => $district_result_row) {
        foreach($district_result_row as $row_name => $district_value) {
            $dis[$row_name] = $district_value;
        }    
    }

    return $dis;
}

function district_finder($precinct_value) {
	
	global $wpdb;
	$table_district = $wpdb->prefix . "precinct";

        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
        mysqli_select_db($con, DB_NAME);
        
	# If the connection failed then send error message.
	if(mysqli_connect_errno()) {
		printf('Could not connect to' . $database .': ' . mysqli_connect_error());
		exit();
	}

	$district_query = "SELECT Short_Number, Congressional_District, State_Senate_District, House_District
		FROM {$table_district}
		WHERE PRECINCT_NUMBER = '{$precinct_value}'";

	$district = mysqli_query($con, $district_query);
	
	# Close MySQL connection
	mysqli_close($con);
	return $district;
	
}


function precinct_finder($street_num, $street_name, $city_stmt) {
	
	global $wpdb;
	$table_voters = $wpdb->prefix . "voters";

        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
        mysqli_select_db($con, DB_NAME);
        
        //global $url, $database, $db_username, $db_password;
	//Make connection to our database
	//$con = new mysqli($url, $db_username, $db_password, $database);

	# If the connection failed then send error message.
	if(mysqli_connect_errno()) {
		printf('Could not connect to' . $database .': ' . mysqli_connect_error());
		exit();
	}

	$precinct_query = "SELECT PRECINCT
		FROM {$table_voters}
		WHERE RESIDENTIAL_ADDRESS like '{$street_num}%{$street_name}%' AND {$city_stmt}
		GROUP BY PRECINCT
		ORDER BY COUNT(PRECINCT) DESC
		LIMIT 1;";

	$precinct = mysqli_query($con, $precinct_query);
	
	# Close MySQL connection
	mysqli_close($con);
	return $precinct;
	
}


function formatCity($city) {

	$city = strtoupper($city);

	if (strpos($city, 'ACADEMY') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'A F ACADEMY')";
	} else if (strpos($city, 'COLORADO SPRINGS') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'COLO SPGS' OR RESIDENTIAL_CITY = 'COLO SPRINGS')";
	} else if (strpos($city, 'CRESTED BUTTE') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY like '%CRESTED BUTTE')";
	} else if (strpos($city, 'DE BEQUE') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'DEBEQUE')";
	} else if (strpos($city, 'FEDERAL HEIGHTS') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'FEDERAL HGTS')";
	} else if (strpos($city, 'FORT CARSON') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'FT CARSON' OR RESIDENTIAL_CITY = 'FORT CARSON')";
	} else if (strpos($city, 'FORT COLLINS') !== false) {	
		$city_stmt = "(RESIDENTIAL_CITY = 'FT COLLINS' OR RESIDENTIAL_CITY = 'FORT COLLINS')";
	} else if (strpos($city, 'FORT GARLAND') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'FT GARLAND' OR RESIDENTIAL_CITY = 'FORT GARLAND')";
	} else if (strpos($city, 'FORT LUPTON') !== false) {	
		$city_stmt = "(RESIDENTIAL_CITY = 'FT LUPTON' OR RESIDENTIAL_CITY = 'FORT LUPTON')";
	} else if (strpos($city, 'FORT MORGAN') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'FT MORGAN' OR RESIDENTIAL_CITY = 'FORT MORGAN')";
	} else if (strpos($city, 'GLENWOOD SPRINGS') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'GLENWOOD SPGS')";
	} else if (strpos($city, 'GREEN MOUNTAIN FALLS') !== false) {	
		$city_stmt = "(RESIDENTIAL_CITY = 'GREEN MTN FLS' OR RESIDENTIAL_CITY = 'GRN MTN FLS')";
	} else if (strpos($city, 'GREENWOOD VILLAGE') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'GREENWOOD VLG')";
	} else if (strpos($city, 'MANITOU SPRINGS') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY = 'MANITOU SPGS')";
	} else if (strpos($city, 'PAGOSA SPRINGS') !== false) {	
		$city_stmt = "(RESIDENTIAL_CITY = 'PAGOSA SPGS')";
	} else if (strpos($city, 'SNOWMASS') !== false) {
		$city_stmt = "(RESIDENTIAL_CITY like 'SNOWMASS%')";
	} else {
		$city_stmt = "(RESIDENTIAL_CITY = '{$city}')";
	}
	return $city_stmt;
	
}
    
// formatStreetName function
// Author: Larry Dunn
// Date: 2008-02-01
function formatStreetName($street_name) {
    $street_name = strtoupper($street_name);
    // Prefix
    $street_name = preg_replace("/^(EAST)\s(.*)/","E $2", $street_name);
    $street_name = preg_replace("/^(E\.)\s(.*)/","E $2", $street_name);
    $street_name = preg_replace("/^(WEST)\s(.*)/","W $2", $street_name);
    $street_name = preg_replace("/^(W\.)\s(.*)/","W $2", $street_name);
    $street_name = preg_replace("/^(NORTH)\s(.*)/","N $2", $street_name);
    $street_name = preg_replace("/^(N\.)\s(.*)/","N $2", $street_name);
    $street_name = preg_replace("/^(NO)\s(.*)/","N $2", $street_name);
    $street_name = preg_replace("/^(NO\.)\s(.*)/","N $2", $street_name);
    $street_name = preg_replace("/^(SOUTH)\s(.*)/","S $2", $street_name);
    //echo "streetname A: ".$street_name."<br>"; // lwd
    $street_name = preg_replace("/^(S\.)\s(.*)/","S $2", $street_name);
    //echo "streetname B: ".$street_name."<br>"; // lwd
    $street_name = preg_replace("/^(SO)\s(.*)/","S $2", $street_name);
    //echo "streetname C: ".$street_name."<br>"; // lwd
    $street_name = preg_replace("/^(SO\.)\s(.*)/","S $2", $street_name);
    //echo "streetname D: ".$street_name."<br>"; // lwd
    $street_name = preg_replace("/^(SAINT)\s(.*)/","ST $2", $street_name);
    // Suffix
    $street_name = str_replace(' AVENUE', ' AV', $street_name);
    $street_name = str_replace(' AVE', ' AV', $street_name);
    $street_name = str_replace(' AVE.', ' AV', $street_name);
    $street_name = str_replace(' AV.', ' AV', $street_name);
    $street_name = str_replace(' BOULEVARD', ' BV', $street_name);
    $street_name = str_replace(' BLVD', ' BV', $street_name);
    $street_name = str_replace(' CIRCLE', ' CL', $street_name);
    $street_name = str_replace(' CIR.', ' CL', $street_name);
    $street_name = str_replace(' CIR', ' CL', $street_name);
    $street_name = str_replace(' COURT', ' CT', $street_name);
    $street_name = str_replace(' DRIVE', ' DR', $street_name);
    $street_name = str_replace(' HIGHWAY', ' HY', $street_name);
    $street_name = str_replace(' HWY', ' HY', $street_name);
    $street_name = str_replace(' LANE', ' LN', $street_name);
    $street_name = str_replace(' LN.', ' LN', $street_name);
    $street_name = str_replace(' MARKET', ' MK', $street_name);
    $street_name = str_replace(' MKT.', ' MK', $street_name);
    $street_name = str_replace(' MKT', ' MK', $street_name);
    $street_name = str_replace(' PLACE', ' PL', $street_name);
    $street_name = str_replace(' PL.', ' PL', $street_name);
    $street_name = str_replace(' PARKWAY', ' PY', $street_name);
    $street_name = str_replace(' PKWY', ' PY', $street_name);
    $street_name = str_replace(' PKWY.', ' PY', $street_name);
    $street_name = str_replace(' PKY', ' PY', $street_name);
    $street_name = str_replace(' PKY.', ' PY', $street_name);
    $street_name = str_replace(' ROAD', ' RD', $street_name);
    $street_name = str_replace(' RD.', ' RD', $street_name);
    $street_name = str_replace(' STREET', ' ST', $street_name);
    $street_name = str_replace(' ST.', ' ST', $street_name);
    $street_name = str_replace(' WAY', ' WY', $street_name);
    $street_name = str_replace(' WY.', ' WY', $street_name);
    // Numbered Streets - first to ninth
    $street_name = str_replace('FIRST','1ST', $street_name);
    $street_name = str_replace('SECOND','2ND', $street_name);
    $street_name = str_replace('THIRD','3RD', $street_name);
    $street_name = str_replace('FOURTH','4TH', $street_name);
    $street_name = str_replace('FIFTH','5TH', $street_name);
    $street_name = str_replace('SIXTH','6TH', $street_name);
    $street_name = str_replace('SEVENTH','7TH', $street_name);
    $street_name = str_replace('EIGHTH','8TH', $street_name);
    $street_name = str_replace('NINTH','9TH', $street_name);
    //
    // 2012-02-12 LWD - remove leading zero logic
    //  $street_name = preg_replace("/(\w)\s1ST/","$1 01ST", $street_name);
    //  $street_name = preg_replace("/(\w)\s01ST/","$1 01ST", $street_name);
    //  $street_name = preg_replace("/^1ST/","01ST", $street_name);
    //  $street_name = preg_replace("/(\w)\s2ND/","$1 02ND", $street_name);
    //  $street_name = preg_replace("/(\w)\s02ND/","$1 02ND", $street_name);
    //  $street_name = preg_replace("/^2ND/","02ND", $street_name);
    //  $street_name = preg_replace("/(\w)\s3RD/","$1 03RD", $street_name);
    //  $street_name = preg_replace("/(\w)\s03RD/","$1 03RD", $street_name);
    //  $street_name = preg_replace("/^3RD/","03RD", $street_name);
    //  $street_name = preg_replace("/(\w)\s(\d)TH/","$1 0$2TH", $street_name);
    //  $street_name = preg_replace("/(\w)\s0(\d)TH/","$1 0$2TH", $street_name);
    //  $street_name = preg_replace("/^(\d)TH/","0$1TH", $street_name);
    
    // Numbered Streets - tenth to twentieth
    $street_name = preg_replace("/^(\w)\sTENTH/","$1 10TH", $street_name);
    $street_name = preg_replace("/^(\w)\sELEVENTH/","$1 11TH", $street_name);
    $street_name = preg_replace("/^(\w)\sTWELTH/","$1 12TH", $street_name);
    $street_name = preg_replace("/^(\w)\sTHIRTEENTH/","$1 13TH", $street_name);
    $street_name = preg_replace("/^(\w)\sFOURTEENTH/","$1 14TH", $street_name);
    $street_name = preg_replace("/^(\w)\sFIFTEENTH/","$1 15TH", $street_name);
    $street_name = preg_replace("/^(\w)\sSIXTEENTH/","$1 16TH", $street_name);
    $street_name = preg_replace("/^(\w)\sSEVENTEENTH/","$1 17TH", $street_name);
    $street_name = preg_replace("/^(\w)\sEIGHTEENTH/","$1 18TH", $street_name);
    $street_name = preg_replace("/^(\w)\sNINETEENTH/","$1 19TH", $street_name);
    $street_name = preg_replace("/^(\w)\sTWENTIETH/","$1 20TH", $street_name);
    $street_name = preg_replace("/^(\w)\sTHIRTIETH/","$1 30TH", $street_name);
    $street_name = preg_replace("/^(\w)\sFORTIETH/","$1 40TH", $street_name);
    $street_name = preg_replace("/^(\w)\sFIFTIETH/","$1 50TH", $street_name);
    $street_name = preg_replace("/^TENTH/","10TH", $street_name);
    $street_name = preg_replace("/^ELEVENTH/","11TH", $street_name);
    $street_name = preg_replace("/^TWELTH/","12TH", $street_name);
    $street_name = preg_replace("/^THIRTEENTH/","13TH", $street_name);
    $street_name = preg_replace("/^FOURTEENTH/","14TH", $street_name);
    $street_name = preg_replace("/^FIFTEENTH/","15TH", $street_name);
    $street_name = preg_replace("/^SIXTEENTH/","16TH", $street_name);
    $street_name = preg_replace("/^SEVENTEENTH/","17TH", $street_name);
    $street_name = preg_replace("/^EIGHTEENTH/","18TH", $street_name);
    $street_name = preg_replace("/^NINETEENTH/","19TH", $street_name);
    $street_name = preg_replace("/^TWENTIETH/","20TH", $street_name);
    $street_name = preg_replace("/^THIRTIETH/","30TH", $street_name);
    $street_name = preg_replace("/^FORTIETH/","40TH", $street_name);
    $street_name = preg_replace("/^FIFTIETH/","50TH", $street_name);
    // 2008-09-26 - These are for the previous weirdness of an extra space between pfx and numeric street name.
    //  $street_name = preg_replace("/^(\w)\s(\d\d)ST/","$1   $2ST", $street_name);
    //  $street_name = preg_replace("/^(\w)\s(\d\d)ND/","$1   $2ND", $street_name);
    //  $street_name = preg_replace("/^(\w)\s(\d\d)RD/","$1   $2RD", $street_name);
    //  $street_name = preg_replace("/^(\w)\s(\d\d)TH/","$1   $2TH", $street_name);
    //  $street_name = preg_replace("/^(\d\d)ST/","  $1ST", $street_name);
    //  $street_name = preg_replace("/^(\d\d)ND/","  $1ND", $street_name);
    //  $street_name = preg_replace("/^(\d\d)RD/","  $1RD", $street_name);
    //  $street_name = preg_replace("/^(\d\d)TH/","  $1TH", $street_name);
    
    return $street_name;
}

?>
