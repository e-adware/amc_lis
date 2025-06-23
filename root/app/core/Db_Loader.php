<?php

class Db_Loader extends PDO{

    function __construct() {
        parent::__construct("mysql:host=localhost;dbname=".DBNAME.";charset=utf8", DBUSER, DBPASS, []);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    }
    
    
    
    private $table          = "";
    private $where_column   = array();
    private $where_values   = array();
    private $query          = "";

    
    public function select($column, $table) {
        $this->table = $table;
        $this->query = "SELECT " . implode(", ", $column) . " FROM " .$table ." ";
        //echo $this->query;
        return $this;
    }
    
    //~ public function select_distinct($columns, $table) {
		//~ $columnsStr = is_array($columns) ? implode(", ", array_map(function($col) use ($table) {
			//~ return $table . '.' . $col;
		//~ }, $columns)) : $columns;
		//~ $this->query = "SELECT DISTINCT " . $columnsStr . " FROM " . $table;
		//~ return $this;
	//~ }
	
	public function select_distinct($columns, $table) {
        $columns = implode(', ', $columns);
        $this->query = "SELECT DISTINCT $columns FROM $table";
        //echo $this->query;
        return $this;
    }
    
    public function whereNotEmpty($column) {
		$this->query .= (strpos($this->query, 'WHERE') !== false ? " AND " : " WHERE ") . "$column!=''";
		return $this;
	}
    
    public function whereLikeString($column, $like) {
		$this->query .= (strpos($this->query, 'WHERE') !== false ? " AND " : " WHERE ") . "`$column` like '%".$like."%'";
		return $this;
	}
    public function whereLike($column, $table, $field, $like) {
        $this->table = $table;
        $this->query = "SELECT " . implode(", ", $column) . " FROM " .$table ." WHERE ".$field." like '%".$like."%'";
        return $this;
    }
    
    public function whereBetweenDates($column, $startDate, $endDate) {
        $this->query .= (strpos($this->query, 'WHERE') !== false ? " AND " : " WHERE ") . "`$column` BETWEEN ? AND ?";
        array_push($this->where_values, $startDate, $endDate);
        return $this;
    }
    
    public function hasGreaterThen($column, $value){
		$this->query .= (strpos($this->query, 'WHERE') !== false ? " AND " : " WHERE ") . "`$column` > '".$value."'";
		return $this;
	}
    
    public function hasGreaterThenEqual($column, $value){
		$this->query .= (strpos($this->query, 'WHERE') !== false ? " AND " : " WHERE ") . "`$column` >= '".$value."'";
		return $this;
	}
	
    public function count($column, $table){
        $total = 0;
        $this->table = $table;
        $this->query = "SELECT COUNT( ". $column ." ) AS TOTAL FROM " .$table ." ";
        return $this;
    }
    
    
    public function sum($column, $table){
        $total = 0;
        $this->table = $table;
        $this->query = "SELECT SUM( ". $column ." ) AS TOTAL FROM " .$table ." ";
        return $this;
    }
    
    
    public function where($w) {
        if(count($w)> 0 ){
              foreach($w as $key => $val){
                  array_push($this->where_column, $key);
                  array_push($this->where_values, $val);
        }
         $this->query = $this->query . " WHERE ". implode("= ? AND ", $this->where_column) . "= ? ";
        }
        return $this;
    }
    
   
    public function filter($query_part, $values) {
        if(count($values)> 0 ){
              foreach($values as $val){
                  array_push($this->where_values, $val);
              }
         $this->query = $this->query . " WHERE ". $query_part;
        }
        return $this;
    }
   
    
    public function limit($limit) {
         $this->query = $this->query . " LIMIT ". $limit ." ";
         return $this;
    }
    
    public function offset($offset) {
         $this->query = $this->query . " OFFSET ". $offset ." ";
         return $this;
    }
        
    public function order($column, $order_type) {
         $this->query = $this->query . " ORDER BY ". $column ." " . $order_type;
         return $this;
    }
    
    public function group_by($columns) {
         $this->query = $this->query . " GROUP BY ". $columns;
         return $this;
    }
    
    
    public function inner_join($table_1, $column_1, $column_2 ) {
         $this->query = $this->query . " INNER JOIN ". $table_1 ." ON " . $column_1 . " = " . $column_2;
         return $this;
    }
    
    public function inner_join_multi($table_1, $columns_1, $columns_2 ) {
		if(count($columns_1) == count($columns_2))
		{
			$join_conditions = [];
			for ($i = 0; $i < count($columns_1); $i++)
			{
				$join_conditions[] = $columns_1[$i] . " = " . $columns_2[$i];
			}

			$this->query = $this->query . " INNER JOIN " . $table_1 . " ON " . implode(" AND ", $join_conditions);
			return $this;
		}
		else
		{
			throw new Exception("The number of columns in each array must be the same");
		}
    }
    
    
    public function whereNotInTable($table, $column) {
        $this->query .= " WHERE ".$column." NOT IN (SELECT " . $column . " FROM " . $table . ")";
        return $this;
    }
    
    
    public function fetch_all() {
       // echo $this->query;
        $row = null;
            try{
                $stmt = $this->prepare($this->query);
                
                if(count($this->where_values)>0){
                    $stmt->execute($this->where_values);
                }
                else{
                    $stmt->execute();
                }
                
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $ex){echo $ex->getMessage();}
        return $row;
    }
    
    
    
    public function fetch_row() {
        // echo $this->query;
        $row = null;
            try{
                $stmt = $this->prepare($this->query);
                if(count($this->where_values)>0){
                    $stmt->execute($this->where_values);
                }
                else{
                    $stmt->execute();
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $ex){echo $ex->getMessage();}
        return $row;
    }
        
    public function fetch_model($model_name) {
        $row = null;
            try{
                $stmt = $this->prepare($this->query);
                if(count($this->where_values)>0){
                    $stmt->execute($this->where_values);
                }
                else{
                    $stmt->execute();
                }
                $stmt->setFetchMode(PDO::FETCH_CLASS, $model_name);
                $row = $stmt->fetch();
            }
            catch(PDOException $ex){echo $ex->getMessage();}
        return $row;
    }
    

    
    public function setQuery($query){
        $this->query = $query;
        //echo $this->query;
        return $this;
    }
    
    

}
