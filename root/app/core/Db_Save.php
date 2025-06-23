<?php
class Db_Save  extends PDO{

    function __construct() {
        parent::__construct("mysql:host=localhost;dbname=".DBNAME.";charset=utf8", DBUSER, DBPASS, []);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    }
    
    public function set_table($table_name) {
        $this->table = $table_name;
        return $this;
    }
    
    public function set_columns($columns) {
        $this->columns = $columns;
        return $this;
    }
    
    public function insert() {
            $success = 0;
            $table_columns  = array();
            $place_holder   = array();
            $column_values  = array();
            
            foreach($this->columns as $key => $val){
                array_push($table_columns, $key);
                array_push($place_holder, "?");
                array_push($column_values, $val);
            }
            $this->query = "INSERT INTO "
                . $this->table
                . " ( " . implode(', ', $table_columns) . " ) VALUES ("
                . implode(',', $place_holder) . ")";
            
            try {
                $stmt = $this->prepare($this->query);
                $_column_count  = count($column_values);
                $param_count    = 0;
                for ($i = 0; $i <$_column_count; $i++) {
                    $param_count++;
                    $stmt->bindValue($param_count, $column_values[$i]);
                }
                $stmt->execute();
                $success = 1;
            } catch (PDOException $ex) {
                echo $ex->getMessage();
            }
           
        return $success;
    }
    
    public function update($where) {
            $table_columns = array();
            $column_values = array();
            
            $w_columns  = array();
            $w_values   = array();
            
            $success = 0;
            
            foreach($this->columns as $key => $val){
                array_push($table_columns, $key);
                array_push($column_values, $val);
            }
            
            foreach($where as $key => $val){
                array_push($w_columns, $key);
                array_push($w_values, $val);
            }
            
            $this->query = "UPDATE " . $this->table. " SET "
                    . " " . implode(" = ?, ", $table_columns) . " = ? WHERE " 
                    . implode(" = ?  AND ", $w_columns) . " = ? " ;
           
            try{
                    $param          = 0;
                    $_cCount  = count($table_columns);
                    $_wCount  = count($w_columns);
                    $stmt     = $this->prepare($this->query);
                    
                    for ($i = 0; $i < $_cCount; $i++) {
                        $param++;
                        $stmt->bindValue($param, $column_values[$i]);
                    }
                    
                    for ($i = 0; $i < $_wCount; $i++) {
                        $param++;
                        $stmt->bindValue($param, $w_values[$i]);
                    }
                    
                    if($stmt->execute()){
                        $success = $stmt->rowCount();
                        if($success==0)
                        {
							$success = -1;
						}
                    }
            } catch (PDOException $ex) {echo $ex->getMessage();}
            return $success;
    }
    
/*-----------------------------------------------------------------------*/    
    public function delete($where){
        $w_columns  = array();
        $w_values   = array();
        $success    = 0;
        
        foreach($where as $key => $val){
                array_push($w_columns, $key);
                array_push($w_values, $val);
            }
            
        $this->query = "DELETE FROM " . $this->table. " WHERE " 
                    . implode(' = ?, AND ', $w_columns) . " = ? " ;
        
        try{
                $_wCount  = count($w_columns);
                $stmt     = $this->prepare($this->query);
                for ($i = 0; $i < $_wCount; $i++) {
                        $stmt->bindValue($i+1, $w_values[$i]);
                }
                if($stmt->execute()){
                    $success = 1;
                }
            } catch (PDOException $ex) {echo $ex->getMessage();}
        return $success;
    }
/*--------------------------------------------------------------------*/
    private $table          = "";
    private $columns        = "";
    private $query          = "";
    
  





}
