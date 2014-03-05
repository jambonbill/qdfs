<?php
///////////////////////////////////////////////////////
// Quick&Dirty File System
// VIEW.PHP
session_start();
$_SESSION["rep"]=str_replace("\\", "/", $_SESSION["rep"]);//WINDOWS STYLE DEBUG

if (@$_GET["fn"]) {

    $fn="../".$_SESSION["rep"]."/".$_GET["fn"];
    $fn=str_replace("\\", "/", $fn); //CHEMINS WINDOWS DE MERDE
    $fn=str_replace("//", "/", $fn); //CHEMINS WINDOWS DE MERDE
    $fn=str_replace("/./", "/", $fn); //CHEMINS WINDOWS DE MERDE

    
    // Generation de la vignette !! :)
    $wanted_size=100;
    $report=0;//Silent report
    if (is_file($fn)) {
        $pic=explode("/", $fn);
        $thumbnam="THUMB_".$pic[count($pic)-1];
        array_pop($pic);
        $picpath=implode("/", $pic);
        if (!is_file("$picpath/$thumbnam")) {
            include "thumbnail.php";
            $resizer=new thumbnailit;
            $result=$resizer->ResizeImg("$fn", "$picpath/$thumbnam", $wanted_size, $report);
        } else {
            //echo "<LI>thumbnail $picpath/$thumbnam allready exist !<BR>";
        }
    }
    /////////////////////////////////////

    
    
    if (is_file($fn)) {//OK
        $title=basename($fn);
        //echo "//OK $title\n";
        $sz=getimagesize($fn);
        $fz=filesize($fn);
        $picstr="<div id=MAIN><center>";
        $picstr.="<ing id='img' src=\"$fn\" $sz[3] BORDER=0 STYLE='cursor:pointer' onclick=\"gal()\">";
        $picstr.="<p><a href=\"$fn\" target=new>$title - $sz[0]*$sz[1] - $fz.b</a></p>";
        $picstr.="</div>";
        echo "w=$sz[0];\n";
        echo "h=$sz[1];\n";
        echo "document.getElementById('pix').innerHTML=\"".addslashes($picstr)."\";";

    } else {
        echo "alert(\"error : file not found : $fn\");\n";
        exit;
    }
    exit;
}

?>
<LINK REL="stylesheet" HREF="css/<?=$_SESSION["css"]?>">
<BODY TOPMARGIN=0 LEFTMARGIN=0 RIGHTMARGIN=0 BOTTOMMARGIN=0>
<SCRIPT SRC="xhr.js"></SCRIPT>
<SCRIPT SRC="view.js"></SCRIPT>
<STYLE>
PRE{font-family:Fixedsys,Courier,Terminal,monospace;}
#thumb{width:100px;height:100;float:left;text-align:center;color:white;border:solid;margin:8px;border-width:2px;cursor:pointer;}
#thumb:hover{background:white}
#pix{display:none;}

</STYLE>
<?php

//BROWSE
//echo "<SCRIPT>alert('error : !_GET[of]');document.location.href='index.php';</SCRIPT>";
if ($handle = opendir("../".$_SESSION["rep"])) {
} else {
    echo "\nError : failed to open ".$_SESSION["rep"]."\n";
    die();
}

$img=0;//nmbre d'images
$pics=array();
$picjs=array();
$picjs[]="var pixs=new Array();";
while ($file = readdir($handle)){//parcours les images
    if (@is_dir("../".$_SESSION["rep"]."/".$file)) {
    } else {
        if (preg_match("/.(jpe?g|gif|png|bmp)$/i", $file)){//IMAGE ONLY

            if (preg_match("/^(THUMB_)/", $file)) {
                continue;
            } else {
                $pics[$img]["thumb"]="../cli/THUMB.png";//Thumb not found...
                $pics[$img]["name"]=urlencode($file);
                $picjs[]="pixs[$img]=\"$file\";";
                if (is_file("../".$_SESSION["rep"]."/THUMB_$file")) {
                    $pics[$img]["thumb"]="../$_SESSION[rep]/THUMB_$file";
                } elseif (is_file("../".$_SESSION["rep"]."/THUMB_$file.png")) {
                    $pics[$img]["thumb"]="../$_SESSION[rep]/THUMB_$file.png";
                } else {
                    //echo "<LI>$rep/THUMB_$file not found<BR>";
                }
            }
            
            if (@$title==$file) {//Compare avec la selection
                $cur=$img;//Current index
            }
            $lastpic=$file;
            $img++;//compte les images
        } else {
            continue;
        }
    }
}

$gallery=array();

$i=0;
foreach ($pics as $pic) {
    $gallery[]="<SPAN ONCLICK=v($i) ID=thumb><IMG SRC=\"$pic[thumb]\" TITLE=\"$pic[name]\"></SPAN>\n";
    $i++;
}

?>
<title><?=$title?></title>
[<A HREF=index.php>QUIT</A>]&nbsp;QDFS Gallery V.1<HR>
<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>
<TR><TD ID=gal WIDTH="100%"><DIV ID=gallery>
<?php
if (count($gallery)) {
    echo implode("", $gallery);// Affiche les vignettes
}
?>
</DIV>&nbsp;</TD></TR>
</TABLE>

<DIV ID=pix>&nbsp;</DIV>

<SCRIPT>
var ir="<?=$_SESSION[isroot]?>"; 
var fn="<?=$title?>";
var w=0;
var h=0;
<?php
if (count($picjs)) {
    echo implode("", $picjs);
}?>
</SCRIPT>
