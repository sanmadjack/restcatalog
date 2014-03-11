<?php

header("Content-type: application/javascript");

$usable_files = array("AWidget.js","AInputWidget.js","TextInputWidget.js");



foreach($usable_files as $file) {
    echo '// '.$file."\n\n";
    echo file_get_contents($file);
    echo "\n\n";
    
}


?>