<?php
/////////////////////////////////
// Quick&Dirty File System
// MAIN TEMPLATE !!!

session_start();
ini_set('include_path', './');
include "config.php";
include "f_command.php";
?>
<html>
<head>
<!-- Optional theme -->
<link rel='stylesheet' href='<?php echo $clipath."/css/".$_SESSION['css']?>'>
<title><?php echo $title?></title>
</head>

<body topmargin=8 onload="document.form.i.focus();rez();" onresize="rez()" onclick="locat()">

<pre>
<?php
if (@$_POST["c"]) {//  EXEC
    require "exec.php";
    echo @$PIPE;//?
} else {//    OR USE THE STANDARD DISPLAY
    echo "$header\n\n";
    include "navig.php";
}
?>
</pre>

<?php 
//message to be displayed once only
echo $_SESSION["info"];
?>

<form name='form' method="POST" action="?" onsubmit="go()">

<nobr />[<?php echo @$_SESSION["rootstr"]?><?=$_SERVER["SERVER_NAME"]?> <?=basename($_SESSION["rep"])?>]$ <input type=text id='d' name='i' size='40' value='' style="border:0" onblur="cmp()"></NOBR>

<!--<INPUT TYPE=TEXT VALUE="" ONFOCUS="return false" SIZE="1">-->
<input type="hidden" name="c">
<input type="submit" style="width:0;height:0">
</form>
<br />
<?php
if (@$_SESSION["debug"]) {
    echo "<font color=red>\$_SESSION[rep]=\"$_SESSION[rep]\"</font>\n";
}
?>
<?php echo @$FOOTER?>
<?php echo @$README?>
<?php
if (@$_SESSION["lrep"]!=$_SESSION["rep"]) {
    grab($_SESSION['rep']);
    $_SESSION["lrep"]=$_SESSION["rep"];
}
unset($_SESSION["info"]);//one time message
?>
<div id='debug'></div>
<script>
var f=new Array("<?=implode("\",\"", $_SESSION['f'])?>");    //autocompletion
var h=new Array("<?=implode("\",\"", $_SESSION['history'])?>");  //historique
var rep="<?=addslashes($_SESSION['rep'])?>"; 
window.status=rep;
//Errors
function errorbox(msg, url, linenumber){document.getElementById("debug").innerHTML='Error : '+msg+'<br>URL= '+url+'<br>Line Number= '+linenumber;return true}
window.onerror=errorbox; 
</script>
<script src="<?=$clipath?>/js/cmds.js"></script>
