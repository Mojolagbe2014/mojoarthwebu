<?php
/**
 * Description of Video
 *
 * @author Kaiste
 */
class Video implements ContentManipulator{
    private $id;
    private $name;
    private $description;
    private $video;
    private $dbObj;
    
    
    //Class constructor
    public function Video($dbObj=null) {
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
     * Method that adds a video into the database
     * @return JSON JSON encoded string/result
     */
    function add(){
        $sql = "INSERT INTO video (name, description, video) "
                ."VALUES ('{$this->name}','{$this->description}','{$this->video}')";
        if($this->notEmpty($this->name,$this->description,$this->video)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, video successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding video! ".  mysqli_error($this->dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a video
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM video WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, video successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting video! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches course videos from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g video_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded coursevideo details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM video ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM video WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-name="'.$r['name'].'" data-video="'.$r['video'].'" />';
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($r['name']), utf8_encode($r['description']), utf8_encode('<div class="sc_video_frame" data-width="100%" data-height="647" style="width:100%;"><video width="200" height="200" preload="metadata" controls><source src="'.MEDIA_FILES_PATH1.'video/'.$r['video'].'" type="video/mp4"></video></div>'), utf8_encode(' <div style="white-space:nowrap;"><button data-name="'.$r['name'].'" data-id="'.$r['id'].'"  data-description="'.$r['description'].'"  data-video="'.$r['video'].'" class="btn btn-info btn-small edit-video"  title="Edit"><i class="btn-icon-only icon-pencil"> </i></button> <button data-name="'.$r['name'].'"   data-video="'.$r['video'].'" data-id="'.$r['id'].'" class="btn btn-danger btn-small delete-video" title="Delete"><i class="btn-icon-only icon-trash"> </i></button></div>'));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches course videos from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g video_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded coursevideo details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM video ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM video WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), "description" =>  utf8_encode($r['description']), "video" =>  utf8_encode($r['video']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection)); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches course videos from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g video_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array Video list
     */
    public function fetchRaw($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM video ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM video WHERE $condition ORDER BY $sort";}
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
    
    /** Method that update single field detail of a video
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE video SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, video successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating video! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a video
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE video SET name = '{$this->name}', description = '{$this->description}', video = '{$this->video}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, video successfully updated!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating video! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** getName() fetches the name of a video using the video $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id Video id of the video whose name is to be fetched
     * @return string Name of the video
     */
    public static function getName($dbObj, $id) {
        $thisReqVal = '';
        $thisReqVals = $dbObj->fetchNum("SELECT name FROM video WHERE id = '{$id}' LIMIT 1");
        foreach ($thisReqVals as $thisReqVals) { $thisReqVal = $thisReqVals[0]; }
        return $thisReqVal;
    }
    
    /**
     * Method that returns count/total number of a particular course
     * @param Object $dbObj Datatbase connectivity object
     * @return int Number of videos
     */
    public static function getRawCount($dbObj, $dbPrefix){
        $tableName = $dbPrefix.'video';
        $sql = "SELECT * FROM $tableName ";
        $count = "";
        $result = $dbObj->query($sql);
        $totalData = mysqli_num_rows($result);
        if($result !== false){ $count = $totalData; }
        return $count;
    }
}
