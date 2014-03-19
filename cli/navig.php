<?php
/**
 * qdfs::default navigation
 */

// README.TXT FOOTER ////////////////////////////////

function README($fn = '')
{
    //ne pas oublier de maj f_command.php
    $fi=$_SESSION["rep"]."/$fn";
    
    if (is_file($fi)) {
        $R=file($fi);
        $RM="<pre>".htmlentities(implode("", $R))."</pre>";
        $RM=preg_replace("/((http|ftp):\/\/[\d\w\/\.~=+%&_-]+)/", "<A HREF=\"$1\" TARGET=NEW>$1</A>", $RM);
        return $RM;
    } else {
        echo "<li>!is_file($fi)";
    }
}

/**
 * Return a blank target depending on the filename (???)
 * @param  string $fn [description]
 * @return [type]     [description]
 */
function target( $fn = '')
{
    if (!preg_match("/.(\/).?$/i", $fn)) {
        return "target=_blank";
    }
    return '';//no target
}


if (@$rep=="") {
    $rep = "./";
}

//bot detection
if (preg_match("/\b(googlebot|msnbot|psbot|slurp)\b/i", $_SERVER["HTTP_USER_AGENT"])) {
    $ISBOT=true;
}

if (@$_GET["rep"]) {
    $rep=$_GET["rep"];
    $nrep=".".str_replace(realpath("./"), "", realpath($rep));//pas sur qu'on dispose de realpath....
    $_SESSION["rep"]=$nrep;
    $title="$title - " . basename($_SESSION["rep"]);
}

//echo "rep=$rep\n";

//todo : rewrite all this using glob
$glob = glob($_SESSION["rep"] . "/*");
//print_r($files);
$files=[];
$folder=[];

$totalsize=0;

foreach ($glob as $k => $f) {
    
    if (is_dir($f)) {
        $folder[]=basename($f);
    } else {

        $file=basename($f);
        //echo "file=$file\n";

        $sz=@filesize("$rep/$file");
        $filetab[]=$file;// autocompletion
        $totalsize+=$sz;
        
        $sz=round($sz/1024);
        if ($sz<1) {
            $sz=1;
        }
        
        $files["$file"]=sprintf("% 8s", $sz."Ko");
    }
}

/*
$folder=$files=array();
while ($file = readdir($handle)) {
    
    if (@is_dir($rep."/".$file)) {
        
        if (@$_SESSION["debug"]) {
            //echo "skipped: $file\n";
        }
        //if($file==".")continue;
        
        if (preg_match("/\.[\/]+(\.\.|cli|sessions|\.git)$/i", "$rep/$file")) {
            continue;
        }

        $folder[]=$file;

    } else {
        
        if (preg_match("/^(THUMB_)/i", $file)) {
            continue;//vignette
        }
        if (preg_match("/^(index.php)$/i", $file)) {
            continue;
        }
        if (preg_match("/^(readme.(txt|html?))$/i", $file, $o)) {
            $README=README($o[1]);//READ FILE README.TXT
            continue;
        }

        if (@!$_SESSION["isroot"] && preg_match("/.(php|inc).?$/i", $file)) {
            continue;
        }
        
        $sz=@filesize("$rep/$file");
        $filetab[]=$file;
        $totalsize+=$sz;
        $sz=round($sz/1024);
        if ($sz<1) {
            $sz=1;
        }
        $files["$file"]=sprintf("% 8s", $sz."Ko");
        //$files[]=$file;
    }
}
*/

//ksort($files);
//sort($folder);

//closedir($handle);

$_SESSION["f"]=array_merge($folder, $files);//Autocompletion
//sort($files);

$nurl="./".clean($rep);


// display folders
for ($i=0; $i < count($folder); $i++) {//REP
    $str=$folder[$i]."/";
    $str=substr($str, 0, 30);
    $str= sprintf("% -35s", $str);
    if ($nurl=="./" && $i==0) {//rien
    } else {
        echo "<a href=\"?rep=$rep/".$folder[$i]."\" title=\"$rep/$folder[$i]\">".$str."&lt;DIR&gt;</a><br>";
    }
}



// display files

//for($i=0;$i<count($files);$i++){//FILES
foreach ($files as $fn => $fs) {
    
    //echo "fn=$fn\n";
    //echo "fs=$fs\n";

    $ext=ext($fn);//
    $str=$fn;
    $str=substr($str, 0, 30);
    $str= sprintf("% -32s", $str);

    if (@!$ISBOT && preg_match("/.(jpe?g|gif|png|bmp)$/i", $fn)) {
        echo "<a href=\"cli/view.php?of=".urlencode($fn)."\" title=\"$fn\">$str<font color='#FFFF00'>$fs</font></a>";
        echo "<br />";
    } elseif (preg_match("/.(url)$/i", $fn)){//url
        $url=READURL("$rep/$fn");
        $str=substr($fn, 0, 34);
        $str= sprintf("% -35s", $str);
        echo "<a href=\"$url\" target=_blank title=\"$url\">$str&lt;<font color=#0000FF>URL</font>&gt;</a><br />";
    } else {
        $target=target($fn);
//      colorize($filesz[$i]);
        echo "<a href=\"$rep/".$fn."\" $target title=\"$fn\">".$str.$fs."</a><br />";
    }
}
echo "                   ".count($files)." file(s) $totalsize bytes<br />";
echo "                   ".count($folder)." folder(s)<br />";
