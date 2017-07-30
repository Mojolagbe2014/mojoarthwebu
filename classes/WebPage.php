<?php
/**
 * Description of WebPage
 *
 * @author Kaiste
 */
class WebPage {
    private $id;
    private $name;
    private $title;
    private $description;
    private $keywords;
    private $author;
    private $dbObj;
    private $tableName;
    
    //Class Constructor
    public function WebPage($dbObj=null, $tableName='webpage') {
        $this->dbObj = $dbObj;        $this->tableName = $tableName;
        //includes predefined constants
        include(CONST_FILE_PATH); 
        //Include Databse manipulation handler file
        include(DB_CONFIG_FILE); 
        
        function autoLoadClasses($className){
            $path = CLASSES_PATH;
            $ext = ".php";
            $fullpath = $path.sprintf("%s",$className.$ext);
            if(file_exists($fullpath)){
                return include $fullpath;
            }else{
                echo $fullpath;
            }
        }
        spl_autoload_register('autoLoadClasses');
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
    
    /** Custom message box */
    public static function messageBox($message,$type){
	switch($type){
            case 'error':   $class = 'alert-danger'; break;
            case 'success': $class = 'alert-success'; break;
            case 'info':    $class = 'alert-info'; break;
            case 'warning': $class = 'alert-warning'; break;
        }
        //$html = file_get_contents(TEMPLATE_PATH.'HTML/forms-error-box.html');
        $html = '<div class="alert '.$class.'"><button type="button" class="close" data-dismiss="alert">&times;</button> '.$message.'</div>';
        return $html;
    }
    
    /** Method for dispalying error message  */
    function showError($error){
        if(is_array($error)){
            $msg ="<p>Please attend to the following errors:</p><ul>";
            foreach($error as $error){ $msg .="<li>".$error."</li>"; }     
            $msg .="</ul>";
        }
        else{
            $msg = $error;
        }
        return $this->messageBox($msg, 'error');
    }
    
    /** Redirect() redirects a webpage to $redirectTo
     *  @param string $location String path of the page to be redirected to
     */
    public function redirectTo($location){
       header("location: ".$location);exit;
   }
   
    /**
     * Display active page menu highlighted
     * @param string $url The current page URL
     * @param string $menuName The name of the menu to make active
     * @param string $actPagCssClass The CSS class of active menu
     * @return string CSS class
     */
    public function active($url, $menuName, $actPagCssClass){ 
        if(strpos($url, $menuName)){ 
            return $actPagCssClass;
        } 
     }
     
     /**  
     * Method that adds a webpage into the database
     * @return JSON JSON encoded string/result
     */
    public function add(){
        $sql = "INSERT INTO webpage (name, title, description, keywords) "
                ."VALUES ('{$this->name}','{$this->title}','{$this->description}','{$this->keywords}')";
        if($this->notEmpty($this->name,$this->description,$this->keywords,$this->title)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, webpage successfully added!"); }
            else{ $json = array("status" => 2, "msg" => "Error adding webpage! ".  mysqli_error($this->dbObj->connection)); }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted. All fields must be filled."); }
        
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** 
     * Method for deleting a webpage
     * @return JSON JSON encoded result
     */
    public function delete(){
        $sql = "DELETE FROM webpage WHERE id = $this->id ";
        if($this->notEmpty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, webpage successfully deleted!"); }
            else{ $json = array("status" => 2, "msg" => "Error deleting webpage! ".  mysqli_error($this->dbObj->connection));  }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that fetches webpages from database for JQuery Data Table
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded webpage details
     */
    public function fetchForJQDT($draw, $totalData, $totalFiltered, $customSql="", $column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM webpage ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM webpage WHERE $condition ORDER BY $sort";}
        if($customSql !=""){ $sql = $customSql; }
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){ 
                $multiActionBox = '<input type="checkbox" class="multi-action-box" data-id="'.$r['id'].'" data-name="'.$r['name'].'" />';
                $result[] = array(utf8_encode($multiActionBox), $r['id'], utf8_encode($r['name']), utf8_encode($r['title']), utf8_encode($r['description']), utf8_encode($r['keywords']), utf8_encode(' <div style="white-space:nowrap"><button data-name="'.$r['name'].'" data-id="'.$r['id'].'"  data-description="'.$r['description'].'"  data-title="'.$r['title'].'"  data-keywords="'.$r['keywords'].'" class="btn btn-info btn-sm edit-webpage"  title="Edit"><i class="btn-icon-only icon-pencil"> </i></button> <button data-name="'.$r['name'].'" data-id="'.$r['id'].'" class="btn btn-danger btn-sm delete-webpage" title="Delete"><i class="btn-icon-only icon-trash"> </i></button></div>'));
            }
            $json = array("status" => 1,"draw" => intval($draw), "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $result);
        } 
        else{ $json = array("status" => 2, "msg" => "Necessary parameters not set. Or empty result. ".mysqli_error($this->dbObj->connection), "draw" => intval($draw),  "recordsTotal"    => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => false); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }
    
    /** Method that fetches webpages from database
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g webpage_id > 9
     * @param string $sort column name to be used as sort parameter
     * @return JSON JSON encoded webpage details
     */
    public function fetch($column="*", $condition="", $sort="id"){
        $sql = "SELECT $column FROM webpage ORDER BY $sort";
        if(!empty($condition)){$sql = "SELECT $column FROM webpage WHERE $condition ORDER BY $sort";}
        $data = $this->dbObj->fetchAssoc($sql);
        $result =array(); 
        if(count($data)>0){
            foreach($data as $r){
                $result[] = array("id" => $r['id'], "name" =>  utf8_encode($r['name']), "title" =>  utf8_encode($r['title']), "description" =>  utf8_encode($r['description']), "keywords" =>  utf8_encode($r['keywords']));
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
    
    /** Method that update single field detail of a webpage
     * @param string $field Column to be updated 
     * @param string $value New value of $field (Column to be updated)
     * @param int $id Id of the post to be updated
     * @return JSON JSON encoded success or failure message
     */
    public static function updateSingle($dbObj, $field, $value, $id){
        $sql = "UPDATE webpage SET $field = '{$value}' WHERE id = $id ";
        if(!empty($id)){
            $result = $dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, webpage successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating webpage! ".  mysqli_error($dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $dbObj->close();
        header('Content-type: application/json');
        return json_encode($json);
    }

    /** Method that update details of a webpage
     * @return JSON JSON encoded success or failure message
     */
    public function update() {
        $sql = "UPDATE webpage SET name = '{$this->name}', title = '{$this->title}', description = '{$this->description}', keywords = '{$this->keywords}' WHERE id = $this->id ";
        if(!empty($this->id)){
            $result = $this->dbObj->query($sql);
            if($result !== false){ $json = array("status" => 1, "msg" => "Done, webpage successfully update!"); }
            else{ $json = array("status" => 2, "msg" => "Error updating webpage! ".  mysqli_error($this->dbObj->connection));   }
        }
        else{ $json = array("status" => 3, "msg" => "Request method not accepted."); }
        $this->dbObj->close();
        header('Content-type: application/json');
        return json_encode($json); 
    }

    /** getTitle() fetches the name of a webpage using the webpage $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param int $id Category id of the webpage whose name is to be fetched
     * @return string Name of the webpage
     */
    public static function getTitle($dbObj, $id) {
        $thisPageTitle = '';
        $thisPageTitles = $dbObj->fetchNum("SELECT title FROM webpage WHERE id = '{$id}' LIMIT 1");
        foreach ($thisPageTitles as $thisPageTitles) { $thisPageTitle = $thisPageTitles[0]; }
        return $thisPageTitle;
    }
    
    /** getSingle() fetches the title of an webpage using the course $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $column Table's required column in the datatbase
     * @param int $name Course id of the webpage whose name is to be fetched
     * @return string Name of the webpage
     */
    public static function getSingleByName($dbObj, $column, $name) {
        $thisPagReqVal = '';
        $thisPagReqVals = $dbObj->fetchNum("SELECT $column FROM webpage WHERE name = '{$name}' ");
        foreach ($thisPagReqVals as $thisPagReqVals) { $thisPagReqVal = $thisPagReqVals[0]; }
        return $thisPagReqVal;
    }
    
    /** getSingle() fetches the title of an webpage using the course $id
     * @param object $dbObj Database connectivity and manipulation object
     * @param string $column Table's required column in the datatbase
     * @param int $id Course id of the webpage whose name is to be fetched
     * @return string Name of the webpage
     */
    public static function getSingle($dbObj, $column, $id) {
        $thisPagReqVal = '';
        $thisPagReqVals = $dbObj->fetchNum("SELECT $column FROM webpage WHERE id = '{$id}' ");
        foreach ($thisPagReqVals as $thisPagReqVals) { $thisPagReqVal = $thisPagReqVals[0]; }
        return $thisPagReqVal;
    }
    
    /**
     * Method that returns count/total number of a particular course
     * @param Object $dbObj Datatbase connectivity object
     * @return int Number of courses
     */
    public static function getRawCount($dbObj){
        $sql = "SELECT * FROM webpage ";
        $count = "";
        $result = $dbObj->query($sql);
        $totalData = mysqli_num_rows($result);
        if($result !== false){ $count = $totalData; }
        return $count;
    }
}
