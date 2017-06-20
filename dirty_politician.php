<?php
/**
 * @package Dirty Politician
 * @version 1.0
 */
/*
Plugin Name: Dirty Politician
Description: Finds a Colorado user's precinct number based off a valid street address and returns the special interest money taken by the politicians that represent that precinct from local to state politicians.
Author: J Haigh
Version: 1.0
Author URI: https://debugsteven.github.io/
*/

require_once('rep_finder.php');
require_once('rep_tables.php');

function dwwp_user_street_address() { 

    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
    echo '<p>';
    
    // This is the form text field for the Street Number
    echo 'Address Line 1 <br />';
    echo '<input type="text" name="cf-street-address" pattern="[a-zA-Z0-9. ]+" value="' . ( isset( $_POST["cf-street-address"] ) ? esc_attr( $_POST["cf-street-address"] ) : '' ) . '" size="90" />';
    echo '</p>';
    
    // This is the form text field for the Street Name
    echo '<p>';
    echo 'Address Line 2 <br />';
    echo '<input type="text" name="cf-unit-num" pattern="[a-zA-Z0-9.#- ]+" value="' . ( isset( $_POST["cf-unit-num"] ) ? esc_attr( $_POST["cf-unit-num"] ) : '' ) . '" size="20" />';
    echo '</p>';
    
    // This is the form text field for the City
    echo '<p>';
    echo 'City <br />';
    echo '<input type="text" name="cf-city" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["cf-city"] ) ? esc_attr( $_POST["cf-city"] ) : '' ) . '" size="40" />';
    echo '</p>';
    
    // This is the submit button for the form
    echo '<p><input type="submit" name="cf-submitted" value="Submit"/></p>';
    echo '</form>';

}
    
function dwwp_sanitize_input() {
    // if the form has been submitted we will sanitize the text
    // text sanitization involves removing extra white space, tab, and new line characters
    if (isset( $_POST['cf-submitted'])) {
        $street_address = sanitize_text_field( $_POST["cf-street-address"]);
        $city = sanitize_text_field( $_POST["cf-city"]);
        $dis = user_queries($street_address, $city);
        user_output($dis);
    }
}

// This code defines the shortcut to use this code file on any page
function cf_shortcode() {
    ob_start();
    
    dwwp_user_street_address();
    dwwp_sanitize_input();
    
    return ob_get_clean();
}
    
add_shortcode( 'dirty-politician', 'cf_shortcode' );


?>
