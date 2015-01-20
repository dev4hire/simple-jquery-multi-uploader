<?php
require 'php_class/uploader.php';

/**
 * Provided for demonstration and testing
 * if you wish to understand whats going on read the 
 * comments in the uploader class file
 */

$name  = 'image';
$dir   = 'uploads';
$exts  = array('jpeg','jpg','png');
$limit = 2;

$upload = new uploader($name, $dir, $exts, $limit);
$upload->unique_name(true);
$upload->upload();
$outcome = $upload->getResults();
echo json_encode($outcome);

?>