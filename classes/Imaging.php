<?php
/**
 * Description of Imaging
 *
 * @author Kaiste
 */
class Imaging {
    // Variables 
    private $input; 
    private $output; 
    private $src; 
    private $format; 
    private $quality = 80; 
    private $xInput; 
    private $yInput; 
    private $xOutput; 
    private $yOutput; 
    private $resize; 

    // Set image 
    public function setImage($img)  { 
        // Find format 
        $ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION)); 
        // JPEG image 
        if(is_file($img) && ($ext == "JPG" OR $ext == "JPEG")) { 
            $this->format = $ext; 
            $this->input = ImageCreateFromJPEG($img); 
            $this->src = $img; 
        } 
        // PNG image 
        elseif(is_file($img) && $ext == "PNG") { 
            $this->format = $ext; 
            $this->input = ImageCreateFromPNG($img); 
            $this->src = $img; 
        } 
        // GIF image 
        elseif(is_file($img) && $ext == "GIF") { 
            $this->format = $ext; 
            $this->input = ImageCreateFromGIF($img); 
            $this->src = $img; 
        } 
        // Get dimensions 
        $this->xInput = imagesx($this->input); 
        $this->yInput = imagesy($this->input); 
    } 

    // Set maximum image size (pixels) 
    public function setSize($max_x = 100,$max_y = 100) { 
        // Resize 
        if($this->xInput > $max_x || $this->yInput > $max_y) { 
            $a= $max_x / $max_y; 
            $b= $this->xInput / $this->yInput; 
            if ($a<$b) { 
                $this->xOutput = $max_x; 
                $this->yOutput = ($max_x / $this->xInput) * $this->yInput; 
            } 
            else { 
                $this->yOutput = $max_y; 
                $this->xOutput = ($max_y / $this->yInput) * $this->xInput; 
            } 
            // Ready 
            $this->resize = TRUE; 
        } 
        // Don't resize       
        else { $this->resize = FALSE; } 
    } 
    
    // Set image quality (JPEG only) 
    public function setQuality($quality) { 
        if(is_int($quality)) 
        { 
            $this->quality = $quality; 
        } 
    } 
    
    // Save image 
    public function saveImg($path) { 
        // Resize 
        if($this->resize) 
        { 
            $this->output = ImageCreateTrueColor($this->xOutput, $this->yOutput); 
            ImageCopyResampled($this->output, $this->input, 0, 0, 0, 0, $this->xOutput, $this->yOutput, $this->xInput, $this->yInput); 
        } 
        // Save JPEG 
        if($this->format == "JPG" OR $this->format == "JPEG") 
        { 
            if($this->resize) { imageJPEG($this->output, $path, $this->quality); } 
            else { copy($this->src, $path); } 
        } 
        // Save PNG 
        elseif($this->format == "PNG") 
        { 
            if($this->resize) { imagePNG($this->output, $path); } 
            else { copy($this->src, $path); } 
        } 
        // Save GIF 
        elseif($this->format == "GIF") 
        { 
            if($this->resize) { imageGIF($this->output, $path); } 
            else { copy($this->src, $path); } 
        } 
    } 
    
    // Get width 
    public function getWidth() {  return $this->xInput;  } 
    
    // Get height 
    public function getHeight() {  return $this->yInput;  } 
    
    // Clear image cache 
    public function clearCache() {  @ImageDestroy($this->input);  @ImageDestroy($this->output);  } 
    
    /**
     * @param string $file Path to the image file
     * @param int $reqWidth Minimum image width required
     * @param int $reqHeight Minimum image height required
     * @param string $mode Mode of comparison between the supplied parameters and the main image dimesions.<br/> It has values: <b>min</b> | <b>max</b> | <b>equ</b> <br/>
     * <i>'max' means maximum value for the dimesions, 'min' means minimum value, while 'equ' means it must equal to the specified dimensions</i>
     * @param string $target Targeted comparison property <br/> It has values: <b>both</b> for both width and height | <b>width</b> for width only | <b>height</b> for height only
     * @return mixed String error message for failed dimession test | String 'true' for success
     */
    public static function checkDimension($file, $reqWidth=400, $reqHeight=400, $mode="equ", $target="both"){
        $msg = '';
        if(getimagesize($file) !== false) {
            list($width, $height, $type, $attr) = getimagesize($file);
            $reqDimensions = array('width' => $reqWidth, 'height' => $reqHeight);
            $messageBox = array('width' => '', 'height' => '');
            switch($target){
                case 'both':    foreach($reqDimensions as $reqDimension => $dimensionVal){
                                    switch($mode){
                                        case 'min': $messageBox["$reqDimension"] = ($$reqDimension >= $dimensionVal ) ? 'true'  : "Image dimensions are too small. Minimum $reqDimension is $dimensionVal px. Uploaded image $reqDimension is {$$reqDimension}px"; break;
                                        case 'max': $messageBox["$reqDimension"] = ($$reqDimension <= $dimensionVal ) ? 'true'  : "Image dimensions are too large. Maximum $reqDimension is $dimensionVal px. Uploaded image $reqDimension is {$$reqDimension}px"; break;
                                        case 'equ': $messageBox["$reqDimension"] = ($$reqDimension == $dimensionVal ) ? 'true'  : "Image dimensions are not equal to required dimensions. Required $reqDimension is $dimensionVal px. Uploaded image $reqDimension is {$$reqDimension}px"; break;
                                    }
                                }
                                $msg = ($messageBox["width"]=='true' && $messageBox["height"]=='true') ? 'true' : (($messageBox["width"]=='true') ? '[However, the required width is correct.]' : $messageBox["width"]).'. '.(($messageBox["height"]=='true') ? '[However, the required height is correct.]' : $messageBox["height"]);
                                break;
                default :       switch($mode){
                                    case 'min': $msg = ($$target >= $reqDimensions["$target"]) ? 'true'  : "Image dimensions are too small. Minimum $target is {$reqDimensions[$target]}px. Uploaded image $target is {$$target}px"; break;
                                    case 'max': $msg = ($$target <= $reqDimensions["$target"]) ? 'true'  : "Image dimensions are too large. Maximum $target is {$reqDimensions[$target]}px. Uploaded image $target is {$$target}px"; break;
                                    case 'equ': $msg = ($$target == $reqDimensions["$target"]) ? 'true'  : "Image dimensions are not equal to required dimensions. Required $target is {$reqDimensions[$target]}px. Uploaded image $target is {$$target}px"; break;
                                }    
                                break;
            }
        }else{ $msg = "File is not an image."; }
        return $msg;
    }
} 
