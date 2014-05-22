<?php

//print_r($_SESSION);
if(isset($_SESSION["userid"])):
	db_connect();
	show_connection_details();
	process_submission();
else:
	echo "<font color='red'>Unauthorized access to this page. Please log in.</font>";
	echo "<br><a href='$_SERVER[PHP_SELF]'>Try Again</a>";
endif;
 

function db_connect(){
	$db_conn = mysql_connect('localhost',$_SESSION["dbuser"],$_SESSION["dbpass"]) or die("Cannot query 14: ".mysql_error());
	mysql_select_db($_SESSION["dbname"],$db_conn) or die("Cannot query 15: ".mysql_error());
}


function show_connection_details(){
	$q_user = mysql_query("SELECT user_lastname, user_firstname, user_id FROM game_user ORDER by user_lastname ASC, user_firstname ASC");
	$q_brgy = mysql_query("SELECT barangay_id, barangay_name FROM m_lib_barangay") or die("Cannot query 21: ".mysql_error());


	echo "<table border='1' width='50%'>";
	echo "<tr><td>Current active database: </td><td>".$_SESSION["dbname"]."</td></tr>";
	echo "<tr><td>Select End User Account to Sync</td>";
	echo "<td><select name='sel_user'>";	
	echo "<option value='all'>All User Accounts</option>";
	while(list($lname,$fname,$uid)=mysql_fetch_array($q_user)){
		echo "<option value='$uid'>$lname, $fname</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><td>Select Barangay/s to Sync</td>";
	echo "<td><select name='sel_barangay' size='10'>";
	echo "<option value='all'>All Barangays</option>";
	while(list($brgy_id,$brgy_name)=mysql_fetch_array($q_brgy)){
		echo "<option value='$brgy_id'>$brgy_name</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><td colspan='2'>Note: Only family folders, patients and consultations from the selected barangays will be included in the sync file.</td>";
	echo "<tr><td colspan='2'><input type='submit' value='Create Sync File'></td>";	
}


?>
