<?
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();
///////////////////////////////////////////////////////
// Quick&Dirty File System - bill@parishq.net
// EXPLORER.PHP - XHR

include "password.php";//PASSWORD
include "f_command.php";

//echo "//action=$_POST[action]\n";
$action=$_POST["action"];

$_SESSION["rep"]=clean($_SESSION["rep"]."/".$_POST["dir"]);

switch($action){

	case "cd":
		load();
		break;

	case "mkdir":
		if(!$_SESSION["isroot"])die("alert(\"Permission denied\");");
		$nn=$_POST["nn"];
		echo "//Create folder $nn\n";
		if(is_dir("../$_SESSION[rep]/$nn"))die("alert(\"Folder '$nn' allready exist\");\n");
		if(@mkdir("../$_SESSION[rep]/$nn",0777)){
			//echo "alert(\"file $fn deleted\");\n";
			die("cd('.');");
		}else{
			die("alert(\"Error creating folder $nn\");\n");
		}
		break;

	case "rename":
		if(!$_SESSION["isroot"])die("alert(\"Permission denied\");");
		$fn=stripslashes($_POST["fn"]);
		$nn=$_POST["nn"];
		echo "//rename $fn to $fn\n";

		if(rename("../$_SESSION[rep]/$fn", "../$_SESSION[rep]/$nn")){
			//ok
		}else{
			die("alert(\"error renaming $fn\");\n");
		}
		load();
		break;

	case "delete":
		if(!$_SESSION["isroot"])die("alert(\"Permission denied\");");
		$fn=$_POST["fn"];//files
		$dn=$_POST["dn"];//folders
		
		$out="";
		for($i=0;$i<count($fn);$i++){
			if($fn[$i]=="..")continue;
			//echo "alert(\"rem $fn[$i]\");";
			$rem=rem("../$_SESSION[rep]/".stripslashes($fn[$i]));
			if($rem)$out.="$rem\\n";
		}

		for($i=0;$i<count($dn);$i++){
			if($dn[$i]=="..")continue;
			//echo "alert(\"rem $fn[$i]\");";
			$rem=remdirectory("../$_SESSION[rep]/$dn[$i]");
			if($rem)$out.="$rem\\n";
		}

		if($out)echo "alert(\"$out\");\n";
		die("cd('.');");
		break;

	case "edit":
		$_SESSION["action"]="edit";
		$_SESSION["fn"]=$_POST["fn"];
		die("document.location.href='?';\n");
		break;

	case "kill":
		$_SESSION["action"]="";
		die("document.location.href='?';\n");
		break;

	case "readme":
		echo readme();
		die();
		break;

	case "preview";
		die(preview($_POST["fn"]));
		break;

	case "move";
		if(!$_SESSION["isroot"])die("alert(\"Permission denied\");");
		$target=$_POST["target"];//target directory
		if(!is_dir("../".$_SESSION["rep"]."/$target"))die("alert(\"Error : target directory '$target' do not exist\");");
		$fn=$_POST["fn"];//files
		$dn=$_POST["dn"];//folders
		for($i=0;$i<count($fn);$i++){
			$fn[$i]=stripslashes($fn[$i]);
			if(!is_file("../".$_SESSION["rep"]."/".$fn[$i]))die("alert(\"file '$fn[$i]' not found\");");
			rename("../".$_SESSION["rep"]."/".$fn[$i],"../".$_SESSION["rep"]."/$target/".$fn[$i]) or die("alert('rename error');");
		}
		//rename(from,to);
		//$fn=$_POST["fn"];//files
		//die(preview($_POST["fn"]));
		load();
		break;

	case "login":
		if(md5($_POST["login"])==$rpwd){
			$_SESSION["isroot"]=true;
			die("alert('Your are logged as ROOT');");
		}else{
			die("alert('Login failed');");
			unset($_SESSION["isroot"]);
		}
		break;

	case "wget":
		if(!$_SESSION["isroot"])die("alert(\"Permission denied\");");
		$fn=$_POST["fn"];
		//echo "alert(\"wget $fn\");\n";
		$newname=basename($fn);
		if(@copy("$fn","../$_SESSION[rep]/$newname")){
			//die("alert(\"$url downloaded to ".$_SESSION["rep"]."/$newname\");\n");
			die("cd('.');");
		}else{
			die("alert(\"Failed to retreive $fn\);\n");
		}
		break;

}

function readme(){
	//return "../$_SESSION[rep]/";
	//echo "test";
	return $_SESSION["README"];
}

function rem($fn){//remove file
	if(!is_file("$fn"))return "file '$fn' not found";
	if(@unlink($fn)){
		//return "file ".basename($fn)." deleted";
	}else{
		return "Error unlinking ".basename($fn);
	}
}

function remdirectory($fn){
	if(!is_dir("$fn"))return "folder '$fn' not found";
	if(@rmdir($fn)){
	}else{
		return "Error removing foler ".basename($fn)." (maybe it's not empty)";
	}
}


