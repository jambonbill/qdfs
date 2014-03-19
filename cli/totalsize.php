<?php
/**
 * qdfs
 * not sure if this is used
 */

function recursiveDirectory($dir)
{
    global $totals,$dirnum,$filnum;
    $totalsize=0;

    if ($dirnum>1000) {
        die("\nGiving up, tree is too big (>1000 dirs)\n");
        return;
    }

    if ($handle = opendir($dir)) {
        
        while (($file = readdir($handle)) !== false) {
            
            if (($file == ".") || ($file == "..")) {
                continue;
            }

            if (is_dir($dir.'/'.$file)) {
                $dirnum++;
                recursiveDirectory("$dir/$file", $str);
            }
            
            $size= filesize($dir.'/'.$file);
            $totalsize=($totalsize+$size);
            $filnum++;
            $lastmod = date("d/m/Y G:i:s ", fileatime($dir.'/'.$file));
            //echo str_replace("//","/","$dir/$file $size\n");
            $f++;
        }
        $dir=preg_replace("/^.[\/][\/]?/", "", $dir)."/";
        $dir=sprintf("% -32s", substr($dir, 0, 32))." ";

        $totals=$totals+$totalsize;
        //echo "$dir ".printsize($totalsize)."\n";
        closedir($handle);
    }
    return;
}


function locate($str)
{
    global $totals,$dirnum,$filnum;
//  global $rep;
    $totalsize=0;
    if ($str) {
        $data=recursiveDirectory("./");
        echo "--------------------------------------\n";
        echo "Total Directory numbers : $dirnum\n";
        echo "Total files : $filnum\n";
        echo "Total size : ".printsize($totals)."\n";
    } else {
        echo "Error : Type search string.";
    }
}
