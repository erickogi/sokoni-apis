<?php

include '../classes/Aouth.php';

$db = new DbHandler;

$response = array();


$token = getAuthorizationHeader();

if($token!=null){
   
   
    $cust_id=$db->authenticateToken($token);
    if($cust_id!=null){

        
       
            $response["error"] = false;
            $response["status"] = 1;
//            echo $cust_id;
            $response["success"] = $db->addAddress($cust_id,$_POST["name"],$_POST["phone"],$_POST["address"],$_POST["lat"],$_POST["lon"],$_POST["desc"],$_POST["countryId"],$_POST["countyId"],$_POST["townId"],$_POST["street"],$_POST["isDefault"]);
            
           $addresses= $db->customerAddress($cust_id);
            
           $response["addresses"] = $addresses;
          
           $response["message"] = "Successfull";
           
        
           
        
       



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
