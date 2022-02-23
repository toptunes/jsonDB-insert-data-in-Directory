<?php

class JsonDB{

  
    public $db_path;
    public $table_dir;
    public $all_data;
    public $all_data_decoded;

    public function __construct($db_path = __DIR__){
 
        
        $this->db_path = $db_path;
             
        
    }


  
    private function show_in_json_db($search_array){

        // This method tries to find Columns that are not equal to your selected query AND skip them.
        $rows = [];
        foreach ($this->all_data_decoded['data'] as $table_row) {
            foreach ($search_array as $key => $value) {

                if ($table_row[$key] != $value) {

                   continue 2;

                }
            }
            $rows[] = $table_row;
        }
      
     
        return json_encode($rows,JSON_PRETTY_PRINT);
    
       
    }

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
                    throw new Exception("Column $k not found");  
  
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
                    throw new Exception("No value provided for column $key");  
  
                }

               
               

            }
           

        }
        return true;
    }


    private function search_json_db($search_array,$allow_show){
        

        $schema = $this->all_data_decoded['schema'];
      
        foreach ($search_array as $key => $value) {
           
            if (!isset($schema[$key])) {
                throw new Exception("Column $key not found");
                return false;
            }else{

                if($allow_show == 1){

                    return self::show_in_json_db($search_array);

                }
                if($allow_show == 2){
                
                    return self::delete_in_json_db($search_array);

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

            throw new Exception("Table $table_name not found"); 
  
        }
        self::search_json_db($search_array,0);
        self::search_json_db($where_array,0);

        if(!empty($search_array)){


            
            if(self::let_schema_allow_method_2($search_array) ){
                return self::delete_in_json_db($search_array);
            }

        }else{

            $this->all_data_decoded["data"] = []; // delete all data from table 
            
            self::just_put_it(); // and save 

           
        }

        
    }

    public function update($table_name,$set_array = [],$where_array = []){

     
        if(self::check_exists($table_name) == false){

            throw new Exception("Table $table_name not found"); 
  
        }

        if(!empty($set_array)){

            return self::calc_json_db($set_array,$where_array,"update");

        }else{

            return false;

        }

        
    }


    public function insert($table_name,$search_array = []){

     
        if(self::check_exists($table_name) == false){

            throw new Exception("Table $table_name not found");  
  
        }

            return self::insert_json_db($search_array);

       
        

        
    }



    public function select($table_name,$search_array = []){

     
        

            if(self::check_exists($table_name) == false){

                throw new Exception("Table $table_name not found"); 
      
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

$db->select('users', ['first_name' => 'Ali']);


