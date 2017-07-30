<?php
/**
 * Description of General Setting
 *
 * @author Kaiste
 */
class Setting implements ContentManipulator{
    private $name;
    private $value;
    private $dbObj;
    private $tableName;
    
    
    //Class constructor
    public function Setting($dbObj, $tableName='setting') {
        $this->dbObj = $dbObj;        $this->tableName = $tableName;
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
     * Method that adds a  setting into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO $this->tableName (name, value) "
                ."VALUES ('{$this->name}','{$this->value}')";
        if($this->notEmpty($this->value,$this->name)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, setting successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding  setting! ".  mysqli_error($this->dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a  setting
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM $this->tableName WHERE name = '$this->name' ";
        if($this->notEmpty($this->name)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  setting successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting  setting! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches settings from database for JQuery Data Table
     * @param string $column Column value of the data to be fetched
     * @param string $condition Additional condition e.g  setting_name > 9
     * @param string $sort column value to be used as sort parameter
     * @return JSON JSON encoded course setting details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="name"){
        $sql = "SELECT $column FROM $this->tableName ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM $this->tableName WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-name="'.$r['name'].'" />';
                $result[] = array(utf8_encode($multiActionBox), $r['name'], utf8_encode(StringManipulator::trimStringToFullWord(90, stripcslashes(strip_tags($r['value'])))), utf8_encode(' <button data-name="'.$r['name'].'" class="btn btn-info btn-sm edit-setting"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> <span id="JQDTvalueholder" class="hidden">'.$r['value'].'</span> </button> <button data-name="'.$r['name'].'" class="btn btn-danger btn-sm delete-setting" title="Delete"><i class="btn-icon-only icon-trash"> </i> <span name="JQDTvalueholder" class="hidden">'.$r['value'].'</span></button>'));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches settings from database
     * @param string $column Column value of the data to be fetched
     * @param string $condition Additional condition e.g  setting_name > 9
     * @param string $sort column value to be used as sort parameter
     * @return JSON JSON encoded course setting details
     */
    public function fetch($column="*", $condition="", $sort="name"){
        $sql = "SELECT $column FROM $this->tableName ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM $this->tableName WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("name" => $r['name'], "value" =>  utf8_encode($r['value']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Empty result. ".mysqli_error($this->dbObj->connection)); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches settings from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_name > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array FAQ list
     */
    public function fetchRaw($column="*", $condition="", $sort="name"){
        $sql = "SELECT $column FROM $this->tableName ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM $this->tableName WHERE $condition ORDER BY $sort";}
        $result = $this->dbObj->fetchAssoc($sql);
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
    
    /** Method that update single field detail of a  setting
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $name Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $name){
        $sql = "UPDATE setting SET $field = '{$value}' WHERE name = $name ";
        if(!empty($name)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  setting successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating  setting! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a  setting
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE setting SET value = '{$this->value}' WHERE name = '$this->name' ";
        if(!empty($this->name)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done,  setting successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating  setting! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** getValue() fetches the value of a setting using the setting $name
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $name Category name of the  setting whose value is to be fetched
     * @return string Name of the  setting
     */
    public static function getValue($dbObj, $name) {
        $thisSettingValue = '';
        $thisSettingValues = $dbObj->fetchNum("SELECT value FROM setting WHERE name = '{$name}' LIMIT 1");
        foreach ($thisSettingValues as $thisSettingValues) { $thisSettingValue = $thisSettingValues[0]; }
        return $thisSettingValue;
    }
}
