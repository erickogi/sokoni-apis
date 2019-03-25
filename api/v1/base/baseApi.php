<?php

include '../classes/Aouth.php';
include '../classes/Products.php';
$db = new DbHandler;
$pdb = new ProductsDbHandler;
$response = array();


$token = getAuthorizationHeader();

if($token!=null){

    $cust_id=$db->authenticateToken($token);
    if($cust_id!=null){

        
            $products= $pdb->trendingProducts();
        
            $response["error"] = false;
            $response["status"] = 1;
            $response["imagesBaseUrl"] = "https://www.calista.co.ke/sokoni/images/";
            $cart= $db->customerCart($cust_id);
           // echo json_encode($cart);
           
           if($cart!=null&&isset($cart['id'])){
            $cart['items_quantity'] = $db->customerCartItemsQuantity($cart['id']);
          
           }
            $response["cart"] = $cart;
            

            $response["sliders"] = $pdb->promotionalSliders();
            $response["topCategories"] = $pdb->topCategories();
            $response["trendingProducts"] = $pdb->getRatingAndFavourite($products,$cust_id);
            //$response["trendiProducts"] = $pdb->getRatingAndFavourite($products,$cust_id);
            $response["tags"] = $pdb->productsTags();
            
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

