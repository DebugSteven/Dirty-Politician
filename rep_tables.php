<?php

/*
File Name : rep_tables.php
Description: This file prints the user's legislative districts based on the contents of $dis. $dis contains the precinct short number, house district, senate district, and congressional district. If it is NULL, meaning the queries didn't yield any results, a voter is directed to their voter registration. The district numbers for house, senate, and congressional determine which politician's table will be printed for the user to view.
Author: J Haigh
Author URI: https://debugsteven.github.io/
*/

require_once(ABSPATH . 'wp-config.php');

function user_output($dis) {
 

    // prints the user's legislative districts
    if($dis != NULL) {
        echo "Your legislative district: <br>";
        echo "Precinct {$dis['Short_Number']}<br>";
        echo "House District {$dis['House_District']}<br>";
        echo "Senate District {$dis['State_Senate_District']}<br>";
        echo "Congressional District {$dis['Congressional_District']}<br>";

        echo "Here's how your politicians fund their campaigns:<br>";
        user_reps($dis);
    }

    else {
        echo "We couldn't find a voter registered at this address!<br>";
        echo "Please try entering your address as it appears on your <a href=\"//www.sos.state.co.us/voter-classic/pages/pub/olvr/findVoterReg.xhtml\">voter registration</a>.<br>";
    }

 }

// finds names of the representatives for each legislative district
function user_reps($dis) {
        
    global $wpdb;
    $table_house = $wpdb->prefix . "house_district";
    $table_senate = $wpdb->prefix . "senate_distrct";
    $table_congress = $wpdb->prefix . "congressional_district";

    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
    mysqli_select_db($con, DB_NAME);
        
    # If the connection failed then send error message.
    if(mysqli_connect_errno()) {
	printf('Could not connect to' . $database .': ' . mysqli_connect_error());
	exit();
    }

    // determines the name of the representative based on the legislative district number in the house, senate, and congress
    $house_rep_query = "SELECT Representative
	    FROM {$table_house}
	    WHERE DISTRICT = '{$dis['House_District']}';";

    $house_rep = mysqli_query($con, $house_rep_query);
	
    $senate_rep_query = "SELECT Senator
	    FROM {$table_senate}
	    WHERE DISTRICT = '{$dis['State_Senate_District']}';";

    $senate_rep = mysqli_query($con, $senate_rep_query);
    
    $congress_rep_query = "SELECT Rep
	    FROM {$table_congress}
	    WHERE DISTRICT = '{$dis['Congressional_District']}';";

    $congress_rep = mysqli_query($con, $congress_rep_query);
    # Close MySQL connection
    mysqli_close($con);


    $rep = array();

    foreach($house_rep as $house_result_row) {
        foreach($house_result_row as $house_rep_name) {
            $rep['house'] = $house_rep_name;
        }    
    }

    foreach($senate_rep as $senate_result_row) {
        foreach($senate_result_row as $senate_rep_name) {
            $rep['senate'] = $senate_rep_name;
        }    
    }

    foreach($congress_rep as $congress_result_row) {
        foreach($congress_result_row as $congress_rep_name) {
            $rep['congress'] = $congress_rep_name;
        }    
    }

    rep_tables($rep);
}

// Finds the wp data chart table to print based on the name of the representative
// The naming convention for each chart was "FirstName LastName Campaign Data"
// Query on the full name of the representative followed by a wildcard
// If there's a id for the table it is printed, otherwise there is no data about that campaign
function rep_tables($rep) {

    global $wpdb;
    $table_wpid = $wpdb->prefix . "wpdatacharts";

    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
    mysqli_select_db($con, DB_NAME);
        
    # If the connection failed then send error message.
    if(mysqli_connect_errno()) {
	printf('Could not connect to' . $database .': ' . mysqli_connect_error());
	exit();
    }

    // queries to determine the table id for a particular politician in the house, senate, and congress
    $house_table_query = "SELECT wpdatatable_id
	    FROM {$table_wpid}
	    WHERE title like '{$rep['house']}%';";

    $house_table = mysqli_query($con, $house_table_query);
	

    $senate_table_query = "SELECT wpdatatable_id
	    FROM {$table_wpid}
	    WHERE title like '{$rep['senate']}%';";

    $senate_table = mysqli_query($con, $senate_table_query);

    $congress_table_query = "SELECT wpdatatable_id
	    FROM {$table_wpid}
	    WHERE title like '{$rep['congress']}%';";

    $congress_table = mysqli_query($con, $congress_table_query);

    # Close MySQL connection
    mysqli_close($con);

    // grab wp data chart id from query result for house, senate, and congressional district
    $table = array();

    if ($house_table != NULL) {
        foreach($house_table as $house_result_row) {
            foreach($house_result_row as $house_id) {
                $table['house'] = $house_id;
            }    
        }
    }

    if ($senate_table != NULL) {
        foreach($senate_table as $senate_result_row) {
            foreach($senate_result_row as $senate_id) {
                $table['senate'] = $senate_id;
            }    
        }
    }

    if ($congress_table != NULL) {
        foreach($congress_table as $congress_result_row) {
            foreach($congress_result_row as $congress_id) {
                $table['congress'] = $congress_id;
            }    
        }
    }

    //displays the tables for the politicians if they exist otherwise prints that data isn't there
    if ($table['house'] != NULL) {
        $house = '[wpdatachart id=' . $table['house'] . ']';
        echo do_shortcode($house);
    }
    else {
        echo "We do not have the data for {$rep['house']}'s campaign yet. Check back later.<br>";
    }

    if ($table['senate'] != NULL) { 
        $senate = '[wpdatachart id=' . $table['senate'] . ']';
        echo do_shortcode($senate);
    }
    else {
        echo "We do not have the data for {$rep['senate']}'s campaign yet. Check back later.<br>";
    }

    if ($table['congress'] != NULL) {
        $congress = '[wpdatachart id=' . $table['congress'] . ']';
        echo do_shortcode($congress);
    }
    else {
        echo "We do not have the data for {$rep['congress']}'s campaign yet. Check back later.<br>";
    }

}

?>
