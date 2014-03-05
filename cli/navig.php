<?php
/**
 * Default navigation
 */


if (@$rep=="") {
    $rep = "./";
}

if (preg_match("/\b(googlebot|msnbot|psbot|slurp)\b/i", $_SERVER["HTTP_USER_AGENT"])) {
    $ISBOT=true;
}

if (@$_GET["rep"]) {
    $rep=$_GET["rep"];
    $nrep=".".str_replace(realpath("./"), "", realpath($rep));//pas sur qu'on dispose de realpath....
    $_SESSION["rep"]=$nrep;
    $title="$title - ".basename($_SESSION["rep"]);
}

if (!$handle = opendir($rep)) {
    echo "\nError : failed to open $rep\n";
}

// README.TXT FOOTER ////////////////////////////////
function README($fn = '')
{
    //ne pas oublier de maj f_command.php
    $fi=$_SESSION["rep"]."/$fn";
    if (is_file($fi)) {
        $R=file($fi);
        $RM="<PRE>".htmlentities(implode("", $R))."</PRE>";
        $RM=preg_replace("/((http|ftp):\/\/[\d\w\/\.~=+%&_-]+)/", "<A HREF=\"$1\" TARGET=NEW>$1</A>", $RM);
        return $RM;
    } else {
        echo "<li>!is_file($fi)";
    }
}

$folder=$files=array();
$totalsize=0;

while ($file = readdir($handle)) {
    
    if (@is_dir($rep."/".$file)) {
        
        if (@$_SESSION["debug"]) {
            echo "skipped: $file\n";
        }
        //if($file==".")continue;
        
        if (preg_match("/\.[\/]+(\.\.|cli|sessions|\.git)$/i", "$rep/$file")) {
            //echo "<LI>Skip : $rep/$file\n";
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
ksort($files);
closedir($handle);

$_SESSION["f"]=array_merge($folder, $files);//Autocompletion
sort($folder);
//sort($files);

$nurl="./".clean($rep);

for ($i=0; $i < count($folder); $i++) {//REP
    $str=$folder[$i]."/";
    $str=substr($str, 0, 30);
    $str= sprintf("% -35s", $str);
    if ($nurl=="./" && $i==0) {//rien
    } else {
        echo "<A HREF=\"?rep=$rep/".$folder[$i]."\" TITLE=\"$rep/$folder[$i]\">".$str."&lt;DIR&gt;</a><br>";
    }
}

//for($i=0;$i<count($files);$i++){//FILES
foreach ($files as $fn => $fs) {
    $ext=ext($fn);//
    $str=$fn;
    $str=substr($str, 0, 30);
    $str= sprintf("% -32s", $str);

    if (@!$ISBOT && preg_match("/.(jpe?g|gif|png|bmp)$/i", $fn)) {
        echo "<A href=\"cli/view.php?of=".urlencode($fn)."\" TITLE=\"$fn\">$str<FONT COLOR=#FFFF00>$fs</FONT></A><BR>";
    } elseif (preg_match("/.(url)$/i", $fn)){//url
        $url=READURL("$rep/$fn");
        $str=substr($fn, 0, 34);
        $str= sprintf("% -35s", $str);
        echo "<A HREF=\"$url\" TARGET=NEW TITLE=\"$url\">$str&lt;<FONT COLOR=#0000FF>URL</FONT>&gt;</A><BR>";
    } else {
        $target=target($fn);
//      colorize($filesz[$i]);
        echo "<A HREF=\"$rep/".$fn."\" $target TITLE=\"$fn\">".$str.$fs."</A><BR>";
    }
}
echo "                   ".count($files)." file(s) $totalsize bytes<BR>";
echo "                   ".count($folder)." folder(s)<BR>";


function colorize($str)
{

    $colors["m3u"]="#FF0000";
    $colors["mp3"]="#FFCC00";
    $colors["gif"]="#00FF00";
    $colors["php"]="#0000FF";
    $colors["htm"]="#0000FF";
    $colors["html"]="#6666FF";
    $colors["txt"]="#FFFFFF";
    $colors["js"] ="#FFFF00";

    echo "<li>$str\n";
}

function READURL($file)
{
    $f=file($file);
    foreach ($f as $value) {
        if (preg_match("/URL=(.*)$/i", $value, $o)) {
            $o[1]=preg_replace("[\n\r]", "", $o[1]);
            return $o[1];
        }
    }
    
}

//open file at the right place
function target($fn)
{
    if (!preg_match("/.(\/).?$/i", $fn)) {
        return "TARGET=\"NEW\"";
    }
    return "";
}



if (@$ISBOT) {
    echo connect();//Pour les crawlers !
}
