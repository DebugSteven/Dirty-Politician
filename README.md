Dirty-Politician
================
WordPress plugin to show a user the special interest money their representatives take to run campaigns 

How It Works
------------
+ A valid registered voter's address is accepted in dirty_politician.php
+ If the address is invalid a user is directed to lookup their state voter registration, specifically designed for Colorado.
+ Address is parsed and queries are executed to find legislative districts based on address and then precinct number in rep_finder.php
+ The legislative districts are printed out for the user and queries are executed to find representative names based state house, senate, and congressional district in rep_tables.php
+ The wpDataCharts table is queried based on the representative name followed by a wildcard and if a table id is returned the table for the politician is printed
+ If there is no id returned, a message is printed saying there is no data for $politician yet

Getting Started
---------------
To use this existing plugin, download a zip and upload it onto your own WordPress website. You will also need to download and install wpDataTables 
(wpDataTables Lite may work depending on the amount of data you have) to create the charts that appear for particular politicians.
In order for the SQL queries to work, you'll need your database setup with these tables and attributes:
+ wp_precinct --> Precinct_Number, Short_Number, Congressional_District, State_Senate_District, House_District
+ wp_voters --> Precinct_Number, Residential_Address
+ wp_house_district --> District, Representative
+ wp_senate_district --> District, Senator
+ wp_congressional_district --> District, Rep
+ wp_wpdatacharts --> wpdatatable_id, title (This table is created by the wpDataTables plugin)
