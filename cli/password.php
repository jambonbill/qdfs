<?php
/**
 * QDFS Password
 */

//echo __FILE__;

$passfile="./cli/.passwd";

if (!is_file($passfile)) {
    echo "Password file not found";
} else {

    $rpwd=file("./cli/.passwd")[0];
    //$rpwd=$p[0];
    if (!$rpwd) {
        echo "<li>File is empty\n";
    }

}


//echo "passwd : $rpwd\n";
