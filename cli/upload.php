<?
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();
// QDFS FILE UPLOAD
?>
<TITLE>File Upload</TITLE>
<BODY STYLE="background-color:#D4D0C8"><PRE>
<?
if(!$_SESSION["isroot"])die("permission denied");
if(!$_FILES){
	echo "<FORM ENCTYPE='multipart/form-data' METHOD=POST>";
	echo "<INPUT TYPE=HIDDEN NAME=MAX_FILE_SIZE value=500000>";
	echo "Upload file<BR><input name=userfile type=file /><HR>";
	echo "<INPUT TYPE=submit VALUE='Upload this file' />";
	echo "</FORM>";
	exit();
}else{
	print_r($_FILES);
	$target="../$_SESSION[rep]/".$_FILES['userfile']['name'];
	if(move_uploaded_file($_FILES['userfile']['tmp_name'],$target))echo "<LI>upload ok : $target";
	if(is_file($target)){
		//echo "<LI>is file ok";
	}else{
		echo "<LI>error : file not found";
	}
	die("<SCRIPT>alert('File uploaded');window.close();</SCRIPT>");
}
?>
