<?php
//css loading
$cssdir = $clipath."css";

if (is_dir("$cssdir")) {
    
    $handle  = @opendir("$cssdir");
    $style=array();
    
    while ($file = @readdir($handle)) {
        $ext=explode(".", $file);
        //print_r($ext);
        $fil=$ext[0];
        $ext=$ext[count($ext)-1];
        if ($ext=="css") {
            $style[]=$fil;
//          echo "<li>$fil";
        }
    }
} else {
    echo "<li>Error : cant locate css dir\n";
}
