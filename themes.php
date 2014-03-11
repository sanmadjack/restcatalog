<?php
// Entry file for requesting a theme file
// Expect input in the form of /themename/filepath.ext
// Input should be in either of these URLs, depending on mod_rewrite setup:
// http://site.com/themes/themename/filepath.ext (with mod_rewrite)
// http://site.com/themes.php/themename/filepath.ext (without mod_rewrite)

require "libs/php/Themes/Theme.php";


try {
    
    if(array_key_exists("PATH_INFO",$_SERVER)) {
        $path = trim($_SERVER['PATH_INFO'],"/");
    } else {
        throw new Exception("No theme or file specified");
    }

    $paths = array_filter(explode("/",$path,2));
    

    switch(count($paths)) {
        case 0:
            throw new Exception("No theme or file specified");
        case 1:
            throw new Exception("No file specified");       
    }
    
    $theme = new Catalog\Themes\Theme($paths[0]);
    $theme->outputThemeFile($paths[1]);
    
} catch(Exception $e) {
    header('Content-type: text/plain');
    echo $e->getMessage();
    echo "\n";
    echo $e->getTraceAsString();
}

?>