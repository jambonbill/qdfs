<?
echo "<"."?\n";
// Quick&Dirty File System
// connect.php
require "f_command.php";

//phpinfo();

$ver=file("version.txt") or die ("Error with version.txt");

$TOS["VERSION"]=$ver[0];

$TOS["REQUEST_METHOD"]=	$_SERVER["REQUEST_METHOD"];
$TOS["SERVER_ADDR"]=	$_SERVER["SERVER_ADDR"];
$TOS["SERVER_ADMIN"]=	$_SERVER["SERVER_ADMIN"]; 
$TOS["SERVER_NAME"]=	$_SERVER["SERVER_NAME"];
$TOS["SERVER_SOFTWARE"]=$_SERVER["SERVER_SOFTWARE"];
$TOS["SERVER_PORT"]=	$_SERVER["SERVER_PORT"];
$TOS["HTTP_ACCEPT_LANGUAGE"]=	$_SERVER["HTTP_ACCEPT_LANGUAGE"];
$TOS["REQUEST_TIME"]=	$_SERVER["REQUEST_TIME"]; //PHP5
$TOS["REMOTE_HOST"]=	$_SERVER["REMOTE_HOST"];

foreach ($TOS AS $key=>$value){
	echo "\$$key=\"$value\";\n";
}

//$TOS["SERVER_SIGNATURE"]=preg_replace("/[\n]/i","",$_SERVER["SERVER_SIGNATURE"]);
//SESSION
//REGEX
//SAFE MODE

//send();
//echo "<PRE>";
//print_r($TOS);
echo "\n"."?".">";
?>