<?php
/**
 * Description of User
 *
 * @author Jamiu Mojolagbe
 */
class User implements ContentManipulator{
    private $id;
    private $clientId;
    private $name;
    private $email;
    private $address;
    private $picture;
    private $username;
    private $password;
    private $canAccess;
    private $status = 0;

    private static $dbObj;
    public static $tableName = 'users';

    //Class constructor
    public function User($dbObj=null, $tableName='users') {
        self::$dbObj = $dbObj;        
        self::$tableName = $tableName;
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
     * Method that adds a user into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO ".self::$tableName." (client_id, name, email, address, picture, username, password, can_access, status) "
                ."VALUES ('{$this->clientId}','{$this->name}','{$this->email}','{$this->address}','{$this->picture}','{$this->username}','{$this->password}','{$this->canAccess}','{$this->status}')";
        if($this->notEmpty($this->name,$this->clientId,$this->username)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, user successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding user! ".  mysqli_error(self::$dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /**  
     * Method that adds a user into the database
     * @return string Success|Error
     */
    public function addRaw(){
        $sql = "INSERT INTO ".self::$tableName." (client_id, name, email, address, picture, username, password, can_access, status) "
                ."VALUES ('{$this->clientId}','{$this->name}','{$this->email}','{$this->address}','{$this->picture}','{$this->username}','{$this->password}','{$this->canAccess}','{$this->status}')";
        if($this->notEmpty($this->name,$this->clientId,$this->username)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ return 'success'; }
            else{ return 'error';    }
        }
        else{return 'error'; }
    }

    /** 
     * Method for deleting a user
     * @return string Success|Error
     */
    public function deleteRaw(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ return 'success'; }
            else{ return 'error';    }
        }
        else{ return 'error'; }
    }

    /** 
     * Method for deleting a user
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM ".self::$tableName." WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, user successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting user! ".  mysqli_error(self::$dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches users from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded user details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $actionButtons = '<div style="white-space:nowrap"> <button data-id="'.$r['id'].'" data-email="'.$r['email'].'" data-address="'.$r['address'].'" data-name="'.$r['name'].'" data-no-of-camera="'.$r['no_of_camera'].'" class="btn btn-danger btn-sm delete-user" title="Delete"><i class="btn-icon-only icon-trash"> </i></button>  </div>';//'<button data-email="'.$r['email'].'" data-id="'.$r['id'].'" data-name="'.$r['name'].'" class="btn btn-primary btn-sm message-user"  title="Send Message"><i class="btn-icon-only icon-envelope"> </i></button> ';
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'"  data-name="'.$r['name'].'" data-email="'.$r['email'].'" />';
                $result[] = array(utf8_encode($multiActionBox), utf8_encode($actionButtons), $r['id'], utf8_encode($r['name']), utf8_encode($r['client_id']), utf8_encode($r['email']), utf8_encode($r['address']), utf8_encode($r['picture']), utf8_encode($r['username']), utf8_encode($r['can_access']));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches users from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @param boolean $endDBcon End database connection
     * @return JSON JSON encoded user details
     */
    public function fetch($column="*", $condition="", $sort="id", $endDBcon=true){
        $sql = "SELECT $column FROM ".self::$tableName." ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM ".self::$tableName." WHERE $condition ORDER BY $sort";}
        $data = self::$dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "email" =>  utf8_encode($r['email']), 'name' =>  utf8_encode($r['name']), 
                    'clientId' =>  utf8_encode($r['client_id']), 'address' =>  utf8_encode($r['address']), 'picture' =>  utf8_encode($r['picture']), 
                    'username' =>  utf8_encode($r['username']), 'status' =>  utf8_encode($r['status']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error(self::$dbObj->connection)); }
        if($endDBcon) self::$dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches user from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array User list
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
    
    /** Method that update single field detail of a user
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id or email of the user to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $det = intval($id) ? "id" : "email";
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE $det = '$id' ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, user successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating user! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that update single field detail of a user
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id or email of the user to be updated
     * @return string success|error
     */
    public static function updateSingleRaw($dbObj, $field, $value, $id){
        $det = intval($id) ? "id" : "email";
        $sql = "UPDATE ".self::$tableName." SET $field = '{$value}' WHERE $det = '$id' ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ return 'success'; }
            else{ return 'error';    }
        }
        else{return 'error'; }
    }

    /** Method that update details of a user
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE ".self::$tableName." SET name = '{$this->name}', address = '{$this->address}', username = '{$this->username}', picture = '{$this->picture}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, user successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating user! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        self::$dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Method that update details of a user
     * @return string Success|Error
     */
    public function updateRaw() {
        $sql = "UPDATE ".self::$tableName." SET name = '{$this->name}', email = '{$this->email}', company = '{$this->company}' WHERE email = '{$this->id}' ";
        if(!empty($this->email)){
            $result = self::$dbObj->query($sql);
            if($result !== false){ return 'success'; }
            else{ return 'error';    }
        }
        else{return 'error'; }
    }
    
    /** getSingle() fetches a single column of an user using $email or $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $column Table's required column in the datatbase
     * @param string $email User email or ID of the user whose name is to be fetched
     * @return string Name of the user
     */
    public static function getSingle($dbObj, $column, $email) {
        $field = intval($email) ? "id" : "email";
        $thisReqVal = '';
        $thisReqVals = $dbObj->fetchNum("SELECT $column FROM ".self::$tableName." WHERE $field = '{$email}' ");
        foreach ($thisReqVals as $thisReqVals) { $thisReqVal = $thisReqVals[0]; }
        return $thisReqVal;
    }
    
    /**
     * Sign in handler 
     */
    public function signIn(){
        $sql = "SELECT * FROM ".self::$tableName." WHERE email = '".$this->email."' AND password = '".md5($this->password)."' AND status=1 LIMIT 1 ";
        $data = self::$dbObj->fetchAssoc($sql); $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "clientId" =>  utf8_encode($r['client_id']), 'name' =>  utf8_encode($r['name']), 'email' =>  utf8_encode($r['email']), 'address' =>  utf8_encode($r['address']), 'picture' =>  utf8_encode($r['picture']), 'username' =>  utf8_encode($r['username']));
            }
            $json = array("status" => 1, "info" => $result);
        } else{ $json = array("status" => 2, "msg" => "<strong>ACCESS DENIED !!!</strong> <br/><u>Reason</u>: Login details in-correct. ".mysqli_error(self::$dbObj->connection)); }
        self::$dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** pwdExists checks if a password truely exists in the database
     * @return Boolean True for exists, while false for not
     */
    private function pwdExists(){
        $sql =  "SELECT * FROM ".self::$tableName." WHERE password = '".md5($this->password)."' AND id = $this->id LIMIT 1 ";
        $result = self::$dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    } 
    
    /** Change Password
     * @param string $newPassword New password
     * @return JSON JSON Object success or failure
     */
    public function changePassword($newPassword){
        $sql = "UPDATE ".self::$tableName." SET password = '".md5($newPassword)."' WHERE id = $this->id ";
        $pwdExists = $this->pwdExists();//Check if old password is correct
        if($pwdExists==TRUE){
            $result = self::$dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, user password successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating user password! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Old password you typed is incorrect. Please retype old password."); }
        self::$dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** Reset Password
     * @return JSON JSON Object success or failure
     */
    public function resetPassword(){
        $sql = "UPDATE ".self::$tableName." SET password = '".md5($this->password)."' WHERE email = '$this->email' ";
        if($this->emailExists()){
            $result = self::$dbObj->query($sql);
            if($result != false){ 
                $json = array("status" => 1, "msg" => "Done, password successfully reset! An email has been sent to you."); 
            }
            else{ $json = array("status" => 2, "msg" => "Error reseting  password! ".  mysqli_error(self::$dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "The email you entered does not exist in our database."); }
        self::$dbObj->close();
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            return $_GET['callback'].'('.json_encode($json).');';
        }else{ header('Content-Type: application/json'); return json_encode($json); }
    }
    
    /** emailExists checks if a password truely exists in the database
     * @return Boolean True for exists, while false for not
     */
    public function emailExists(){
        $sql =  "SELECT * FROM ".self::$tableName." WHERE email = '$this->email' LIMIT 1 ";
        $result = self::$dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    }
}