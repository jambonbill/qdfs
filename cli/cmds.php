<?
//Liste des commandes disponibles
$cmd = Array();
$cmd["cd"]="cd();";		//Modifie le rep en cours
//$cmd["clear"]="clear";	//Null
$cmd["connect"]="connect();";//
$cmd["date"]="datte();";	//Date
$cmd["df"]="df();";		//DiskFree
$cmd["dir"]="ls('dir');";	//Dir
$cmd["echo"]="echoo()";		//Echo
$cmd["edit"]="edit();";		//Light edit
$cmd["exit"]="exite();";	//Exit
$cmd["explorer"]="explorer();";	//Explorer
$cmd["figlet"]="figlet();";	//Genere un figlet :)
$cmd["get"]="get();";		//Ouvre un fichier (popup)
$cmd["help"]="help();";		//Help
$cmd["ll"]="ls('-l');";		//LL
$cmd["ls"]="ls('');";		//LS
$cmd["locate"]="locat();";	//Locate
$cmd["man"]="mane()";		//MAN11
//$cmd["mail"]="";		//Send mail ?
$cmd["more"]="more();";		//Affiche le contenu d'un fichier (txt)
//$cmd["pdf"]="pdf();";		//PDF
$cmd["play"]="play();";		//Play (music)
$cmd["pwd"]="pwd();";		//PWD
$cmd["su"]="su();";		//Su
$cmd["style"]="style();";	//Style
$cmd["startx"]="startx();";	//StartX
$cmd["upload"]="upload();";	//Upload
$cmd["ver"]="ver();";		//Ver
$cmd["view"]="view();";		//Viewer

////////////////////////////////////////////////////////////////////////////////////

if($_SESSION["isroot"]){
	$cmd["isfile"]="isfile();";	//Isfile ?
	$cmd["chmod"]="chmode();";	//CHMOD
	$cmd["cp"]="cp();";			//Copy
	$cmd["debug"]="debug();";	//Debug mode
	$cmd["del"]="remove();";	//Alias de rm
	$cmd["delete"]="remove();";	//Alias de rm
	$cmd["deltree"]="remdir();";//Alias de rmdir
	
//	$cmd["eval"]="";		//Eval
//	$cmd["htaccess"]="";		//Cree htaccess+htpassword

	$cmd["install"]="maj();";	//MAJ !!!
	$cmd["maj"]="maj();";		//MAJ !!!
//	$cmd["mail"]="mel();";		//EMAIL
	$cmd["mkdir"]="makedir();";	//MKDIR
	$cmd["move"]="movefile();";	//Alias de mv
	$cmd["mv"]="movefile();";	//Alias de mv
	$cmd["password"]="password();";	//Generate md5 password
	$cmd["ren"]="ren();";		//RENAME
	$cmd["rename"]="ren();";	//RENAME
	$cmd["readme"]="edit_readme();";//README (alias pour edit README.TXT)
	$cmd["rm"]="remove();";		//REMOVE
	$cmd["rmdir"]="remdir();";	//REMOVE DIRECTORY
	$cmd["touch"]="touche();";	//Update access times of FILE to current time.
	$cmd["unzip"]="unzip();";	//Unzip

	$cmd["updatedb"]="pouet();";	//Update file database
	$cmd["url"]="urlize();";	//Urlizer !

	$cmd["wget"]="wget();";		//Wget URL :)
	$cmd["grab"]="grabe();";	//Grab URL :)

	$cmd["tar"]="tar();";		//archive tar
	$cmd["zip"]="zip();";		//archive zip
}
?>