<?php

class JsonDB{

  
    private $db_path;
    private $table_dir;
    private $all_data;
    private $all_data_decoded;

    public function __construct($db_path = __DIR__){
 
        
        $this->db_path = $db_path;
             
        
    }
    private function get_k_v($object){


    }

  
    private function show_in_json_db($search_array , $method){

        
        // This method tries to find Columns that are not equal to your selected query AND skip them.
        $rows = [];
        $all_rows = [];
        
        $i = 0;
        foreach ($this->all_data_decoded['data'] as $table_row) {
            
            foreach ($search_array as $key => $value) {

                if ($table_row[$key] == $value) {

                   $rows[] = $i;

                }

                
            }
            $all_rows[] = $i;
            $i++;
        }

        if($method == "select"){

            $rows = array_unique($rows);
        
        }

        if($method == "delete"){

            $rows = array_diff($all_rows, $rows);

        }

    
        $table_rows = [];
        foreach ($rows as $key => $value) {
    
            $table_rows[] =  $this->all_data_decoded['data'][$rows[$key]];

        }
        
       

        if($method == "select"){
            return json_encode($table_rows,JSON_PRETTY_PRINT);

        }
      
        if($method == "delete"){
            $this->all_data_decoded['data'] = $table_rows;
            self::just_put_it();
        }
        


      
        
    
       
    }
/*
    private function delete_in_json_db($search_array){

        // This method tries to find Columns that are equal to your selected query AND gather them.

        $rows = [];
        foreach ($this->all_data_decoded['data'] as $table_row) {
            foreach ($search_array as $key => $value) {

                if ($table_row[$key] == $value) {
                    continue 2;
                }
            }
            $rows[] = $table_row;
        }
      
        $this->all_data_decoded['data'] = $rows;
        self::just_put_it();
      
       
    }
*/

    private function check_array_value($search_array,$input){
      
        if($search_array[$input] == ""){

            return 0;
        }else{
            return 1;
        }

    }
    private function let_schema_allow_method_2($search_array){

        $schema = $this->all_data_decoded["schema"];
        foreach ($schema as $key => $vaue) {
            
            foreach ($search_array as $k => $v) {
              
                if(!isset($schema[$k])){
                    $exception = new Exception("Column $k not found");  
                    echo $exception->getMessage();
                    die();
                    continue 1;
                    
                }

            }
        }
        return true;

    }

    private function let_schema_allow($search_array){

        $schema = $this->all_data_decoded["schema"];
        foreach ($schema as $key => $value){
            
            

            foreach ($value as $k => $v) {

               
                if($v== "" && !isset($search_array[$key])){
                    $exception = new Exception("No value provided for column $key");  
                    echo $exception->getMessage();
                    die();
                }

               
               

            }
           

        }
        return true;
    }


    private function search_json_db($search_array,$allow_show){
        

        $schema = $this->all_data_decoded['schema'];
      
        foreach ($search_array as $key => $value) {
           
            if (!isset($schema[$key])) {
                $exception = new Exception("Column $key not found");
                echo $exception->getMessage();
                die();
            }else{

                if($allow_show == 1){

                    return self::show_in_json_db($search_array,"select");

                }
                if($allow_show == 2){
                
                    return self::show_in_json_db($search_array,"delete");

                }

            }
        }


    }

 
 