function load(){

	if(!$handle=@opendir("../$_SESSION[rep]"))die("alert(\"Error : failed to open $_SESSION[rep]\");");
	$dirs=Array();
	$files=Array();
	while($file=@readdir($handle)){
		if(@is_dir("../$_SESSION[rep]/$file")){		
			if($file==".")continue;
			if($file=="cli")continue;
			if($file=="sessions")continue;
			if($_SESSION["rep"]=="" && $file=="..")continue;
			$dirs[]=$file;
		}else{
			if(preg_match("/^THUMB_/i",$file))continue;//vignettes
			if(preg_match("/^\./i",$file))continue;//.htaccess etc
			if(preg_match("/^readme\.(txt)$/i",$file,$o)){
				$_SESSION["README"]=implode("",file("../$_SESSION[rep]/$file"));
				//$_SESSION["README"]="readme=$o[0]";
				$readme=$o[0];//README !!
			}

			//if(!preg_match("/(gif|jpe?g|png|bmp)$/i",$file))continue;
			$files[]=$file;
		}
	}

	$out=Array();
	for($i=0;$i<count($dirs);$i++){
		$modtime=date("YmdHi", @filemtime("../".$_SESSION["rep"]."/".$dirs[$i]));//date de modif
		$out[]="['d',\"$dirs[$i]\",0,'$modtime','$perms']";
	}

	for($i=0;$i<count($files);$i++){	
		$fsize=@filesize("../".$_SESSION["rep"]."/".$files[$i]);//taille
		$modtime=date("YmdHi", @filemtime("../".$_SESSION["rep"]."/".$files[$i]));//date de modif
		$out[]="['f',\"$files[$i]\",$fsize,'$modtime']";
	}

	echo "fils=new Array();\n";
	for($i=0;$i<count($out);$i++)echo "fils[$i]=$out[$i];\n";
	echo "build();\n";
	echo "document.getElementById('dir').value=\"/$_SESSION[rep]\";\n";
	echo "document.title=\"QDFS /$_SESSION[rep]\";\n";
	
	if($readme)echo ("readme(\"$readme\");\n");

}

function recursiveRemoveDirectory($path){   
	$dir = new RecursiveDirectoryIterator($path);
	//Remove all files
	foreach(new RecursiveIteratorIterator($dir) as $file){
		unlink($file);
	}
	//Remove all subdirectories
	foreach($dir as $subDir){
		//If a subdirectory can't be removed, it's because it has subdirectories, so recursiveRemoveDirectory is called again passing the subdirectory as path
		if(!@rmdir($subDir)){//@ suppress the warning message
			recursiveRemoveDirectory($subDir);
		}
	}
	//Remove main directory
	rmdir($path);
}

function preview($filename){
	$filename=stripslashes($filename);
	if(!$filename)return;
	if(!is_file("../".$_SESSION["rep"]."/".$filename))return "Error : $filename";
	$fsize=@filesize("../".$_SESSION["rep"]."/".$filename);//taille
	$modtime=date("Y/m/d H:i:s", @filemtime("../".$_SESSION["rep"]."/".$filename));//date de modif
	$basename=@basename("../".$_SESSION["rep"]."/".$filename);
	//
	//$html.="<A HREF=\"".$_SESSION["rep"]."/$filename\" TARGET=new><IMG SRC=cli/gif/file.gif BORDER=0>&nbsp;$filename</A>";
	$html.="<CENTER><BR>";
	$html.="<TABLE WIDTH=96% STYLE='border:solid;border-width:1;border-color:black;' CLASS=nfo>";
	$html.="<TR><TD ALIGN=RIGHT WIDTH=100><B>Path :</B><TD>".$_SESSION["rep"]."/$filename";
	$html.="<TR><TD ALIGN=RIGHT WIDTH=100><B>Size :</B><TD>$fsize b";
	$html.="<TR><TD ALIGN=RIGHT WIDTH=100><B>Modified :</B><TD> $modtime";
	$html.="</TABLE>";

	$parts=explode(".",$filename);
	$ext=strtolower($parts[count($parts)-1]);

	if(is_file("./gif/$ext.gif")){
		$icon="<IMG SRC=cli/gif/$ext.gif BORDER=0 ALIGN=ABSMIDDLE>";
	}else{
		$icon="<IMG SRC=cli/gif/file.gif BORDER=0 ALIGN=ABSMIDDLE>";
	}

	if(preg_match("/\.(gif|png|jpe?g|bmp)$/i",$filename)){
		//$size=getimagesize("../".$_SESSION["rep"]."/".$filename);
		//$html.="<HR><IMG SRC="."../".$_SESSION["rep"]."/".$filename." ALT=\"$filename\">";
		//<A HREF=\"".$_SESSION["rep"]."/$filename\" TARGET=new>
		$html.="<BR><IMG SRC=\""."./".$_SESSION["rep"]."/".$filename."\" BORDER=0>";
		$html.="<BR><BR><A HREF=\"./".$_SESSION["rep"]."/$filename\" TARGET=new>".$icon."&nbsp;$filename</A>";

	}elseif(preg_match("/\.(wma|mid|mp3|ogg)$/i",$filename)){//MUSIC

		$icon="<IMG SRC=cli/gif/mp3.gif BORDER=0 ALIGN=ABSMIDDLE>";
		$html.="<BR><EMBED SRC=\""."./".$_SESSION["rep"]."/".$filename."\" autostart=false hidden=false>";
		$html.="<BR><BR><A HREF=\"./".$_SESSION["rep"]."/$filename\" TARGET=new>".$icon."&nbsp;$filename</A>";

	}else{
		$html.="<BR><A HREF=\"./".$_SESSION["rep"]."/$filename\" TARGET=new>".$icon."&nbsp;$filename</A>";
	}
	return $html;
}

?>