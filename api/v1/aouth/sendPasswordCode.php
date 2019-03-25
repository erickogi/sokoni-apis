<?php
include '../classes/AfricasTalkingGateway.php';
include '../classes/Aouth.php';
$db = new DbHandler;
$response = array();

$user=$db->getUser($_POST['phone']);

africasTalking($phone,"22423","Reset Code",$phone);

if($user!=null){
   
}


function africasTalking($to,$msg,$subject,$from){

// Specify your login credentials
    $username   = "sokoni";
    $apikey     = "7a714f47ad34124a8875ed75326313b136a01b53c9611ad50f5730a25668d35e";
// NOTE: If connecting to the sandbox, please use your sandbox login credentials
// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
      $recipients = "+254".$to;
// And of course we want our recipients to know what we really do
    //   $message    = "I'm a lumberjack and its ok, I sleep all night and I work all day";
// Create a new instance of our awesome gateway class
    $gateway    = new AfricasTalkingGateway($username, $apikey);
// NOTE: If connecting to the sandbox, please add the sandbox flag to the constructor:
    /*************************************************************************************
     ****SANDBOX****
    $gateway    = new AfricasTalkingGateway($username, $apiKey, "sandbox");
     **************************************************************************************/
// Any gateway error will be captured by our custom Exception class below,
// so wrap the call in a try-catch block


    $otp_prefix = ':';

    //Your message to send, Add URL encoding here.
    $message = "Request From ".$from."\nSubject ".$subject."\nMessage ".$msg;
    //$message="BLUE.jxc.Mirr.g.thika.  Tecno 626466 ime :********k** .Virus :hrank3.3 Battery :**2 SMS ***3 CALL**";

    try
    {
        // Thats it, hit send and we'll take care of the rest.
        $results = $gateway->sendMessage($recipients, $message);

        foreach($results as $result) {
            // status is either "Success" or "error message"
            //  echo " Number: " .$result->number;
            //  echo " Status: " .$result->status;
            //   echo " MessageId: " .$result->messageId;
               echo " Cost: "   .$result->cost."\n";
        }
    }
    catch ( AfricasTalkingGatewayException $e )
    {
        echo "Encountered an error while sending: ".$e->getMessage();
    }





}
?>