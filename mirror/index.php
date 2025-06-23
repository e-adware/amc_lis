<?php 
include ('dumper.php');

try {
	$world_dumper = Shuttle_Dumper::create(array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'penguin',
		'db_name' => 'penguin_his',
	));

	// dump the database to gzipped file
	//$world_dumper->dump('tru_col_final.sql.gz');

	// dump the database to plain text file
	$world_dumper->dump('penguin_his.sql');
/*
	$wp_dumper = Shuttle_Dumper::create(array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'penguin',
		'db_name' => 'tru_col_final',
	));

	// Dump only the tables with wp_ prefix
	$wp_dumper->dump('tru_col_final.sql', 'wp_');
	
	$countries_dumper = Shuttle_Dumper::create(array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'penguin',
		'db_name' => 'tru_col_final',
		'include_tables' => array('country_list', 'currency_xrate'), // only include those tables
	));
	$countries_dumper->dump('selected_table.sql.gz');

	// Dump exclude_tables with wp_ prefix
	$world_dumper = Shuttle_Dumper::create(array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'penguin',
		'db_name' => 'tru_col_final',
		'exclude_tables' => array('country_list'), 
	));
	$world_dumper->dump('excluding_tables.sql.gz');
	*/

} catch(Shuttle_Exception $e) {
	echo "Couldn't dump database: " . $e->getMessage();
}
