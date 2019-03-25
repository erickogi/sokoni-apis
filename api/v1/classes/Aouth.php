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

class DbHandler
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
    
    public function login($email,$password){
       
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE customers.email = ?  AND customers.password = ? ");
        $stmt->bind_param("ss", $email,$password);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }
        
      
        
    }

    public function getUser($phone){

       
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE customers.mobile = ? ");
        $stmt->bind_param("s", $phone);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }
        
      
        
    }


    public function updateProfile($cust_id,$json_data){
        $stmt = $this->conn->prepare("UPDATE customers SET  customers.first_name = ?,customers.last_name = ?,
         customers.email = ?,customers.mobile = ? WHERE customers.id = ?");
        $stmt->bind_param("ssssi",$json_data["first_name"], $json_data["last_name"],$json_data["email"],$json_data["mobile"],$cust_id);
        $result =$stmt->execute();
        //$result = $stmt->get_result();
        
      //  $num_rows = $stmt->num_rows;
        $stmt->close();

       // echo json_encode($result);
      
        return $result;
    }

    public function getUserProfile($cust_id){
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE customers.id = ? ");
        $stmt->bind_param("i", $cust_id);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }

    }



    public function addBilling($cust_id,$billingId,$detail){
       
        $stmt = $this->conn->prepare("INSERT INTO customer_billing ( customer_billing.cust_id, customer_billing.billing_id,
        customer_billing.detail)
        VALUES(?,?,?)");

        $stmt->bind_param("iis", $cust_id,$billingId,$detail);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();


       

        if($num_rows>0){
            return false;
        }else{
            return true;

        }





    }
   

    public function customerBillings($cust_id){
            $stmt = $this->conn->prepare("SELECT * FROM customer_billing WHERE cust_id = ?  ");
             $stmt->bind_param("i", $cust_id);
            
             $stmt->execute();
             
             
             $result = $stmt->get_result();
             $num_rows = $result->num_rows;
             
             $stmt->close();
     
           
             
             if($num_rows>0){
                 while ($row = $result->fetch_assoc()) {
                   $results[] = $row;
               }
               $defaultAddress=$this->getDefaultAddress($cust_id)["default_billing"];
             
               foreach($results as $key => &$billing) {
   
                   $bdata=$this->getBilling($billing["billing_id"]);
                   
                   $billing["name"]=$bdata["name"];
                   $billing["image"]=$bdata["image"];
                 //  $billing["detail"]=$bdata["detail"];
                  
                  
   
   
                
                   if($defaultAddress!=null&&$defaultAddress==$billing["id"]){
                       $billing["default"]=true;
                  
                   }
                   else{
                       $billing["default"]=false;
                  
                   }
                  
                  
               }
   
   
   
               return $results;
             }else{
                 return NULL;
     
             }
             
           
             
       }

       public function getBilling($id){
        $stmt = $this->conn->prepare("SELECT * FROM billing_methods WHERE billing_methods.id = ? ");
        $stmt->bind_param("i", $id);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }
        
       }







    public function addAddress($cust_id,$name,$phone,$address,$lat,$lon,$desc,$countryId,$countyId,$townId,$street,$isDefault){
       
                $stmt = $this->conn->prepare("INSERT INTO customer_address ( customer_address.cust_id, customer_address.name,
                customer_address.phone, customer_address.address, customer_address.latitude, customer_address.longitude, customer_address.description,
                customer_address.country, customer_address.state, customer_address.town, customer_address.street )
                VALUES(?,?,?,?,?,?,?,?,?,?,?)");

                $stmt->bind_param("issssssssss", $cust_id,$name,$phone,$address,$lat,$lon,$desc,$countryId,$countyId,$townId,$street);

                $stmt->execute();

                $stmt->store_result();
                $num_rows = $stmt->num_rows;
                $stmt->close();


                if($isDefault==true){
                  //  $this->getDefaultAddressId($cust_id);
                }

                if($num_rows>0){
                    return false;
                }else{
                    return true;

                }





}
    
    public function register($firstName,$lastName,$email,$mobile,$password)
    {

        $stmt = $this->conn->prepare("INSERT INTO customers (customers.first_name, customers.last_name, customers.email, customers.mobile,  customers.password)
           
        VALUES(?,?,?,?,?)");

        $stmt->bind_param("sssss", $firstName,$lastName,$email,$mobile,$password);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }


    }
    public function updateToken($id,$token){
        $stmt = $this->conn->prepare("UPDATE customers SET token = ? WHERE customers.id = ?");
        $stmt->bind_param("si",$token, $id);
        $result =$stmt->execute();
        //$result = $stmt->get_result();
        
      //  $num_rows = $stmt->num_rows;
        $stmt->close();

       // echo json_encode($result);
      
        return $result;
    }

    function authenticateToken($token) {
        $stmt = $this->conn->prepare("SELECT id FROM customers WHERE customers.token = ? ");
        $stmt->bind_param("s", $token);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc()["id"];
        }else{
            return NULL;

        }
    }
   

    public function getDefaultAddressId($cust_id){
        $stmt = $this->conn->prepare("SELECT MAX(id)  FROM customer_address WHERE cust_id = ?  ");
        $stmt->bind_param("i", $cust_id);
       
        $result =$stmt->execute();
        
        
        $stmt->close();

        
        
         $this->changeDefaultAddress($result,$cust_id);

    }
    

    public function getCustId($email,$password,$token)
    {  
      
        $stmt = $this->conn->prepare("UPDATE customers SET token = ? WHERE customers.email = ?  AND customers.password = ?");
        $stmt->bind_param("sss",$token, $email, $password);
        $result =$stmt->execute();
        //$result = $stmt->get_result();
        
      //  $num_rows = $stmt->num_rows;
        $stmt->close();

       // echo json_encode($result);
      
        return $result;
    }

    public function isUserExists($email,$mobile)
    {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE customers.email = ?  OR customers.mobile = ?");
        $stmt->bind_param("ss", $email,$mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function  getDefaultAddress($cust_id){
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = ? ");
          $stmt->bind_param("i", $cust_id);
         
          $result =$stmt->execute();
          
         
            
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
//            echo "result -  ".$result; 
            return $result->fetch_assoc();
        }else{
            return NULL;

        }

        
    }

    public function changeDefaultBillings($id,$cust_id){
        $stmt = $this->conn->prepare("UPDATE  customers SET default_billing= ? WHERE id = ?  ");

        $stmt->bind_param("ii", $id,$cust_id);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }
    }
    
    public function changeDefaultAddress($id,$cust_id){

        $stmt = $this->conn->prepare("UPDATE  customers SET default_address= ? WHERE id = ?  ");

        $stmt->bind_param("ii", $id,$cust_id);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }
    } 

    public function deleteBilling($id){
        $stmt = $this->conn->prepare("DELETE FROM  customer_billing WHERE id = ? ");

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }


    }
  

    public function deleteAddress($id){
        $stmt = $this->conn->prepare("DELETE FROM  customer_address WHERE id = ? ");

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }


    }
    public function getCountry($id){
        $stmt = $this->conn->prepare("SELECT * FROM operating_countries WHERE id = ? ");
        $stmt->bind_param("i", $id);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }
    }
    public function getCounty($id){
        $stmt = $this->conn->prepare("SELECT * FROM operating_counties_state WHERE id = ? ");
        $stmt->bind_param("i", $id);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }
    }
    public function getTown($id){
        $stmt = $this->conn->prepare("SELECT * FROM operating_towns WHERE id = ? ");
        $stmt->bind_param("i", $id);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return NULL;

        }
    }
    public function getCustomerDefaultAddress($cust_id){
        $defaultAddress=$this->getDefaultAddress($cust_id)["default_address"];
        $stmt = $this->conn->prepare("SELECT * FROM customer_address WHERE id = ?  ");
          $stmt->bind_param("i", $defaultAddress);
         
          $stmt->execute();
          
          
          $result = $stmt->get_result();
          $num_rows = $result->num_rows;
          
        $stmt->close();
        if($num_rows>0){
            $address= $result->fetch_assoc();
               $country=$this->getCountry($address["country"]);
                $county=$this->getCounty($address["state"]);
                $town=$this->getTown($address["street"]);

                $address["country"]=$country["name"];
                $address["state"]=$county["name"];
                $address["town"]=$town["name"];
                $address["street"]=$town["name"];
                $address["default"]=true;
                $address["shippingFee"]=$town["shipping_fee"];


                return $address;


        }else{
            return NULL;

        }
  



    }

    public function getCustomerDefaultBilling($cust_id){
        $defaultBilling=$this->getDefaultAddress($cust_id)["default_billing"];
        $stmt = $this->conn->prepare("SELECT * FROM customer_billing WHERE id = ?  ");
          $stmt->bind_param("i", $defaultBilling);
         
          $stmt->execute();
          
          
          $result = $stmt->get_result();
          $num_rows = $result->num_rows;
          
        $stmt->close();
        if($num_rows>0){
               $billing= $result->fetch_assoc();
               $bdata=$this->getBilling($billing["billing_id"]);
        
               $billing["name"]=$bdata["name"];
               $billing["image"]=$bdata["image"];
             //  $billing["detail"]=$bdata["detail"];
               $billing["default"]=true;
              
       
       


                return $billing;


        }else{
            return NULL;

        }
  



    }






    public function customerAddress($cust_id){
     //   echo $cust_id;
          $stmt = $this->conn->prepare("SELECT * FROM customer_address WHERE cust_id = ?  ");
          $stmt->bind_param("i", $cust_id);
         
          $stmt->execute();
          
          
          $result = $stmt->get_result();
          $num_rows = $result->num_rows;
          
          $stmt->close();
  
        
          
          if($num_rows>0){
              while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            $defaultAddress=$this->getDefaultAddress($cust_id)["default_address"];
          //  echo "default -  ".$defaultAddress; 
            foreach($results as $key => &$address) {

                $country=$this->getCountry($address["country"]);
                $county=$this->getCounty($address["state"]);
                $town=$this->getTown($address["street"]);

                $address["country"]=$country["name"];
                $address["state"]=$county["name"];
                $address["town"]=$town["name"];
                $address["street"]=$town["name"];
                $address["shippingFee"]=$town["shipping_fee"];



             //   echo "id -  ".$address["id"]; 
                if($defaultAddress!=null&&$defaultAddress==$address["id"]){
                    $address["default"]=true;
               
                }
                else{
                    $address["default"]=false;
               
                }
               
               
            }



            return $results;
          }else{
              return NULL;
  
          }
          
        
          
    }
    public function getAddress($id){
        $stmt = $this->conn->prepare("SELECT * FROM customer_address WHERE id = ?  ");
           $stmt->bind_param("i", $id);
          
           $stmt->execute();
        
        
           $result = $stmt->get_result();
           $num_rows = $result->num_rows;
           
           $stmt->close();
   
           echo $id;
          
           
           if($num_rows>0){
               $address= $result->fetch_assoc();

               $country=$this->getCountry($address["country"]);
                $county=$this->getCounty($address["state"]);
                $town=$this->getTown($address["street"]);

                $address["country"]=$country["name"];
                $address["state"]=$county["name"];
                $address["town"]=$town["name"];
                $address["street"]=$town["name"];
                $address["shippingFee"]=$town["shipping_fee"];


                return $address;
           }else{
               return NULL;
   
           }
           
         
           
     }


    public function customerBilling($cust_id){
       
        $stmt = $this->conn->prepare("SELECT * FROM customer_billing WHERE cust_id = ?  ");
         $stmt->bind_param("i", $cust_id);
        
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


    public function customerFavorites($cust_id){
       
        $stmt = $this->conn->prepare("SELECT * FROM customers_favorites WHERE cust_id = ?  ");
         $stmt->bind_param("i", $cust_id);
        
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


    public function customerCart($cust_id){
       
        $stmt = $this->conn->prepare("SELECT * FROM customer_cart WHERE cust_id = ?  ");
         $stmt->bind_param("i", $cust_id);

       //  echo $cust_id;
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
             //echo json_encode($result);
            return $result->fetch_assoc();
         }else{
             return null;
 
         }
         
       
         
    }


    public function customerCartItems($cart_id){
       
        $stmt = $this->conn->prepare("SELECT * FROM customer_cart_items WHERE cart_id = ?  ");
         $stmt->bind_param("i", $cart_id);
        
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
    public function customerCartItemsQuantity($cart_id){
       
        $stmt = $this->conn->prepare("SELECT SUM(quantity) AS value_sum FROM customer_cart_items WHERE cart_id = ?  ");
         $stmt->bind_param("i", $cart_id);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         // return $num_rows;
 
          if($num_rows>0){
            //$row["value"]
            $data=$result->fetch_assoc()["value_sum"];
            if($data!=null){
                return $data;
            }
            return 0.0;
        }else{
            return 0.0;

        }
         
       
         
    }

    public function customerCompletedOrders($cust_id){
        $notCompleted=2;
        $stmt = $this->conn->prepare("SELECT * FROM customer_orders WHERE cust_id = ? AND order_status > ?  ");
        $stmt->bind_param("ii", $cust_id,$notCompleted);
       
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

    public function customerPendingOrders($cust_id){
        $Completed=3;
        $notCompleted=2;
        $stmt = $this->conn->prepare("SELECT * FROM customer_orders WHERE cust_id = ? AND order_status > ?  ");
        $stmt->bind_param("ii", $cust_id,$notCompleted);
       
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
    
    
    public function billingMethods(){
        $stmt = $this->conn->prepare("SELECT * FROM billing_methods  ");
        $stmt->bind_param("i", $cart_id);
       
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
