<?php
/**
 * Description of Data
 *
 * @author Jamiu Mojolagbe
 */
class Data implements ContentManipulator{
    private $id;
    private $clientId;
    private $bedId;
    private $patientId;
    private $createdAt;
    private $status = 1;
    
    private static $dbObj;
    private static $tableName = 'datas';
    
    //Class constructor
    public function Data($dbObj, $tableName='datas') {
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
     * Method that adds a  data into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO ".self::$tableName." (client_id, bed_id, patient_id, status, created_at) "
                ."VALUES ('{$this->clientId}','{$this->bedId}','{$this->patientId}','{$this->status}',$this->createdAt)";
        if($this->notEmpty($this->clientId,$this->bedId)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, data successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding  data! ".  mysqli_error(self::$dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a  data
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  data successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting  data! ".  mysqli_error(self::$dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches datas from database for JQuery Data Table
     * @param string $column Column of the data to be fetched
     * @param string $condition Additional condition e.g  data_id > 9
     * @param string $sort column to be used as sort parameter
     * @return JSON JSON encoded data details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); $fetDataStat = 'icon-check-empty'; $fetDataRolCol = 'btn-warning'; $fetDataRolTit = "Activate Data";
        if(count($data)>0){
            foreach($data as $r){ 
                $fetDataStat = 'icon-check-empty'; $fetDataRolCol = 'btn-warning'; $fetDataRolTit = "Activate Data";
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-patient-id="'.$r['patient-id'].'" data-client-id="'.$r['client-id'].'" data-status="'.$r['status'].'"/>';
                if($r['status'] == 1){  $fetDataStat = 'icon-check'; $fetDataRolCol = 'btn-success'; $fetDataRolTit = "De-activate Data";}
                $delButt = '<div style="white-space:nowrap"><button data-id="'.$r['id'].'"  class="btn btn-danger btn-sm delete-data" title="Delete"><i class="btn-icon-only icon-trash"> </i></button>';
                $editButt = ' <button  data-id="'.$r['id'].'"  data-patient-id="'.$r['patient_id'].'" data-client-id="'.$r['client_id'].'" data-bed-id="'.$r['bed_id'].'" data-created-at="'.$r['created_at'].'" data-status="'.$r['status'].'" class="btn btn-info btn-sm edit-data"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> <span class="hidden" id="JQDTbedIdholder"></span> </button>';
                $actButt = ' <button data-id="'.$r['id'].'" data-client-id="'.$r['client_id'].'" data-status="'.$r['status'].'"  class="btn '.$fetDataRolCol.' btn-sm activate-data"  title="'.$fetDataRolTit.'"><i class="btn-icon-only '.$fetDataStat.'"> </i></button></div>';
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($delButt.$editButt.$actButt), utf8_encode($r['client_)id']), utf8_encode($r['patient_id']), utf8_encode($r['bed_id']), utf8_encode($r['created_at']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        self::$dbObj->close();
        //header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches datas from database
     * @param string $column Column clientId of the data to be fetched
     * @param string $condition Additional condition e.g  data_id > 9
     * @param string $sort column clientId to be used as sort parameter
     * @return JSON JSON encoded  data details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){//"Turns Right" at 11:30hour Fri 23, 2017
                $result[] = array("id" => $r['id'], "clientId" =>  utf8_encode($r['client_id']), "patientId" =>  utf8_encode($r['patient_id']), "patient" => utf8_encode(Patient::getSingle(self::$dbObj, 'name', $r['patient_id'])), "bedId" =>  utf8_encode($r['bed_id']), "bed" => utf8_encode(Bed::getLabel(self::$dbObj, $r['bed_id'])), "createdAt" =>  utf8_encode($r['created_at']), "status" =>  utf8_encode($r['status']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Empty result. ".mysqli_error(self::$dbObj->connection)); }
        self::$dbObj->close();
       if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }

    /** Method that fetches datas from database
     * @param string $column Column  of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column to be used as sort parameter
     * @return Array datas list
     */
    public function fetchRaw($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $result = self::$dbObj->fetchAssoc($sql);
        return $result;
    }
    
    /** Empty string checker  
     * @return Booloean True|False
     */
    public function notEmpty() {
        foreach (func_get_args() as $arg) {
            if (empty($arg)) { return false; } 
            else {continue; }
        }
        return true;
    }
    
    /** Method that update single field detail of a  data
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  data successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating  data! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a  data
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE ".self::$tableName." SET client_id = '{$this->clientId}', patient_id = '{$this->patientId}', bed_id = '{$this->bedId}' WHERE id = $this->id ";
        if($this->notEmpty($this->id, $this->bedId, $this->clientId)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  data successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating  data! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** getName() fetches the name of a data using the data $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id id of the  data whose clientId is to be fetched
     * @return string Name of the  data
     */
    public static function getName($dbObj, $id) {
        $thisDataName = '';
        $thisDataNames = $dbObj->fetchNum("SELECT name FROM ".self::$tableName." WHERE id = '{$id}' LIMIT 1");
        foreach ($thisDataNames as $thisDataNames) { $thisDataName = $thisDataNames[0]; }
        return $thisDataName;
    }
    
    /** getSingle() fetches the column of an data using the data $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $column Table's required column in the database
     * @param int $id Course id of the data whose clientId is to be fetched
     * @return string column value of the data
     */
    public static function getSingle($dbObj, $column, $id) {
        $thisReqColVal = '';
        $thisReqColVals = $dbObj->fetchNum("SELECT $column FROM ".self::$tableName." WHERE id = '{$id}' ");
        foreach ($thisReqColVals as $thisReqColVals) { $thisReqColVal = $thisReqColVals[0]; }
        return $thisReqColVal;
    }
}
