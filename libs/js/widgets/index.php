<?php
require_once("../../php/Themes/FileCombiner.php");
$output = new Catalog\Themes\FileCombiner(array("AWidget.js","AInputWidget.js","TextInputWidget.js"));
$output->renderFile();
?>