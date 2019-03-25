<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 12/21/2017
 * Time: 6:22 AM
 */
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Kogi Eric
 *
 */

/**
 * Change the queries in this methods to correspond with your sql queries
 * this is also the class you should add any other crud method for all the api calls
 *
 *
 * NOTE YOU NEED TO INTRODUCE YOUR CONNECTION TO DB HERE BY INCLUDING THE CONNECT.PHP FILE
 *
 *
 * IE private $conn
 *
 * @author Kogi Eric
 */


/**
 * Class DbHandler
 */

class LocationsDbHandler
{


    /**
     * @var PDO
     */
    /**
     * DbHandler constructor.
     */


    /**
     * @return mixed
     */

    /**
     * @var PDO
     */
    private $conn;

    /**
     * DbHandler constructor.
     */
    function __construct()
    {
       // require_once dirname(__FILE__) . '../configs/DbConnect.php';
        require_once dirname(__FILE__) . '/../configs/DbConnect.php';
       // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }


    
    public function Countries(){
       
        $stmt = $this->conn->prepare("SELECT * FROM operating_countries  ");
         
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }


    public function Counties($CountryId){
       
        $stmt = $this->conn->prepare("SELECT * FROM operating_counties_state WHERE country_id = ?  ");
         $stmt->bind_param("i", $CountryId);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
       
         
    }

    public function towns($CountyId){
       
        $stmt = $this->conn->prepare("SELECT * FROM operating_towns WHERE county_id = ?  ");
         $stmt->bind_param("i", $CountyId);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
       
         
    }

   


    
    
    
    


}
?>
