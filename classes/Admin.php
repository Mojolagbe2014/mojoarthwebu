<?php
/**
 * Description of Admin
 *
 * @author Kaiste
 */
class Admin{
    private $id;
    private $name;
    private $email;
    private $userName;
    private $passWord;
    private $role;
    private $dateRegistered = " CURRENT_DATE ";
    private $dbObj;




    //Class constructor
    public function Admin($dbObj) {
        $this->dbObj = $dbObj;
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
     * Method that adds a admin into the database
     * @return JSON JSON encoded string/result
     */
    function add(){
        $sql = "INSERT INTO admin (name, email, username, password, role, date_registered) "
                ."VALUES ('{$this->name}','{$this->email}','{$this->userName}','".md5($this->passWord)."','{$this->role}',$this->dateRegistered)";
        if($this->notEmpty($this->name,$this->email,$this->role)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, admin successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding admin! ".  mysqli_error($this->dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a admin
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM admin WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, admin successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting admin! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches admins from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded admin details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM admin ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM admin WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $fetAdminRole = 'icon-check-empty'; $fetAdminRolCol = 'btn-warning'; $fetAdminRolTit = "Upgrade Admin";
                if($r['role']== "Admin"){  $fetAdminRole = 'icon-check'; $fetAdminRolCol = 'btn-success'; $fetAdminRolTit = "Degrade Admin";}
                $deleteActionLink = '<button data-role="'.$r['role'].'" data-name="'.$r['name'].'" data-id="'.$r['id'].'" class="btn '.$fetAdminRolCol.' btn-small upgrade-admin"  title="'.$fetAdminRolTit.'"><i class="btn-icon-only '.$fetAdminRole.'"> </i></button> <button data-role="'.$r['role'].'" data-id="'.$r['id'].'" data-name="'.$r['name'].'" class="btn btn-danger btn-small delete-admin" title="Delete"><i class="btn-icon-only icon-trash"> </i></button>';
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-role="'.$r['role'].'" />';
                if($r['role']== "Admin" && $r['email'] == trim(stripcslashes(strip_tags(Setting::getValue($this->dbObj, 'COMPANY_EMAIL'))))){ $deleteActionLink = ''; $multiActionBox = '';}
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($r['name']), utf8_encode($r['email']),  utf8_encode($r['username']),  utf8_encode($r['role']),  utf8_encode($r['date_registered']), utf8_encode(' <button data-role="'.$r['role'].'" data-name="'.$r['name'].'" data-email="'.$r['email'].'"  data-username="'.$r['username'].'" data-id="'.$r['id'].'" class="btn btn-info btn-small edit-admin"  title="Edit"><i class="btn-icon-only icon-pencil"> </i></button> '.$deleteActionLink));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches admins from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded admin details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM admin ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM admin WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), 'email' =>  utf8_encode($r['email']), 'userName' =>  utf8_encode($r['username']), 'role' =>  utf8_encode($r['role']), 'dateRegistered' =>  utf8_encode($r['date_registered']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection)); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
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
    
    /** Method that update single field detail of a admin
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE admin SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, admin successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating admin! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a admin
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE admin SET name = '{$this->name}', email = '{$this->email}', username = '{$this->userName}', role = '{$this->role}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, admin successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating admin! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** Change Password
     * @param string $newPassword New password
     * @return JSON JSON Object success or failure
     */
    public function changePassword($newPassword){
        $sql = "UPDATE admin SET password = '".md5($newPassword)."' WHERE id = $this->id ";
        $pwdExists = $this->pwdExists();//Check if old password is corect
        if($pwdExists==TRUE){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, admin password successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating admin password! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Old password you typed is incorrect. Please retype old password."); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** pwdExists checks if a password truely exists in the database
     * @return Boolean True for exists, while false for not
     */
    public function pwdExists(){
        $sql =  "SELECT * FROM admin WHERE password = '".md5($this->passWord)."' AND id = $this->id LIMIT 1 ";
        $result = $this->dbObj->fetchAssoc($sql);
        if($result != false){ return true; }
        else{ return false;    }
    } 
}
