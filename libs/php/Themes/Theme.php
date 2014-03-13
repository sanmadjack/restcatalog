<?php
namespace Catalog\Themes;
use Exception;

require_once("FileCombiner.php");

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
            throw new Exception("Theme config file for ".$theme." cannot be found or read");
        }
        
        $ini_array = parse_ini_file($ini_file, true);
        if($ini_array==false) {
            throw new Exception("Syntax error in theme config file for ".$theme);
        }
        
        if(!array_key_exists("info",$ini_array)) {
            throw new Exception("Theme config file for ".$theme." does not contain theme info");
        }
        
        if(!array_key_exists("name",$ini_array["info"])) {
            throw new Exception("No name specifeid in theme config file for ".$theme);
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
        $output = new FileCombiner();

        foreach($this->parents as $parent) {
            $output->addFile('themes/'.$parent."/".$file_path,false);
        }
        
        $output->addFile('themes/'.$this->name."/".$file_path,false);

        $output->renderFile(function($data) {
            // Here we insert variables from the ini file into the text output
            if(is_string($data)) {
                $matches = array();
                while(preg_match('|\$\(([^\s]+)\)|',$data,$matches,PREG_OFFSET_CAPTURE)==1) {
                    $var_name = $matches[1][0];
                    $value = "";
                    if(array_key_exists($var_name,$this->variables)) {
                        $value = $this->variables[$var_name];
                    } else {
                        throw new Exception("Variable not found in theme config: ".$var_name);
                    }
    
                    $data = substr($data,0,$matches[0][1]).$value.substr($data,($matches[0][1]+strlen($matches[0][0])));
                
                }
                return $data;
            }            
        });

    }
    
}
?>
