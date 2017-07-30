<?php
/**
 * Description of Camera
 *
 * @author Jamiu Mojolagbe
 */
class Camera implements ContentManipulator{
    private $id;
    private $label;
    private $clientId;
    private $status;
    
    private  static $dbObj;
    private static $tableName = 'cameras';
    
    
    //Class constructor
    public function Camera($dbObj, $tableName = 'cameras') {
        self::$dbObj = $dbObj;        self::$tableName = $tableName;
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
     * Method that adds a camera into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO ".self::$tableName." (label, client_id, status) "
                ."VALUES ('{$this->label}','{$this->clientId}','{$this->status}')";
        if($this->notEmpty($this->label,$this->clientId)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, camera successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding camera! ".  mysqli_error(self::$dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a camera
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, camera successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting camera! ".  mysqli_error(self::$dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches camera from database for JQuery Data Table
     * @param string $column Column label of the data to be fetched
     * @param string $condition Additional condition e.g camera_id > 9
     * @param string $sort column label to be used as sort parameter
     * @return JSON JSON encoded camera details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); $fetDataStat = 'icon-check-empty'; $fetDataRolCol = 'btn-warning'; $fetDataRolTit = "Activate";
        if(count($data)>0){
            foreach($data as $r){ 
                $fetDataStat = 'icon-check-empty'; $fetDataRolCol = 'btn-warning'; $fetDataRolTit = "Activate";
                if($r['status'] == 1){  $fetDataStat = 'icon-check'; $fetDataRolCol = 'btn-success'; $fetDataRolTit = "De-activate";}
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-status="'.$r['status'].'" />';
                $editButt = ' <div style="white-space:nowrap"><button data-id="'.$r['id'].'"  data-client-id="'.$r['client_id'].'"  data-label="'.$r['label'].'"  data-status="'.$r['status'].'" class="btn btn-info btn-sm edit-camera"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> <span class="hidden"></span> </button>';
                $delButt = ' <button data-status="'.$r['status'].'" data-id="'.$r['id'].'" class="btn btn-danger btn-sm delete-camera" title="Delete"><i class="btn-icon-only icon-trash"> </i><span class="hidden"></span></button>';
                $actButt = ' <button data-id="'.$r['id'].'" data-label="'.$r['label'].'" data-status="'.$r['status'].'"  class="btn '.$fetDataRolCol.' btn-sm activate-camera"  title="'.$fetDataRolTit.'"><i class="btn-icon-only '.$fetDataStat.'"> </i></button></div>';
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($editButt.$delButt.$actButt), utf8_encode($r['label']), utf8_encode($r['client_id']));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches camera from database
     * @param string $column Column label of the data to be fetched
     * @param string $condition Additional condition e.g camera_id > 9
     * @param string $sort column label to be used as sort parameter
     * @return JSON JSON encoded camera details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "label" =>  utf8_encode($r['label']), "clientId" =>  utf8_encode($r['client_id']), "status" =>  utf8_encode($r['status']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection)); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches camera from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array camera list
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
    
    /** Method that update single field detail of a camera
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, camera successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating camera! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a camera
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE ".self::$tableName." SET label = '{$this->label}', client_id = '{$this->clientId}', status = '{$this->status}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, camera successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating camera! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** getLabe() fetches the label of a camera using the camera $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id Camera id of the camera whose label is to be fetched
     * @return string Name of the camera
     */
    public static function getLabel($dbObj, $id) {
        $thisCameraCont = '';
        $thisCameraConts = $dbObj->fetchNum("SELECT label FROM ".self::$tableName." WHERE id = '{$id}' LIMIT 1");
        foreach ($thisCameraConts as $thisCameraConts) { $thisCameraCont = $thisCameraConts[0]; }
        return $thisCameraCont;
    }
    
    /**
     * Method that returns count/total number of a particular course
     * @param Object $dbObj Database connectivity object
     * @return int Number of cameras
     */
    public static function getRawCount($dbObj, $dbPrefix){
        $tableName = $dbPrefix.self::$tableName;
        $sql = "SELECT * FROM $tableName ";
        $count = "";
        $result = $dbObj->query($sql);
        $totalData = mysqli_num_rows($result);
        if($result !== false){ $count = $totalData; }
        return $count;
    }
    
    /** getSingle() fetches the title of an camera using the course $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $column Table's required column in the datatbase
     * @param int $id camera id of the course whose name is to be fetched
     * @return string requested value
     */
    public static function getSingle($dbObj, $column, $id) {
        $thisReqContent = '';
        $thisReqContents = $dbObj->fetchNum("SELECT $column FROM ".self::$tableName." WHERE id = '{$id}' ");
        foreach ($thisReqContents as $thisReqContents) { $thisReqContent = $thisReqContents[0]; }
        return $thisReqContent;
    }
}
