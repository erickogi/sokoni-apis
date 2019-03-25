<?php


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
        require_once dirname(__FILE__) . '/DbConnect.php';
       // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
    public function login($email,$password){
       
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE customers.email = ?  AND customers.password = ?");
        $stmt->bind_param("ss", $email,$password);
       
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();


        if($num_rows>1){
            return "dsdssd";
        }else{
            return "sds";

        }
        
      
        
    }
    public function isUserRegisterd($email,$password)
    {  
       // echo $pass;
        $stmt = $this->conn->prepare("SELECT id FROM customers WHERE customers.email = ?  AND customers.password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function createBeneficiary($BeneficiaryID, $FirstName, $LastName, $DateOfBirth, $Gender, $PhoneNumber,
                                      $PhysicalAddress, $Latitude, $Longitude, $RecentSchool, $LastClass,
                                      $PhotoUrl, $FatherName, $FatherID, $FatherTelephone, $FatherAddress,
                                      $MotherName, $MotherID, $MotherTelephone, $MotherAddress, $GuardianName,
                                      $GuardianID, $GuardianTelephone, $GuardianAddress, $GuardianRelationship,$CreatedBy)
    {
        $response = array();

        if (!$this->isUserExists($BeneficiaryID)) {

            $stmt = $this->conn->prepare("INSERT INTO beneficiary

          (BeneficiaryID, FirstName, LastName, DateOfBirth, Gender, PhoneNumber,PhysicalAddress,Latitude,Longitude,
          RecentSchool,LastClass,PhotoUrl,FatherName,FatherID,FatherTelephone,FatherAddress,MotherName,MotherID,MotherTelephone,
          MotherAddress,GuardianName,GuardianID,GuardianTelephone,GuardianAddress,GuardianRelationship,CreatedBy)
           
          VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");





            $stmt->bind_param("ssssssssssssssssssssssssss", $BeneficiaryID, $FirstName, $LastName, $DateOfBirth, $Gender, $PhoneNumber,
                $PhysicalAddress,$Latitude,$Longitude,$RecentSchool,$LastClass,$PhotoUrl,$FatherName,$FatherID,$FatherTelephone,$FatherAddress,
                $MotherName,$MotherID,$MotherTelephone,$MotherAddress,$GuardianName,$GuardianID,$GuardianTelephone,$GuardianAddress,$GuardianRelationship,$CreatedBy);

            $result = $stmt->execute();


            $new_user_id = $stmt->insert_id;

            $stmt->close();

            if ($result) {
                echo "" . $result;
                // User successfully inserted

                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                echo "" . $result;
                // User successfully inserted

                // Failed to create user
                return USER_CREATE_FAILED;
            }





        } else {

            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }


    /**
     * @param $BeneficiaryID
     * @return bool
     */
 

    public function createSibbling($name, $gender, $age, $BeneficiaryID,$CreatedBy)
    {

        $stmt = $this->conn->prepare("INSERT INTO sibblings

        (sibblings.BeneficiaryID, sibblings.FullName, sibblings.Gender, sibblings.DateOfBirth,  sibblings.CreatedBy)
           
        VALUES(?,?,?,?,?)");

        $stmt->bind_param("sssss", $BeneficiaryID,$name,$gender,$age,$CreatedBy);

        $stmt->execute();


    }

    public function getSibblingss($BeneficiaryID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM sibblings  WHERE BeneficiaryID = ? ");
        $stmt->bind_param("s", $BeneficiaryID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;

        
    }
    public function getBeneficiary()
    {
        $stmt = $this->conn->prepare("SELECT * FROM beneficiary ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;


    }
    public function getBeneficiarySp($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM beneficiary WHERE BeneficiaryID =?");
        $stmt->bind_param("s", $id);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;


    }

    public function getInterventions($BeneficiaryID, $string)
    {
        $stmt = $this->conn->prepare("SELECT * FROM interventions  WHERE BeneficiaryID = ? AND status = ?");
        $stmt->bind_param("ss", $BeneficiaryID,$string);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function csiEntry($BeneficiaryID, $CSIID, $CSISCORE,$CreatedBy)
    {


        $stmt = $this->conn->prepare("INSERT INTO csientry

        (csientry.TrackID, csientry.CSIID, csientry.Score, csientry.CreatedBy)
           
        VALUES(?,?,?,?)");

        $stmt->bind_param("ssss", $BeneficiaryID,$CSIID,$CSISCORE,$CreatedBy);

        $result = $stmt->execute();


        //$new_user_id = $stmt->insert_id;

        //$stmt->close();

//        if ($result) {
//
//            return USER_CREATED_SUCCESSFULLY;
//        } else {
//
//            return USER_CREATE_FAILED;
//        }

    }

    public function InterventionEntry($BeneficiaryID,
                                      $InterventionID,
                                      $type, 
                                      $referedTo, 
                                      $messageTo, 
                                      $messageFrom,
                                      $status,
                                      $SocialWorker)
    {

        $stmt = $this->conn->prepare("INSERT INTO interventions

        (interventions.BeneficiaryID, 
        interventions.InterventionID,
        interventions.type,
        interventions.referedTo,
        interventions.messageTo,
        interventions.messageFrom,
        interventions.status,
        interventions.CreatedBy)
           
        VALUES(?,?,?,?,?,?,?,?)");

        $stmt->bind_param("ssssssss", $BeneficiaryID,$InterventionID,$type,$referedTo,$messageTo,$messageFrom,$status,$SocialWorker);

        $stmt->execute();


        $new_user_id = $stmt->insert_id;

        return $new_user_id;

    }
//$res=$db->KpiEntry($item3['INTERVENTIONID'],$item3['KPIID'],$item3['KPISCRORE'],$item3['KPIPPI']);
    public function KpiEntry($TrackID, $kpiID,$kpiScore,$kpiData)
    {
        $stmt = $this->conn->prepare("INSERT INTO kpientry

        (TrackID, kpiID,kpiScore,kpiData)
           
        VALUES(?,?,?,?)");

        $stmt->bind_param("ssss", $TrackID,$kpiID,$kpiScore,$kpiData);

        $result = $stmt->execute();


        $new_user_id = $stmt->insert_id;

        $stmt->close();

        if ($result) {

            return USER_CREATED_SUCCESSFULLY;
        } else {

            return USER_CREATE_FAILED;
        }

    }

    public function InterventionExit($BeneficiaryID,$InterventionID)
    {


        $stmt = $this->conn->prepare("UPDATE interventions SET status = ?
       WHERE BeneficiaryID = ? AND  InterventionID =? AND interventions.status= ?");
        $stmt->bind_param("sss",'inactive', $BeneficiaryID, $InterventionID,"active");

        $stmt->execute();

        $result = $stmt->get_result();
        return $result;



    }

    public function getInterventionsForSp($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM interventions  WHERE referedTo = ? ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
        
        
    }

    public function getInterventionsSp($bID, $sid,$type)
    {

        $stmt = $this->conn->prepare("SELECT interventions.BeneficiaryID FROM interventions
       WHERE BeneficiaryID = ? AND referedTo = ? AND  interventions.status=?");
        $stmt->bind_param("sss", $bID,$sid,$type);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
        
    }

    public function getInterventionsForSpByID($BeneficiaryID, $string)
    {
        $stmt = $this->conn->prepare("SELECT * FROM interventions  WHERE BeneficiaryID = ? AND  referedTo = ? ");
        $stmt->bind_param("ss", $BeneficiaryID,$string);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result; 
        
    }
        public function deleteSibbling($BeneficiaryIDD)
    {

        $stmt = $this->conn->prepare("DELETE  FROM sibblings WHERE BeneficiaryID = ?  ");
        $stmt->bind_param("s", $BeneficiaryIDD);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function deleteBeneficiary($BeneficiaryIDD)
    {
        $stmt = $this->conn->prepare("DELETE  FROM beneficiary WHERE BeneficiaryID = ?  ");
        $stmt->bind_param("s", $BeneficiaryIDD);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;

    }


}
?>
