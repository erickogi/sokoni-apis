<?php

include '../classes/Aouth.php';
$db = new DbHandler;
$response = array();

if (validate() ) {
    
    
    
    if(!$db->isUserExists($_POST['email'],$_POST['mobile'])){
    $user=$db->register($_POST['firstName'],$_POST['lastName'],$_POST['email'],$_POST['mobile'],md5(sha1($_POST['password'])));
   
    if ($user != NULL) {

        $response["error"] = false;
        $response["status"] = 1;
        $response["profile"] = $db->login($_POST['email'],md5(sha1($_POST['password'])));
        $response["message"] = "Registered";

    } else {
        $response["error"] = false;
        $response["status"] = 0;
        $response["profile"] = NULL;
        $response["message"] = "Registration Failed";
    }

   

}else{
        $response["error"] = false;
        $response["status"] = 0;
        $response["profile"] = NULL;
        $response["message"] = "Email or Mobile Already exists";
}
echo json_encode($response);

}


function validate()
{
 $isOkay=true; 
 $response = array();
 $response['fields'] = array();


  
 if(!isset($_POST['firstName'])||$_POST['firstName'] == '' ){
  $isOkay=false;
  $temp['firstName'] = "Required|String";
}
 if(!isset($_POST['lastName'])||$_POST['lastName'] == '' ){
    $isOkay=false;
    $temp['lastName'] = "Required|String";
}
if(!isset($_POST['email'])||$_POST['email'] == '' ){
    $isOkay=false;
    $temp['email'] = "Required|String|";
}
if(!isset($_POST['mobile'])||$_POST['mobile'] == '' ){
    $isOkay=false;
    $temp['mobile'] = "Required|Int|10";
}
if(!isset($_POST['password'])||$_POST['password'] == '' ){
    $isOkay=false;
    $temp['password'] = "Required|String|8";
}
array_push($response['fields'], $temp);
   

 if($isOkay){
    return true;
 }else{
    $response["error"] = true;
    $response["status"] = 0;
    $response["profile"] = NULL;
    $response["message"] = "Some fields are missing";
    echo json_encode($response);

    return false;
 }



}

?>

