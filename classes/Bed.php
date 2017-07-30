<?php
/**
 * Description of Bed
 *
 * @author Jamiu Mojolagbe
 */
class Bed implements ContentManipulator{
    private $id;
    private $clientId;
    private $label;
    private $cameraId;
    private $status = 1;

    private static $dbObj;
    private static $tableName = 'beds';
    
    //Class constructor
    public function __construct($dbObj, $tableName = 'beds') {
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
     * Method that adds a  bed into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO ".self::$tableName." (client_id, label, camera_id, status) "
                ."VALUES ('{$this->clientId}','{$this->label}','{$this->cameraId}','{$this->status}')";
        if($this->notEmpty($this->clientId,$this->label)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, bed successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding  bed! ".  mysqli_error(self::$dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a  bed
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  bed successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting  bed! ".  mysqli_error(self::$dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches beds from database for JQuery Data Table
     * @param string $column Column question of the data to be fetched
     * @param string $condition Additional condition e.g  bed_id > 9
     * @param string $sort column question to be used as sort parameter
     * @return JSON JSON encoded course bed details
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
                $editButt = ' <div style="white-space:nowrap"><button data-id="'.$r['id'].'" data-client-id="'.$r['client_id'].'" data-camera-id="'.$r['camera_id'].'"  data-label="'.$r['label'].'"  data-status="'.$r['status'].'" class="btn btn-info btn-sm edit-bed"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> <span class="hidden"></span> </button>';
                $delButt = ' <button data-status="'.$r['status'].'" data-id="'.$r['id'].'" class="btn btn-danger btn-sm delete-bed" title="Delete"><i class="btn-icon-only icon-trash"> </i><span class="hidden"></span></button>';
                $actButt = ' <button data-id="'.$r['id'].'" data-label="'.$r['label'].'" data-status="'.$r['status'].'"  class="btn '.$fetDataRolCol.' btn-sm activate-bed"  title="'.$fetDataRolTit.'"><i class="btn-icon-only '.$fetDataStat.'"> </i></button></div>';
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($editButt.$delButt.$actButt), utf8_encode($r['client_id']), utf8_encode($r['label']), utf8_encode($r['camera_id']));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Empty result. ".mysqli_error(self::$dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches beds from database
     * @param string $column Column question of the data to be fetched
     * @param string $condition Additional condition e.g  bed_id > 9
     * @param string $sort column question to be used as sort parameter
     * @return JSON JSON encoded course bed details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "clientId" =>  utf8_encode($r['client_id']), "label" =>  utf8_encode($r['label']), "cameraId" =>  utf8_encode($r['camera_id']), "status" =>  utf8_encode($r['status']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Empty result. ".mysqli_error(self::$dbObj->connection)); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches beds from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array FAQ list
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
    
    /** Method that update single field detail of a  bed
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  bed successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating  bed! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a  bed
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE ".self::$tableName." SET client_id = '{$this->clientId}', label = '{$this->label}', camera_id = '{$this->cameraId}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  bed successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating  bed! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** getLabel() fetches the question of a bed using the bed $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id Id of the  bed whose question is to be fetched
     * @return string Label of the  bed
     */
    public static function getLabel($dbObj, $id) {
        $thisBedName = '';
        $thisBedNames = $dbObj->fetchNum("SELECT label FROM ".self::$tableName." WHERE id = '{$id}' LIMIT 1");
        foreach ($thisBedNames as $thisBedNames) { $thisBedName = $thisBedNames[0]; }
        return $thisBedName;
    }
}
