
<?php
include'../../includes/connection.php';

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];
$type = $_GET['typ'];

//~ $date1='2018-03-22';
//~ $date2='2018-03-22';

$mysqlUserName      = "root";
$mysqlPassword      = "penguin";
$mysqlHostName      = "localhost";
$DbName             = "demo_his";
$backup_name        = "demo_his.sql";
$tables             = "";

//or add 5th parameter(array) of specific tables:    array("mytable1","mytable2","mytable3") for multiple tables

	Export_Database($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName,  $tables=false, $backup_name=false, $date1, $date2 );

	function Export_Database($host,$user,$pass,$name,  $tables=false, $backup_name=false, $date1, $date2 )
	{
		$mysqli = new mysqli($host,$user,$pass,$name); 
		$mysqli->select_db($name); 
		$mysqli->query("SET NAMES 'utf8'");

		$queryTables    = $mysqli->query('SHOW TABLES'); 
		while($row = $queryTables->fetch_row()) 
		{ 
			$target_tables[] = $row[0]; 
		}   
		if($tables !== false) 
		{ 
			$target_tables = array_intersect( $target_tables, $tables); 
		}
		foreach($target_tables as $table)
		{
			$check_table_qry = $mysqli->query('SELECT * FROM backup_tables WHERE table_name="'.$table.'"');
			$check_table     =   $check_table_qry->fetch_row();
			
			//if (($key = array_search($table, $selected_tables)) !== false)
			if($check_table[1]==$table)
			{
				if($check_table[2]==1)
				{
					$result         =   $mysqli->query('SELECT * FROM '.$table.' WHERE date between "'.$date1.'" and "'.$date2.'" ');  
				}
				if($check_table[2]==0)
				{
					$result         =   $mysqli->query('SELECT * FROM '.$table);  
				}
				
				$fields_amount  =   $result->field_count;  
				$rows_num=$mysqli->affected_rows;     
				$res            =   $mysqli->query('SHOW CREATE TABLE '.$table); 
				$TableMLine     =   $res->fetch_row();
				$content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

				for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) 
				{
					while($row = $result->fetch_row())  
					{ //when started (and every after 100 command cycle):
						if ($st_counter%100 == 0 || $st_counter == 0 )  
						{
								$content .= "\nINSERT INTO ".$table." VALUES";
						}
						$content .= "\n(";
						for($j=0; $j<$fields_amount; $j++)  
						{ 
							$row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
							if (isset($row[$j]))
							{
								$content .= '"'.$row[$j].'"' ; 
							}
							else 
							{   
								$content .= '""';
							}     
							if ($j<($fields_amount-1))
							{
									$content.= ',';
							}      
						}
						$content .=")";
						//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
						if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) 
						{   
							$content .= ";";
						} 
						else 
						{
							$content .= ",";
						} 
						$st_counter=$st_counter+1;
					}
				} $content .="\n\n\n";
			}
			
		}
		//$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
		$backup_name = $backup_name ? $backup_name : $name."_from_".$date1."_to_".$date2.".sql";
		header('Content-Type: application/octet-stream');   
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
		echo $content; exit;
	}
?>
