<?php
/**
 * QDFS::Edit controller
 */
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();

if (!$_SESSION["isroot"]) {
    die("<script>document.location.href='index.php';</script>");
}



switch(@$_POST["action"]){

    case "save":
        
        if (!$_POST["fn"]) {
            die("\nalert('Error : !fn');");//Error : no filename
        }//echo "alert(\"switch 'save' as $_POST[fn]\");\n";
    
        // MAGIC QUOTES ////////////////////////////////////////////////////
        if (get_magic_quotes_gpc()) {
            $_POST["txt"]=stripslashes($_POST["txt"]);
        }

        $fn=realpath("../$_SESSION[rep]/$_POST[fn]");
        @unlink("$fn.bak");
        
        if (is_file($fn) && !@rename($fn, "$fn.bak")) {
            echo "window.status=\"Error : cant rename '$fn -> $fn.bak'\";";
        }

        $f=fopen($fn, "w+") or die("alert(\"Error : fopen($fn,w+)\");");
        fwrite($f, $_POST["txt"]);
        fclose($f);
        
        die("alert(\"File saved to ".addslashes($fn)."\");");
        break;
        

    
    case "mailto":

        //echo "alert('switch mailto $_POST[mail]');";
        if (!ereg("^([a-zA-Z0-9_.-]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([a-zA-Z0-9-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$", $_POST["mail"])) {
            die("alert(\"'$_POST[mail]' is invalid.\nPlease enter a valid e-mail address.\");");
        } else {
            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            // Additional headers
            $headers .= "To: $_POST[mail] \r\n";
            $headers .= "From:$_SERVER[SERVER_NAME] <qdfs@free.fr>\r\n";
            if (@mail($_POST["mail"], basename($_POST["fn"]), $_POST["txt"], $headers)) {
                die("alert(\"Mail sent to '$_POST[mail]'\");");
            } else {
                die("alert(\"Error sending email to '$_POST[mail]'\");");
            }
        }

        break;

    case "open"://Open
        //if($_GET["fn"])$_POST["fn"]=$_GET["fn"];
        if (!$_POST["fn"]) {
            die("\nalert('Error : !fn');");//Error : no filename
        }

        $fn="../$_SESSION[rep]/$_POST[fn]";
        if (is_file($fn)) { //OK
            $CHARS=implode("", file($fn));//CHARGEMENT
            die("$CHARS");
        } else {
            echo "File '".basename($fn)."' not found";
        }
        exit;
        break;


    case "browse"://Browse current dir
        if ($_GET["nrep"] && $_GET["nrep"]!="undefined") {
            
            if ($_GET["nrep"]=="..") {//dépile
                $rrep=explode("/", $_SESSION["rep"]);
                array_pop($rrep);
                $nrep=implode("/", $rrep);   //echo "<LI>rrep=$rrep<LI>nrep=$nrep";
            } else {
                $nrep="$_SESSION[rep]/$_GET[nrep]";
            }
            
            if (is_dir("../$nrep")) {
                $nrep=str_replace("//", "/", $nrep);
                $_SESSION["rep"]=$nrep; //echo "<LI>\$_SESSION[rep]=$nrep";
            }
        }
        
        if (!$handle=@opendir("../$_SESSION[rep]")) {
            die("alert(\"Error : failed to open $_SESSION[rep]\");");
        }

        echo "<pre>";
        $dirs=array();
        $files=array();
        
        while ($file=@readdir($handle)) {
            if (@is_dir("../$_SESSION[rep]/$file")) {
                if ($file==".") {
                    continue;
                }
                $dirs[]=$file;
                //contiuplnue;
            } else {
                
                if ($file=="..") {
                    continue;
                }
                
                if (preg_match("/(jpg|gif|png|bmp|mp3|ogg|wav|exe|zip|tgz|ico|wma|mod|xm)$/i", $file)) {
                    continue;//skip...
                }

                if (is_file("../$_SESSION[rep]/$file")) {
                    $size=filesize("../$_SESSION[rep]/$file");
                    if ($size<1048576) {
                        $files["$file"]=$size."b";
                    }
                }
            }
        }
        
        ksort($files);

        foreach ($dirs as $nrep) {
            echo "<A HREF=# ONCLICK='browse(\"$nrep\");'>".sprintf("%-35s &lt;DIR&gt;", "$nrep/")."</A>\n";
        }

        foreach ($files as $file => $size) {
            echo "<A HREF=# ONCLICK='opn(\"$file\");'>".sprintf("%-32s %8s", $file, $size)."</A>\n";
        }

        die();
        break;

    default:
        break;
}



// Load ////////////////////////////////
$fn = "../$_SESSION[rep]/$_SESSION[fn]";
$fstr='';
if (is_file($fn)) { //OK
    $CHARS=implode("", file($fn));//CHARGEMENT
    $TITLE="$fstr";
} elseif ($_SESSION["fn"]){//fichier non trouvé
    $TITLE=$fstr."Error : file '$fn' not found\n";
    $CHARS=$fstr."Error : file '$fn' not found\n";
}
