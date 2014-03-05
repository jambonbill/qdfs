<?php
/**
 * Locate script(s)
 */

function recursiveDirectory($dir = '', $str = '')
{
    if ($handle = opendir($dir)) {
        $f=0;
        while (($file = readdir($handle)) !== false) {

            if (($file == ".") || ($file == "..")) {
                continue;
            }

            if (is_dir($dir.'/'.$file)) {
                recursiveDirectory("$dir/$file", $str);
            }

            $str=str_replace("*.", ".*", $str);//bof
            $str=str_replace("?", "[\d\w]", $str);
            $reg="/$str/i";

            if (preg_match($reg, "$file")) {
                $size= filesize($dir.'/'.$file);
                if (is_dir($dir.'/'.$file)) {
                    $type="folder";
                } else {
                    $type="file";
                }
                $lastmod = date("d/m/Y G:i:s ", fileatime($dir.'/'.$file));
                echo str_replace("//", "/", "$dir/$file\n");
                $f++;
                if ($f>50) {
                    echo "More than 50 matches, aborting";
                    exit;
                }
            }
        }
        closedir($handle);
    }
    return true;
}

function locate($str)
{
    global $rep;
    if ($str) {
        $data=recursiveDirectory($rep, $str);
    } else {
        return "Error : Type search string.";
    }
}

//Build file database
function recurDir($dir = '', $output = '')
{
    $f=0;
    if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false) {

            if (is_link($dir.'/'.$file)) {
                //die("$dir.'/'.$file is sym_link !!!");
                continue;
            }

            if (($file == ".") || ($file == "..")) {
                continue;
            }

            if (is_dir($dir.'/'.$file)) {
                recurDir("$dir/$file", $output);//Recursion
            }
        
            $size= filesize($dir.'/'.$file);
            $f++;//reseted every recursion

           //$lastmod = date("d/m/Y h:i:s ", fileatime($dir.'/'.$file));
            $file=str_replace("//", "/", "$dir/$file");
            $line="$size\t".preg_replace("/^\./", "", $file);
            echo "<LI>$line";
            $_SESSION["db"][]=$line;

        }
        closedir($handle);
    }
    return;
}
