<?php
namespace Catalog\Themes;

class Theme {
    private $name;
    private $display_name;
    
    private $parents = array();
    private $variables = array();
    
    public function __construct($theme_name) { 
        $this->name = $theme_name;
        $this->loadTheme();
    }
    
    private function loadTheme() {
        $this->loadThemeIni($this->name);
        
    }
    
    private function loadThemeIni($theme,$is_parent = false) {
        $ini_file = 'themes/'.$theme.'/theme.ini';
        if(!is_readable($ini_file)) {
            throw new \Exception("Theme config file for ".$theme." cannot be found or read");
        }
        
        $ini_array = parse_ini_file($ini_file, true);
        if($ini_array==false) {
            throw new \Exception("Syntax error in theme config file for ".$theme);
        }
        
        if(!array_key_exists("info",$ini_array)) {
            throw new \Exception("Theme config file for ".$theme." does not contain theme info");
        }
        
        if(!array_key_exists("name",$ini_array["info"])) {
            throw new \Exception("No name specifeid in theme config file for ".$theme);
        }
        $name = $ini_array["info"]["name"];
        
        // Load the theme's parent, recursively if necessary
        if(array_key_exists("parent",$ini_array["info"])) {
            $this->loadThemeIni($ini_array["info"]["parent"],true);
        }
        
        if($is_parent) {
            array_push($this->parents,$theme);
        }
        
        // We load the variables after the parent check, so that the parent's variables will get loaded first, 
        // thus allowing the child theme to override them
        if(array_key_exists("variables",$ini_array)) {
            foreach($ini_array["variables"] as $key=>$value) {
                $this->variables[$key] = $value;
            }
        }
    }
    
    public function outputThemeFile($file_path) {
        $output = array();
        $output["data"] = null;
        $output["mime"] = null;
        
        foreach($this->parents as $parent) {
            $temp = 'themes/'.$parent."/".$file_path;
            $output = $this->processThemeFile($temp,$output);
        }
        
        $temp = 'themes/'.$this->name."/".$file_path;
        $output = $this->processThemeFile($temp,$output);
        
        
        if($output["data"]==null) {
            throw new \Exception($file_path." could not be found");
        }
        
        // Here we insert variables from the ini file into the text output
        if(is_string($output["data"])) {
            $data = $output["data"];
            $matches = array();
            while(preg_match('|\${([^\s]+)}|',$data,$matches,PREG_OFFSET_CAPTURE)==1) {
                $var_name = $matches[1][0];
                $value = "";
                if(array_key_exists($var_name,$this->variables)) {
                    $value = $this->variables[$var_name];
                } else {
                    throw new \Exception("Variable not found in theme config: ".$var_name);
                }

                $data = substr($data,0,$matches[0][1]).$value.substr($data,($matches[0][1]+strlen($matches[0][0])));
            
            }
            $output["data"] = $data;
        }
        
        header('Content-type: '.$output["mime"]);
        echo $output["data"];
        
    }
    
    private $textOffset = 0;

    private function replacePlaceholder($data,$match) {
        
        
        return $data;
    }
    
    // Comment blocks to be inserted into files to delineate combined files
    const CSS_COMMENT = '/* CSS contents from file %s */';

    
    public function processThemeFile($file_path, $output) {
        if(file_exists($file_path)) {
            if(is_readable($file_path)) {
                $extension = pathinfo($file_path,PATHINFO_EXTENSION);
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file_path);
                finfo_close($finfo);
                
                $appendable_text = false;
                $comment = "";
                $new_data = null;
                
                if($mime=="text/plain") {
                    switch($extension) {
                        case "css":
                            $mime = "text/css";
                            break;
                    }
                    
                }
                
                if(filesize($file_path)==0) {
                    return $output;
                }
                
                if($output["mime"]!=null&&$output["mime"]!=$mime) {
                    throw new \Exception("MIME type mismatch, cannot combine existing data (".$output["mime"].") and $file_path (".$mime.")");
                }
                
                switch($mime) {
                    case "text/plain":
                        $appendable_text = true;
                        $new_data = file_get_contents($file_path);
                        break;
                    case "text/css":
                        $appendable_text = true;
                        $comment = sprintf(self::CSS_COMMENT,$file_path);
                        $new_data = file_get_contents($file_path);
                        break;
                    default:
                        throw new \Exception("The file type: ".$mime." is not process-able by the theme system");
                }

                if($appendable_text) {
                    if(is_null($output["data"])) {
                        if(!is_null($comment)&&!strlen($comment)==0) {
                            $output["data"] = $comment."\n\n".$new_data;
                        } else {
                            $output["data"] = $new_data;
                        }
                    } else if(is_string($output["data"])) {
                        if(strlen($new_data)>0) {
                            if(!is_null($comment)&&!strlen($comment)==0) {
                                $output["data"] .= "\n\n".$comment."\n\n".$new_data;
                            } else {
                                $output["data"] .= "\n\n".$new_data;
                            }
                        } else { // Empty file, we skip!
                            return $output;
                        }
                    } else {
                        throw new Exception("Attempted to concatenate string data onto binary data; do NOT mix file types with the same name in themes");
                    }
                } else {
                    
                }
                
                $output["mime"] = $mime;
            } else {
                throw new \Exception("Cannot read file: ".$temp);
            }
        }
        return $output;
    }
}
?>
