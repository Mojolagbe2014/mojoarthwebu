<?php
/* 
 * Class Patient describes individual patients
 * @author Jamiu Mojolagbe
 */
class Patient implements ContentManipulator{
    //class properties/data
    private $id;
    private $name;
    private $address;
    private $email;
    private $clientId;
    private $bedId;
    private $admissionId;
    private $noOfUsers;
    private $status = 1;
    
    private static $dbObj;
    private static $tableName;

    //class constructor
    public function Patient($dbObj, $tableName='patients') {
        self::$dbObj =  $dbObj;        self::$tableName = $tableName;
    }

    //Using Magic__set and __get
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
    
    /**  
     * Method that submits a patient into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO ".self::$tableName." (name, address, email, client_id, bed_id, admission_id, status) "
                ."VALUES ('{$this->name}','{$this->address}','{$this->email}','{$this->clientId}','{$this->bedId}','{$this->admissionId}','{$this->status}')";
        if($this->notEmpty($this->name,$this->admissionId)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, patient successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding patient! ".  mysqli_error(self::$dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a patient
     * @return JSON JSON encoded string/result
     */
    public function delete(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, patient successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting patient! ".  mysqli_error(self::$dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches patients from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g patient_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded string/result
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), "address" =>  utf8_encode($r['address']), 
                    "email" =>  utf8_encode($r['email']), 'clientId' =>  utf8_encode($r['client_id']),
                    "bedId" =>  utf8_encode($r['bed_id']), "admissionId" =>  utf8_encode($r['admission_id']), 
                    "status" =>  utf8_encode($r['status']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection)); }
        
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches patients from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array patient list
     */
    public function fetchRaw($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $result = self::$dbObj->fetchAssoc($sql);
        return $result;
    }
    
    /** Method that fetches patients from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g patient_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded patient details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); $fetPatientStat = 'icon-check-empty'; $fetPatientRolCol = 'btn-warning'; $fetPatientRolTit = "Activate Patient";
        if(count($data)>0){
            foreach($data as $r){ 
                $fetPatientStat = 'icon-check-empty'; $fetPatientRolCol = 'btn-warning'; $fetPatientRolTit = "Activate Patient";
                if($r['status'] == 1){  $fetPatientStat = 'icon-check'; $fetPatientRolCol = 'btn-success'; $fetPatientRolTit = "De-activate Patient";}
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-name="'.$r['name'].'"  data-status="'.$r['status'].'" />';
                $delButt = ' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" class="btn btn-danger btn-sm delete-patient" title="Delete"><i class="btn-icon-only icon-trash"> </i></button> ';
                $editButt = ' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-address="'.$r['address'].'" data-email="'.$r['email'].'" data-no-of-camera="'.$r['no_of_camera'].'" data-bed-per-cam="'.$r['bed_per_cam'].'" data-access-code="'.$r['access_code'].'" data-no-of-users="'.$r['no_of_users'].'" data-status="'.$r['status'].'" class="btn btn-info btn-sm edit-patient"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> <span id="JQDTcontentholder" data-content="" class="hidden"></span> </button>';
                $actButt = ' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-status="'.$r['status'].'"  class="btn '.$fetPatientRolCol.' btn-sm activate-patient"  title="'.$fetPatientRolTit.'"><i class="btn-icon-only '.$fetPatientStat.'"> </i></button> ';
                $butts = $delButt.$editButt.$actButt;
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($butts), utf8_encode($r['name']), utf8_encode($r['address']), utf8_encode($r['email']), utf8_encode($r['client_id']), utf8_encode($r['bed_id']), utf8_encode($r['admission_id']), utf8_encode($r['status']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that update details of a patient
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE ".self::$tableName." SET name = '{$this->name}', address = '{$this->address}', email = '{$this->email}', client_id = '{$this->clientId}', bed_id = '{$this->bedId}', admission_id = '{$this->admissionId}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, patient successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating patient! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }
    
    /** Method that update single field detail of a patient
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, patient successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating patient! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        echo json_encode($json);
    }
    
    /** Empty string checker  */
    public function notEmpty() {
        foreach (func_get_args() as $arg) {
            if (empty($arg)) { return false; } 
            else {continue; }
        }
        return true;
    }
    
    /** getSingle() fetches the name of a patient using the patient $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $column Requested column from the database
     * @param int $id Patient id of the patient whose name is to be fetched
     * @return string Name of the patient
     */
    public static function getSingle($dbObj, $column, $id) {
        $thisPatientName = '';
        $thisPatientNames = $dbObj->fetchNum("SELECT $column FROM ".self::$tableName." WHERE id = '{$id}' LIMIT 1");
        foreach ($thisPatientNames as $thisPatientNames) { $thisPatientName = $thisPatientNames[0]; }
        return $thisPatientName;
    }
}