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

class CartDbHandler
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

    public function checkIfCustomerCartExists($cust_id){
        $stmt = $this->conn->prepare("SELECT * FROM customer_cart WHERE cust_id = ?  ");
        $stmt->bind_param("i", $cust_id);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return null;

        }
        
    }

    public function insertCartItem($cust_id,$product_id,$quantity,$variations){

        $existingCart=$this->checkIfCustomerCartExists($cust_id);
        $cartId="";
        if($existingCart!=null){
            

            $cartId=$existingCart["id"];

        }else{
            $cartId=$this->createCart($cust_id)["id"];

        }
        
        
        $cartItemId=$this->createCartItems($cartId,$product_id,$quantity);

        foreach ($variations as $variation) {
            
            
            $this->createCartItemVariation($cartItemId["id"],$variation);
        }


           $cart= $this->customerCart($cust_id);
        // echo json_encode($cart);
            $cart['items_quantity'] = $this->customerCartItemsQuantity($cart['id']);
           // $response["cart"] = $cart;


            return $cart;
            

        
    }

    public function createCart($cust_id){
        $stmt = $this->conn->prepare("INSERT INTO customer_cart

        (customer_cart.cust_id)
           
        VALUES(?)");

        $stmt->bind_param("i", $cust_id);

        $stmt->execute();
       // $id = $this->conn->lastInsertId();
       // mysqli_insert_id($con)

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       


       
       
        return $this->checkIfCustomerCartExists($cust_id);
      
       
      
       
    }
    //$_POST["amount"],$_POST["phone"],$_POST["orderId"],$_POST["firebaseToken"]
    public function pay($amount,$phone,$orderId,$firebasetoken){
        $stk_request_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $outh_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';


    $safaricom_pass_key = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $safaricom_party_b = "174379";
    $safaricom_bussiness_short_code = "174379";

    //$safaricom_Auth_key = "hAVnRxa2UOjyAnydVJMG31A0OuDDCxm5";
    $safaricom_Auth_key = "a76Iw1pLxVjk9GkiIqAWaNLalSC16rM3";
    
    //$safaricom_Secret = "UcpmdCdI8bAakdgm";

    $safaricom_Secret = "H6G0EKL8h4hNEl2h";


    $outh = $safaricom_Auth_key . ':' . $safaricom_Secret;


    $curl_outh = curl_init($outh_url);
    curl_setopt($curl_outh, CURLOPT_RETURNTRANSFER, 1);

    $credentials = base64_encode($outh);
    curl_setopt($curl_outh, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_outh, CURLOPT_HEADER, false);
    curl_setopt($curl_outh, CURLOPT_SSL_VERIFYPEER, false);

    $curl_outh_response = curl_exec($curl_outh);

    $json = json_decode($curl_outh_response, true);


    $time = date("YmdHis", time());

    $password = $safaricom_bussiness_short_code . $safaricom_pass_key . $time;


    $curl_stk = curl_init();
    curl_setopt($curl_stk, CURLOPT_URL, $stk_request_url);
    curl_setopt($curl_stk, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $json['access_token'])); //setting custom header

    $amount=1;

    $curl_post_data = array(

        'BusinessShortCode' => '174379',
        'Password' => base64_encode($password),
        'Timestamp' => $time,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => '174379',
        'PhoneNumber' => $phone,
        'CallBackURL' => 'http://calista.co.ke/sokoni/api/v1/payment/callback.php?orderId='. urlencode($orderId).'&ftoken='. urlencode($firebasetoken),
        'AccountReference' => '4352',
        'TransactionDesc' => $phone
    );


    $data_string = json_encode($curl_post_data);

    curl_setopt($curl_stk, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_stk, CURLOPT_POST, true);
    curl_setopt($curl_stk, CURLOPT_HEADER, false);
    curl_setopt($curl_stk, CURLOPT_POSTFIELDS, $data_string);

    $curl_stk_response = curl_exec($curl_stk);

// <script>
// alert(""$curl_stk_response);

// </script>
    $testjason = json_decode($curl_stk_response);

    if($testjason->ResponseCode == 0){
        return "Request made successfuly";
    }else{
        return "Something went wrong, please try again";
    }


   // return $curl_stk_response;
      //  }

    }
    public function insertIntoOrders($cust_id,$data){
       // echo $data["totalPrice"];
       //error_log(json_encode($data["defaultBilling"]));
         
       $defaultBilling=json_encode($data["defaultBilling"]);
       $defaultAddress=json_encode($data["defaultAddress"]);
       $cartItems=json_encode($data["cartItems"]);
       $cart=json_encode($data["cart"]);
       $totalPrice=$data["totalPrice"];
       $paymentStatus=0;
       $orderStatus=1;




        $date = new DateTime('now', new DateTimeZone('Africa/Nairobi'));
        $code=$date->format('H:m:s')."".$cust_id;

        $stmt = $this->conn->prepare("INSERT INTO customer_orders

        (customer_orders.cust_id,customer_orders.code,customer_orders.amount,customer_orders.payment_status
        ,customer_orders.order_status,customer_orders.defaultBilling,customer_orders.defaultAddress,customer_orders.cartItems,customer_orders.cart)
           
        VALUES(?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param("issiissss", $cust_id,$code,$totalPrice,$paymentStatus,$orderStatus, $defaultBilling, $defaultAddress, $cartItems, $cart);

        $stmt->execute();
       // $id = $this->conn->lastInsertId();
       // mysqli_insert_id($con)

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       


       
       
        return $this->getOrder($code,$cust_id);
      

    }
    
    public function updateOrderNotPaid($orderId,$details){
        $stmt = $this->conn->prepare("UPDATE  customer_orders SET payment_status= 2, payment_details= ? WHERE id = ?  ");

        $stmt->bind_param("si", $details,$orderId);

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

    public function updateOrderPaid($orderId,$details){
        $stmt = $this->conn->prepare("UPDATE  customer_orders SET payment_status= 1, payment_details= ? WHERE id = ?  ");

        $stmt->bind_param("si", $details,$orderId);

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
            $row["defaultAddress"]= json_decode($row["defaultAddress"], true);
            $row["defaultBilling"]= json_decode($row["defaultBilling"], true);
 
            $row["cartItems"]= json_decode($row["cartItems"], true);
            $row["cart"]= json_decode($row["cart"], true);
 
            $row["orderStatusName"]= $this->getOrderStatus($row["order_status"]);
            $row["paymentStatusName"]= $this->getPaymentStatus($row["payment_status"]);
            

               $results[] = $row;
           }
           return $results;
        }else{
            return NULL;

        }
    }

    public function customerPendingOrders($cust_id){
        $Completed=3;
       
        $stmt = $this->conn->prepare("SELECT * FROM customer_orders WHERE cust_id = ? AND order_status < ?  ");
        $stmt->bind_param("ii", $cust_id,$Completed);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

      
        
        if($num_rows>0){
           while ($row = $result->fetch_assoc()) {

            $row["defaultAddress"]= json_decode($row["defaultAddress"], true);
           $row["defaultBilling"]= json_decode($row["defaultBilling"], true);

           $row["cartItems"]= json_decode($row["cartItems"], true);
           $row["cart"]= json_decode($row["cart"], true);

           $row["orderStatusName"]= $this->getOrderStatus($row["order_status"]);
           $row["paymentStatusName"]= $this->getPaymentStatus($row["payment_status"]);
           
           
               $results[] = $row;
           }
           return $results;
        }else{
            return NULL;

        }
    }
    public function  getOrder($code,$cust_id){
        $stmt = $this->conn->prepare("SELECT * FROM customer_orders WHERE code = ?  ");
        $stmt->bind_param("s", $code);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){

            $cartNow=$this->clearCart($cust_id);
            $data=$result->fetch_assoc();

            
           $data["defaultAddress"]= json_decode($data["defaultAddress"], true);
           $data["defaultBilling"]= json_decode($data["defaultBilling"], true);

           $data["cartItems"]= json_decode($data["cartItems"], true);
           $data["cart"]= json_decode($data["cart"], true);

           $data["orderStatusName"]= $this->getOrderStatus($data["order_status"]);
           $data["paymentStatusName"]= $this->getPaymentStatus($data["payment_status"]);
           $data["cartNow"]= $cartNow;
           
           
           
           

             return $data;
        }else{
            return null;

        }
    }
    public function  getCustOrder($orderId){
        $stmt = $this->conn->prepare("SELECT * FROM customer_orders WHERE id = ?  ");
        $stmt->bind_param("i", $orderId);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){

           // $cartNow=$this->clearCart($cust_id);
            $data=$result->fetch_assoc();

            
           $data["defaultAddress"]= json_decode($data["defaultAddress"], true);
           $data["defaultBilling"]= json_decode($data["defaultBilling"], true);

           $data["cartItems"]= json_decode($data["cartItems"], true);
           $data["cart"]= json_decode($data["cart"], true);

           $data["orderStatusName"]= $this->getOrderStatus($data["order_status"]);
           $data["paymentStatusName"]= $this->getPaymentStatus($data["payment_status"]);
          // $data["cartNow"]= $cartNow;
           
           
           
           

             return $data;
        }else{
            return null;

        }
    }

    private function getOrderStatus($orderStatus){

        if($orderStatus==1){
            return " Order Recieved by Sokoni";
        }
        if($orderStatus==2){
            return " Order Enroute to Customer";
        }
        if($orderStatus==3){
            return " Order Delivered to Customer";
        }
        return "Null";
    }
    private function getPaymentStatus($orderStatus){
        if($orderStatus==1){
            return "Full Payment Recieved by Sokoni";
        }
        if($orderStatus==0){
            return " Payment Pending";
        }
        if($orderStatus==2){
            return " Payment Failed";
        }
        
        return "Null";
    }

    public function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }


    public function createCartItems($cart_id,$product_id,$quantity){

        $date = new DateTime('now', new DateTimeZone('Africa/Nairobi'));
        $code=$date->format('H:m:s').$cart_id.$product_id;

        
        $stmt = $this->conn->prepare("INSERT INTO customer_cart_items
         (cart_id,code,product_id,quantity)
           
        VALUES(?,?,?,?)");

        $stmt->bind_param("isii", $cart_id,$code,$product_id,$quantity);

        $stmt->execute();
       
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        if($num_rows>0){
            return $this->getCartItemId($code);
      
        }else{
            return null;

        }
       
       
       
    }
    public function getCartItemId($code){
        $stmt = $this->conn->prepare("SELECT * FROM customer_cart_items WHERE code = ? LIMIT 1 ");
        $stmt->bind_param("s", $code);
       
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            return $result->fetch_assoc();
        }else{
            return null;

        }
    }
    public function createCartItemVariation($item_id,$variation){
      //  $variation_id,$variant_name,$selected_variantId,$selected_variantName

     // error_log($variation["variationName"]);
        $stmt = $this->conn->prepare("INSERT INTO cart_item_variations

        (item_id,variation_id,variation_title,selected_variation_id,selected_variation)
           
        VALUES(?,?,?,?,?)");

        $stmt->bind_param("iisis", $item_id,$variation["variationId"],$variation["variationName"],$variation["choosenVariationId"],$variation["choosenVariationName"]);

        $stmt->execute();
        
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return true;
      
        }else{
            return false;

        }
       
      
       
    }



    public function customerCart($cust_id){
       
        $stmt = $this->conn->prepare("SELECT * FROM customer_cart WHERE cust_id = ?  ");
         $stmt->bind_param("i", $cust_id);
        
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

    public function clearCart($cust_id){
        $cartId=$this->customerCart($cust_id)["id"];
        $this->deleteCartItemByCartId($cartId);

        return $this->customerCart($cust_id);
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
            
            return $this->attachProduct($results);
         }else{
             return NULL;
 
         }
         
       
         
    }

    public function attachProduct($cartItems){
        foreach($cartItems as $key => &$cartItem) {
            $cartItem["product"]=$this->product($cartItem["product_id"]);
           
           
        }
           return $cartItems;
    }

    public function getTotalPrice($cartItems){
        $totalPrice=0;
        foreach($cartItems as $key => &$cartItem) {
            $quantity=$cartItem["quantity"];
            $price=$cartItem["product"]["discounted_price"];
           
           //s echo $price."  *  ".$quantity;
            $value=($quantity*$price);
            $totalPrice=$totalPrice+$value;
           
        }

        return $totalPrice;
        
    }
    public function deleteCartItem($cartItemId){
        $stmt = $this->conn->prepare("DELETE FROM  customer_cart_items WHERE id = ?  ");

        $stmt->bind_param("i", $cartItemId);

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
    public function deleteCartItemByCartId($cartId){
        $stmt = $this->conn->prepare("DELETE FROM  customer_cart_items WHERE cart_id = ?  ");

        $stmt->bind_param("i", $cartId);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        $this->deleteCart($cartId);

        if($num_rows>0){
            return false;
        }else{
            return true;

        }

        
    }
    public function deleteCart($cartId){
        $stmt = $this->conn->prepare("DELETE FROM  customer_cart WHERE id = ?  ");

        $stmt->bind_param("i", $cartId);

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



    public function decreaseCartItem($cartItemId){
        //UPDATE my_table SET my_field = my_field - 1 WHERE `other` = '123

        $stmt = $this->conn->prepare("UPDATE  customer_cart_items SET quantity= quantity-1 WHERE id = ?  ");

        $stmt->bind_param("i", $cartItemId);

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
    public function increaseCartItem($cartItemId){
        $stmt = $this->conn->prepare("UPDATE  customer_cart_items SET quantity= quantity+1 WHERE id = ?  ");

        $stmt->bind_param("i", $cartItemId);

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




    public function product($product_id){
       
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?   LIMIT 1 ");
        $stmt->bind_param("i", $product_id);
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
    public function customerCartItemsQuantity($cart_id){
       
        $stmt = $this->conn->prepare("SELECT SUM(quantity) AS value_sum FROM customer_cart_items WHERE cart_id = ?  ");
         $stmt->bind_param("i", $cart_id);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
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
}











?>
