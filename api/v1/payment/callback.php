<?php

include '../classes/Aouth.php';
include '../classes/Cart.php';
$db = new DbHandler;
$cdb = new CartDbHandler;
$response = array();


$firetoken =  $_GET["ftoken"];
$orderId =  $_GET["orderId"];
        $data = file_get_contents('php://input');
        $result = json_decode($data , true);


        error_log($orderId);

        $resultcode = $result["Body"]["stkCallback"]["ResultCode"];
        $resultdesc = $result["Body"]["stkCallback"]["ResultDesc"];
        
        
        $_token = array($firetoken);
            
      
        $fields = array(
                'registration_ids' =>  $_token,
                'data' => "",
            );
        
		if ($resultcode != 0) {
            $cdb->updateOrderNotPaid($orderId,"Payment Not Recieved ".$resultdesc);
		    
			$data= array();
            $data['data']['title'] = 'Sokoni Order Payment';
            $data['data']['message'] = $resultdesc;
            $data['data']['image'] = null;
            $data['data']['code'] = 0;
            $data['data']['status'] = 1;
            
            $data['notification']['sound'] = 'default';
            
             error_log($firetoken);
            
            $fields = array(
                'registration_ids' => $_token,
                'data' => $data,
            );
            
		}
		else{
            $cdb->updateOrderPaid($orderId,"Payment Recieved ".$mpesacost."  ".$resultdesc);
		    
		    
		     $mpesacode = $result["Body"]["stkCallback"]["CallbackMetadata"]["Item"];
             $mpesacost="";

	        for($i = 0; $i < count($mpesacode); ++$i) {
	            if($mpesacode[$i]['Name'] === 'Amount'){
	                $mpesacost = $mpesacode[$i]["Value"];
	            }
	            if($mpesacode[$i]['Name'] === 'MpesaReceiptNumber'){
	                $mpesareciept = $mpesacode[$i]["Value"];
	            }
	            if($mpesacode[$i]['Name'] === 'TransactionDate'){
	                $mpesadate = $mpesacode[$i]["Value"];
	            }
	            if($mpesacode[$i]['Name'] === 'PhoneNumber'){
	                $mpesanumber = $mpesacode[$i]["Value"];
	            }
	        }
	        
		    
		    
		    
		    $data= array();
	                $data['data']['title'] = 'Sokoni Order Payment';
	                $data['data']['message'] = "Payment Recieved ".$mpesacost."  ".$resultdesc;
	                $data['data']['image'] = null;
	                $data['data']['code'] = 0;
	                $data['data']['status'] = 0;
                    
	                $data['notification']['sound'] = 'default';
	                
	                $fields = array(
	                    'registration_ids' => $_token,
	                    'data' => $data,
                    );
                    
                   
		}






  
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers= array(
            
            'Authorization: key=AAAAm6DxvtM:APA91bGzb-e2CVTSOidkho_H8_ACJ-bkrOIgW23VNujydbO077KbpVr4_zqGN8vCBx4gF9I6Bn9xZALmraDZWHXXmSqrh_VvH_m3UWDWympAf8cygGk8xKSwyOrVv_hdagnYZ6vBBQTb',
            'Content-Type: application/json'
        );
        //Initializing curl to open a connection
        $ch = curl_init();
        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);
        //adding headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //adding the fields in json format
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        //finally executing the curl request
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            
             error_log($result);
            return 0;
            // die('Curl failed: ' . curl_error($ch));
        }else{
             error_log($result);
            return $result;
        }
        //Now close the connection
        //and return the result
    



?>

