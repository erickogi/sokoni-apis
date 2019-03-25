<?php

include '../classes/Aouth.php';
include '../classes/Cart.php';
$db = new DbHandler;
$cdb = new CartDbHandler;
$response = array();


$token = getAuthorizationHeader();

if($token!=null){
    
    $cust_id=$db->authenticateToken($token);
    if($cust_id!=null){
        
        $data = file_get_contents('php://input');
        $json_data = json_decode($data , true);

       // error_log(json_encode($data));
           



             
    
       

       
       $order=$cdb->insertIntoOrders($cust_id,$json_data);
       $amount=$order["amount"];
       $orderId=$order["id"];
       $ftoken=$json_data["firebaseToken"];
       $paymentPhone=$json_data["phone"];

             $response["order"]=$rder;
             $response["error"] = false;
             $response["status"] = 1;
             $response["success"] = true;
             $response["message"] = $cdb->pay($amount,$paymentPhone,$orderId,$ftoken);
             
           
             
        
           
        
       



    }else{


        $response["error"] = true;
        $response["status"] = 0;
        $response["errorCode"] = 243;
        $response["profile"] = NULL;
        $response["message"] = "Token is invalid or expired";
       
     
    }
    echo json_encode($response);

}






function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { 
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }
    
    elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    // $headers = apache_request_headers();
    if (!empty($headers)) {
    //     $token=explode(" ",$headers)[1];
    //    // return explode(" ", $headers)[1];
    //    if($token!=""&&$token!=null){
           
        return $headers;
//       }
    }
    $response["error"] = true;
    $response["status"] = 0;
    $response["errorCode"] = 213;
    $response["profile"] = NULL;
    $response["message"] = "Token not found Headers".json_encode($headers);
    echo json_encode($response);

    return null;
}


?>