    private function just_put_it(){

        if(
            file_put_contents($this->table_dir,
                json_encode($this->all_data_decoded,JSON_PRETTY_PRINT)
            )
        ){

            return true;

        }

    }
    private function calc_json_db($set_array = [],$where_array = [],$type){



        self::search_json_db($set_array,0);
        self::search_json_db($where_array,0);
       
        foreach ($this->all_data_decoded["data"] as $key => $value){

            
            foreach ($value as $k => $v) {

               
                foreach ($set_array as $k_set => $v_set) {

                    
                    if(empty($where_array)){
                        
                        if ($k == $k_set){

                            //echo $k."->".$v."\n";
                            if($type == "update"){

                                $this->all_data_decoded["data"][$key][$k_set] = $v_set;

                            }

                            if($type == "delete"){
                                
                                unset($this->all_data_decoded["data"][$key][$k_set]);
                            
                            }
                            

                            
                        }

                        

                    }else{
                        foreach ($where_array as $k_where => $v_where) {
                                //&& $v_where == $v
                            
                                //echo $k."->".$v."\n";

                                /* open this comment and let it run to see how it works
                            echo " set [$v_set] to $k_set"."\n";

                            echo "
                            where 

                            k_where = $k_where => $v_where

                            
                            if ($k == $k_where && $v == $v_where) ){

                                UPDATE 
                            }
                            k = $k
                            v_set = $v_set
                            k_where = $k_where => $v_where

                            "."\n\n\n";

                            echo "
                            "."\n\n\n";*/
                                
                            if ($k == $k_where && $v == $v_where){

                                
                                if($type == "update"){

                                    $this->all_data_decoded["data"][$key][$k_set] = $v_set;

                                }
                                if($type == "delete"){

                                    unset($this->all_data_decoded["data"][$key][$k_set]);

                                }
                                
                            }

                        }

                       
                            

                    }


                   

                }

            }
        
           
            
           // print_r($set_array)."\n";
            //print_r($where_array)."\n";
            
        }
       // print_r(json_encode($this->all_data_decoded["data"],JSON_PRETTY_PRINT));
       
           
      self::just_put_it();
            
    }

    private function insert_json_db($search_array){
        

        $rows = [];
        if(self::let_schema_allow_method_2($search_array)   && self::let_schema_allow($search_array)   ){
            $schema = $this->all_data_decoded["schema"];
            foreach ($schema as $key => $value){

               
                if(isset($search_array[$key])){
                   // echo $key."->".$search_array[$key]."\n";
                    $rows[$key] = $search_array[$key];
                   

                }else{

                    if(isset($schema[$key]["default"])){
                        $rows[$key] = $schema[$key]["default"];
                    }else{
                        $rows[$key] = null;
                    }
                    
                }
                    

             
            }
            array_push($this->all_data_decoded["data"],$rows);
         
           // print_r(json_encode($this->all_data_decoded,JSON_PRETTY_PRINT));

            self::just_put_it();

        }
   
     
 
    }

    private function check_exists($table_name){

        $this->table_dir = $this->db_path."/".$table_name.".json";
        if(file_exists($this->table_dir)){

           
            $this->all_data = file_get_contents($this->table_dir);
            $this->all_data_decoded =json_decode($this->all_data,true);

            return true;
        }else{

            return false;

        }

    }

    public function delete($table_name,$search_array = [],$where_array = []){

        
     
        

        if(self::check_exists($table_name) == false){ 

            $exception = new Exception("Table $table_name not found"); 
            echo $exception->getMessage();
            die();
        }
        self::search_json_db($search_array,0);
        self::search_json_db($where_array,0);

        if(!empty($search_array)){

           
            
            if(self::let_schema_allow_method_2($search_array) ){
                return self::show_in_json_db($search_array,"delete");
            }

        }else{

          
            $this->all_data_decoded["data"] = []; // delete all data from table 
            
            self::just_put_it(); // and save 

           
        }

        
    }

    public function update($table_name,$set_array = [],$where_array = []){

     
        if(self::check_exists($table_name) == false){

            $exception  = new Exception("Table $table_name not found"); 
            echo $exception->getMessage();
            die();
        }

        if(!empty($set_array)){

            return self::calc_json_db($set_array,$where_array,"update");

        }else{

            return false;

        }

        
    }


    public function insert($table_name,$search_array = []){

     
        if(self::check_exists($table_name) == false){

            $exception = new Exception("Table $table_name not found");  
            echo $exception->getMessage();
            die();
        }

            return self::insert_json_db($search_array);

       
        

        
    }



    public function select($table_name,$search_array = []){

     
        

            if(self::check_exists($table_name) == false){

                $exception = new Exception("Table $table_name not found"); 
                echo $exception->getMessage();
                die();
            }
        

            if(!empty($search_array)){

                if(self::let_schema_allow_method_2($search_array) ){
                    echo self::search_json_db($search_array,1);
                }
               
 
             }else{
 
                echo json_encode($this->all_data_decoded["data"],JSON_PRETTY_PRINT);
 
             }
            
       
        
       
    }
}


$db = new JsonDB(__DIR__ . '/db');

$db->select('user3s');

//$db->delete('users', ['first_name' => 'Mohammad']);