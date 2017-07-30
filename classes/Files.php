<?php
/**
 * Description of Files
 *
 * @author Kaiste
 */
class Files implements ContentManipulator{
    private $id;
    private $contentHash;
    private $contextId;
    private $component;
    private $fileArea;
    private $itemId;
    private $filePath;
    private $fileName;
    private $userId;
    private $tableName;
    private $dbObj;
    
    
    //Class constructor
    public function Files($dbObj, $tablePrefix='') {
        $this->tableName = $tablePrefix.'files';
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
     * Method that adds a files into the database
     * @return JSON JSON encoded string/result
     */
    function add(){ }

    /** 
     * Method for deleting a files
     * @return JSON JSON encoded result
     */
    public function delete(){ }
    
    /** Method that fetches files from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded files details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM $this->tableName ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM $this->tableName WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "contentHash" =>  utf8_encode($r['contenthash']), "contextId" =>  utf8_encode($r['contextid']), 'component' =>  utf8_encode($r['component']), 'fileArea' => utf8_encode($r['filearea']), 'itemId' => utf8_encode($r['itemid']), 'filePath' => utf8_encode($r['filepath']), 'fileName' => utf8_encode($r['filename']), 'userId' => utf8_encode($r['userid']));
            }
            $json = array("status" => 1, "info" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Empty result. ".mysqli_error($this->dbObj->connection)); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches files from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return Array Filess list
     */
    public function fetchRaw($column="*", $condition="", $sort="id"){
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
    
    /** Method that update single field detail of a files
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){ }

    /** Method that update details of a files
     * @return JSON JSON encoded success or failure message
     */
    public function update() { }
      
    /** getSingle() fetches the title of an files using the files $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $dbPrefix Moodle database name prefix
     * @param string $column Table's required column in the datatbase
     * @param int $condition Files addition query
     * @return string Name of the files
     */
    public static function getSingle($dbObj, $dbPrefix, $column, $condition = ' 1 = 1 ') {
        $thisAsstReqVal = ''; $tableName = $dbPrefix.'files';
        $thisAsstReqVals = $dbObj->fetchNum("SELECT $column FROM $tableName WHERE $condition ");
        foreach ($thisAsstReqVals as $thisAsstReqVals) { $thisAsstReqVal = $thisAsstReqVals[0]; }
        return $thisAsstReqVal;
    }
    
    /** getCourseImage() fetches the image name
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $dbPrefix Moodle database name prefix
     * @param int $contextId Context Id of the course
     * @return string Name of the files
     */
    public static function getCourseImage($dbObj, $dbPrefix, $contextId) {
        $thisAsstReqVal = ''; $tableName = $dbPrefix.'files';
        $thisAsstReqVals = $dbObj->fetchNum("SELECT filename FROM $tableName WHERE contextid = $contextId AND component = 'course' AND filearea = 'overviewfiles' AND filename !='.' LIMIT 1");
        foreach ($thisAsstReqVals as $thisAsstReqVals) { $thisAsstReqVal = $thisAsstReqVals[0]; }
        return $thisAsstReqVal;
    }
    
    /**
     * Method that returns count/total number of a particular files
     * @param Object $dbObj Datatbase connectivity object
     * @return int Number of files
     */
    public static function getRawCount($dbObj, $dbPrefix){
        $tableName = $dbPrefix.'files';
        $sql = "SELECT * FROM $tableName ";
        $count = "";
        $result = $dbObj->query($sql);
        $totalData = mysqli_num_rows($result);
        if($result !== false){ $count = $totalData; }
        return $count;
    }
}
