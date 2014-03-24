<?php
// Quick&Dirty File System
// config.php - not really a config script

$fp=realpath("./");
//ini_set("include_path","./inc");//safe mode wont allow it
$inip="./inc/";//ini path

include "password.php";//PASSWORD
if (!$rpwd) {
    die("error:no password file\n");
}



//SESSIONS
if(@!$_SESSION["f"])$_SESSION["f"]=array("./");      //files (auto complete)
if(@!$_SESSION["rep"])$_SESSION["rep"]="./";         //path
if(@!$_SESSION["css"])$_SESSION["css"]=@$style;   //default style
if(@!$_SESSION["css"])$_SESSION["css"]="cmd.css";//default style
if(@!$_SESSION["history"])$_SESSION["history"]=array();//HIST
if(@!$_SESSION["info"])@$_SESSION["info"]="";         //system msgs
if(@!$_SESSION["fr"])$_SESSION["fr"]="1";            //FIRSTRUN 


//FILE FOUND
$header="Quick&Dirty File System v0.1<BR>Copyleft 1979-20??";

if (!$title) {
    $title="QDFS - ".$_SERVER["SERVER_NAME"];
}
//$FOOTER="<IMG SRC=>";//YOUR FOOTER HERE (HTML)


if ($_SESSION["fr"]=="1") {//FIRSTRUN //Ajouter les logs ici !!!
    
    $logfile="./cli/log/".date("Y_m").".log";
    touch($logfile);
    $logstr=date("d/m/Y H:i:s ")." $_SERVER[REMOTE_ADDR]\t:$_SERVER[REMOTE_PORT]\t$_SERVER[HTTP_USER_AGENT]\t$_SERVER[HTTP_REFERER]\n";//strlog

    $infile=file($logfile);
    $fo=fopen($logfile, "w");
    fwrite($fo, $logstr.implode("", $infile));
    fclose($fo);
    
    $_SESSION["fr"]="2";//??
}




//UPLOAD
if (@$uf) {
    if (move_uploaded_file("$uf", $_SESSION["rep"]."/$uf_name")) {
        echo "File '$uf_name' uploaded to $_SESSION[rep]<br>";
    } else {
        echo "File upload error<br>";
        echo "Cant upload to ".$SESSION["rep"]."/$uf_name";
    }
}

//SAVE (editor)
if (@$_POST["txtfile"]) {
    //echo "SAVE ".$SESSION[rep]."/".$_POST[filename]." !!!\n";
    echo "<script>window.status=\"$_POST[filename] saved\";</script>";
    $_POST["txtfile"]=stripslashes($_POST["txtfile"]);
    if ($_POST["newname"]) {
        if (preg_match("/.*.php[\d]?/i", $_POST["newname"])) {
            echo "saving php files is not allowed\n";
            return;
        }
        $fil=fopen("./$SESSION[rep]/$_POST[newname]", "w ");
        fwrite($fil, $_POST["txtfile"]);
        fclose($fil);
    } else {
        rename("./$SESSION[rep]/$_POST[filename]", "./$SESSION[rep]/$_POST[filename]~");
        $fil=fopen("./$SESSION[rep]/$_POST[filename]", "w ");
        fwrite($fil, $_POST["txtfile"]);
        fclose($fil);
    }
}

//SU IDENTIFICATION
if (@$_POST["isroot"]) {
    unset($_POST["isroot"]);
}

if (@$_GET["isroot"]) {
    unset($_GET["isroot"]);
}


if (@$_POST["pwd"]) {
    if (md5($_POST["pwd"]) == $rpwd) {
        $_SESSION["isroot"]=true;
        $_SESSION["rootstr"]="root@";
    } else {
        echo "Wrong password";
        unset($_SESSION["isroot"]);
        $_SESSION["rootstr"]="";
    }
}
