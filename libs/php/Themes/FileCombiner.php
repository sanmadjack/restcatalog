<?php
namespace Catalog\Themes;

use Exception;

// A class for automating the combining of text-based web site resources, such as CSS or JavaScript files.
// This allows keeping source files very organized, while still having most of the benefit of requesting 
// only a single file from a web page.
// TODO: Add support for binary files
class FileCombiner {
    private $files = array();
    
    private $mime = null;
    private $extension;
    private $last_modified;
    
    public $binary_mode = "replace";
    
    // Comment blocks to be inserted into files to delineate combined files
    const CSS_COMMENT = '/* %s */';
    const JS_COMMENT = '// %s';
    
    public function __construct($input = null) { 
        if($input!=null) {
            if(is_array($input)) {
                $this->addFiles($input);
            } else if(is_string($input)) {
                $this->addFile($input);
            }
        }
    }
    
    public function addFile($file_path,$must_exist = true) {
        if(file_exists($file_path)) {
            if(is_readable($file_path)) {
                if(filesize($file_path)==0) {
                    return;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file_path);
                finfo_close($finfo);
                
                $extension = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
                
                if($mime=="text/plain") {
                    switch($extension) {
                        case "css":
                            $mime = "text/css";
                            break;
                        case "txt":
                            break;
                        case "js":
                            $mime = "application/javascript";
                            break;
                        default:
                            throw new Exception("Text format not recognized: ".$extension);
                    }
                }
                
                
                // TODO: Enhance this to be able to dynamically allow some mime types to override eachother (for instance, going from jpg to png)
                if($this->mime!=null&&$this->mime!=$mime) {
                    throw new Exception("MIME type mismatch, cannot combine existing data (".$this->mime.") and $file_path (".$mime.")");
                } else if($this->mime==null) {
                    $this->mime = $mime;
                }
                
                $this->extension = $extension;
                
                $last_modified = filemtime($file_path);
                    
                if($this->last_modified==null||$this->last_modified< $last_modified) {
                    $this->last_modified = $last_modified;
                }
        
                array_push($this->files,$file_path);
            } else {
                throw new Exception("Cannot read file: ".$file_path);
            }
        } else if($must_exist) {
            // If the file must exist, then we throw a 404 error
            $this->writeHeaders(404);
            echo "Could not find ".$file_path;
            return;
        }
    }
    public function addFiles($names) {
        foreach($names as $name) {
            $this->addfile($name);
        }
    }
    
    public function renderFile($post_process = null) {
        // If no files were specified, for whatever reason, we throw a 404
        if(sizeof($this->files)==0) {
            $this->writeHeaders(404);
            echo "No files found for combination";
            return;
        }
        
        // First we check if we actually need to send the file, or a 304 indicating that there have been no changes
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == gmdate('r',$this->last_modified)) {
                $this->writeHeaders(304);
                return;
            }
        }
        
        $output;
        switch($this->mime) {
            case "text/plain":
                $output = $this->renderTextFile();
                break;
            case "application/javascript":
                $output = $this->renderTextFile(self::JS_COMMENT);
                break;
            case "text/css":
                $output = $this->renderTextFile(self::CSS_COMMENT);
                break;
            default:
                throw new Exception("The file type: ".$this->mime." is not process-able by the file combiner");
        }
        
        if($post_process!=null) {
            $output = $post_process($output);
        }
        
        $this->writeHeaders();
        
        echo $output;
    }
    
    private function renderTextFile($comment = null) {
        $output = "";
        foreach($this->files as $file) {
            if($comment!=null) {
                $output .= sprintf($comment,$file)."\n\n";
            }
            
            $new_data = file_get_contents($file);
            
            $output .= $new_data."\n\n";
        }
        return $output;
    }
    
    // TODO: Add support for binary files
    private function renderBinaryFile() {
        return null;
    }
    
    private function writeHeaders($code = null) {
        header("Content-type: ".$this->mime);
        header('Cache-Control: public, must-revalidate');
        header("Last-Modified: ".gmdate('r',$this->last_modified));
        //header('ETag: "'.md5($output).'"'); // modification time is good enough for now, need to keep this speedy
        
        if($code!=null) {
            header('X-PHP-Response-Code: '.$code, true, $code);
        }
    }
}

?>