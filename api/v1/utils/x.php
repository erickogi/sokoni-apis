<?php 
    class X {
        private $debugValue;
        function __construct ($debug = NULL){
            $this->debugValue = $debug;
        }
        public function validate($passedModel, $requiredModel){
            $isOkay=true; 
            $response = array();
            $response['fields'] = array();
            $i = 0;
            if($this->debugValue){
                echo "passedModel";
                print_r($passedModel);
                echo "<hr/>";
                echo "requiredModel";
                print_r($requiredModel);
            }
            foreach($requiredModel as $key=>$value){
                $x = 0;
                $foundIndex = -1;
                $tempValue = "";
                foreach($passedModel as $xKey=>$xValue){
                    if($key == $xKey){ 
                        $foundIndex = $x;
                        $tempValue = $xValue;
                       // break;
                    }
                    $x ++;
                }
               
                if($foundIndex == -1){
                    $isOkay=false;
                    $temp[$key] = $value;
                    array_push($response['fields'], $temp);
                }else{
                    $tempRules = explode("|", $value);
                   if( $this->examineValue($tempValue,$tempRules)){
                     $isOkay=true;
                   }else{
                    $isOkay=false;
                    $temp[$key] = $value;
                    array_push($response['fields'], $temp);
                    
                   }
                }
                $i ++;
            }           
            if($isOkay){
                return true;
            }else{

                $response["error"] = true;
                $response["status"] = 0;
                $response["profile"] = NULL;
                $response["message"] = "Some fields are missing";
                return $response;
            }
        }

        private function examineValue ($value, $rules){
            $i = 0;
            foreach($rules as $rule){
                switch($i){
                    case 0:
                        if($rule == 'Required' && $value == '' ){
                            //echo $value."   Required";
                             return false; 
                            }
                    break;
                    case 1:
                        if($rule =='Int'){
                            if(!is_numeric ($value) ){
                                //echo $value."   Int";
                                return false;
                                
                            }
                        }
                        if($rule =='String'){
                            if(gettype ($value)!='string'||is_numeric ($value)  ){
                                //echo $value."  String";
                                return false;
                            }
                        }
                        if($rule =='Boolean'){
                            if(gettype ($value)!='boolean' ){
                                //echo $value;
                                return false;
                            }
                        }
                        if($rule =='Double'){
                            if(!is_float($value)){
                                //echo $value;
                                return false;
                            }
                        }
                    break;
                    case 2:
                        if(strlen($value)<$rule){
                            //echo $value;
                            return false;
                        } 
                    break;
                }
                $i ++;
            }
            
            // if($tempRules[0] == 'Required'){
            //     if($tempValue == '' || $tempRules == null){
            //         $isOkay=false;
            //         $temp[$key] = $value;
            //         array_push($response['fields'], $temp);
            //     }
            // }

            return true;
        }
    }
?>