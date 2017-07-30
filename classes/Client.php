<?php
/* 
 * Class Client describes individual clients
 * @author Jamiu Mojolagbe
 */
class Client implements ContentManipulator{
    //class properties/data
    private $id;
    private $name;
    private $address;
    private $email;
    private $noOfCamera;
    private $bedPerCam;
    private $accessCode;
    private $noOfUsers;
    private $status = 1;
    
    private static $dbObj;
    private static $tableName;

    //class constructor
    public function Client($dbObj, $tableName='clients') {
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
     * Method that submits a client into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO ".self::$tableName." (name, address, email, no_of_camera, bed_per_cam, access_code, no_of_users, status) "
                ."VALUES ('{$this->name}','{$this->address}','{$this->email}','{$this->noOfCamera}','{$this->bedPerCam}','{$this->accessCode}','{$this->noOfUsers}','{$this->status}')";
        if($this->notEmpty($this->name,$this->accessCode)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, client successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding client! ".  mysqli_error(self::$dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a client
     * @return JSON JSON encoded string/result
     */
    public function delete(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, client successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting client! ".  mysqli_error(self::$dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches clients from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g client_id > 9
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
                    "email" =>  utf8_encode($r['email']), 'noOfCamera' =>  utf8_encode($r['no_of_camera']),
                    "bedPerCam" =>  utf8_encode($r['bed_per_cam']), "accessCode" =>  utf8_encode($r['access_code']), 
                    "noOfUsers" =>  utf8_encode($r['no_of_users']), "status" =>  utf8_encode($r['status']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection)); }
        
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches clients from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array client list
     */
    public function fetchRaw($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $result = self::$dbObj->fetchAssoc($sql);
        return $result;
    }
    
    /** Method that fetches clients from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g client_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded client details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); $fetClientStat = 'icon-check-empty'; $fetClientRolCol = 'btn-warning'; $fetClientRolTit = "Activate Client";
        if(count($data)>0){
            foreach($data as $r){ 
                $fetClientStat = 'icon-check-empty'; $fetClientRolCol = 'btn-warning'; $fetClientRolTit = "Activate Client";
                if($r['status'] == 1){  $fetClientStat = 'icon-check'; $fetClientRolCol = 'btn-success'; $fetClientRolTit = "De-activate Client";}
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-name="'.$r['name'].'"  data-status="'.$r['status'].'" />';
                $delButt = ' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" class="btn btn-danger btn-sm delete-client" title="Delete"><i class="btn-icon-only icon-trash"> </i></button> ';
                $editButt = ' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-address="'.$r['address'].'" data-email="'.$r['email'].'" data-no-of-camera="'.$r['no_of_camera'].'" data-bed-per-cam="'.$r['bed_per_cam'].'" data-access-code="'.$r['access_code'].'" data-no-of-users="'.$r['no_of_users'].'" data-status="'.$r['status'].'" class="btn btn-info btn-sm edit-client"  title="Edit"><i class="btn-icon-only icon-pencil"> </i> <span id="JQDTcontentholder" data-content="" class="hidden"></span> </button>';
                $actButt = ' <button data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-status="'.$r['status'].'"  class="btn '.$fetClientRolCol.' btn-sm activate-client"  title="'.$fetClientRolTit.'"><i class="btn-icon-only '.$fetClientStat.'"> </i></button> ';
                $butts = $delButt.$editButt.$actButt;
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($butts), utf8_encode($r['name']), utf8_encode($r['address']), utf8_encode($r['email']), utf8_encode($r['no_of_camera']), utf8_encode($r['bed_per_cam']), utf8_encode($r['access_code']), utf8_encode($r['no_of_users']), utf8_encode($r['status']));//
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that update details of a client
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE ".self::$tableName." SET name = '{$this->name}', address = '{$this->address}', email = '{$this->email}', no_of_camera = '{$this->noOfCamera}', bed_per_cam = '{$this->bedPerCam}', access_code = '{$this->accessCode}', no_of_users = '{$this->noOfUsers}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, client successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating client! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }
    
    /** Method that update single field detail of a client
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, client successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating client! ".  mysqli_error($dbObj->connection));   }
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
    
    /** getSingle() fetches the name of a client using the client $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $column Requested column from the database
     * @param int $id Client id of the client whose name is to be fetched
     * @return string Name of the client
     */
    public static function getSingle($dbObj, $column, $id) {
        $thisClientName = '';
        $thisClientNames = $dbObj->fetchNum("SELECT $column FROM ".self::$tableName." WHERE id = '{$id}' LIMIT 1");
        foreach ($thisClientNames as $thisClientNames) { $thisClientName = $thisClientNames[0]; }
        return $thisClientName;
    }
}