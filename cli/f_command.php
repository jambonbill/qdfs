<?php
/**
 * QDFS Functions
 */


/**
 * Enter debug mode
 * @return [type] [description]
 */
function debug()
{
    if (@!$_SESSION["debug"]) {
        $_SESSION["debug"]=true;
        return "<font color='#00FF00'>debug mode on</font>";
    } else {
        $_SESSION["debug"]=false;
        return "<font color='#FF0000'>debug mode off</font>";
    }
}


//return CLI version
function ver()
{
    global $clipath;

    $ver ="QDFS 2014\n";
    $ver.='PHP version : ' . phpversion()."\n";

    //INFO
    $nfo ="Courtesy of Jambonbill";

    $ver.=$nfo;
    return $ver;
}


/**
 * Return current date
 * @return [type] [description]
 */
function datte()
{
    return date("D M j G:i:s T Y");
}





//ALIAS DE edit README.TXT
function edit_readme()
{
    $fn=$_SESSION["rep"]."/README.TXT";
    if (!is_file($fn)) {
        touch($fn);
    }
    $_SESSION["fn"]="README.TXT";
    echo "<script>document.location.href='cli/edit.php'</script>";
}

//get version
function getv($url)
{
    if ($v=@file($url)) {
        if (preg_match("/^Quick&Dirty File System/", $v[0])) {
            return $v;
        } else {
            die("Error, cant read version file : $url\n");
            return false;
        }
    } else {
        die("Error, cant read version file : $url\n");
        return false;
    }
}


//function cd($dir){
function cd()
{
    global $args;
    if (!$args[0]) {
        return "call : cd path \n";
    } else {
        $dir=$args[0];
    }
    
    $basedir=realpath("./");
    if ($dir=="/") {
        $_SESSION["rep"]=$basedir;
    }

    $cdir=realpath($_SESSION["rep"]);//current
    $ndir=realpath($_SESSION["rep"]."/".$dir);//newdir
    if (preg_match("/^\//", $dir)) {// cd /mod
        $ndir=realpath("./").$dir;
    }

//  return;
    if (@is_dir($ndir)) {
        $_SESSION["rep"]=".".str_replace($basedir, "", $ndir);
        $_SESSION["info"]="current dir : ".$_SESSION["rep"];
//      echo "Current dir : ".realpath("./")."/".$_SESSION["rep"];
        //if($rep)return "current directory : ".$rep;
    } else {
        return "Error : \"$dir\" is not a valid directory";
    }
}

/**
 * Return file extension
 * @param  [type] $fn [description]
 * @return [type]     [description]
 */
function ext($fn = '')
{
    $parts=explode(".", $fn);
    $ext=$parts[count($parts)-1];
    return $ext;
}


function echoo($str = '')
{
    $str=trim(str_replace("echo", "", $str));
//  if(!$str){echo "call : echo str";return false;}

    return "$str";
}


function mel()
{
    global $args;
    
    if (!$args[0]) {
        return "call : mail email_adress";
    } else {
        return "mail to $args[0]";
    }
}


// UPDATED 21/11/2005
function wget()
{

    global $args;
    if (!$args[0]) {
        return "call : wget [URL] [opt_name]";
    }

    $url=$args[0];
    $dest=$_SESSION["rep"]."/".basename($url);
    if (is_file($dest)) {
        return "Destination file $dest already exist. Aborting";
    }
    
    $newname=basename($url);
    if (@$args[1]) {
        $newname=$args[1];
    }

    if (@copy("$url", "$_SESSION[rep]/$newname")) {
        return "$url downloaded to ".$_SESSION["rep"]."/$newname";
    } else {
        return "Failed to retreive $url";
    }
}


/*
function maj()
{
    if ($_SESSION["remote"]) {
        return "not available w remote mode";
    }
    global $clipath;
    echo "<script>document.location.href='$clipath/install.php';</script>";
}
*/

//retourne les fichiers correspondant       x//dir ; ls ; rm ...
function grab($arg = '')
{
    $reg=null;
    if ($arg) {
        $arg=str_replace("*", "(.*)", $arg);
        $arg=str_replace("?", "[\d\w]", $arg);
        $arg=str_replace("/", "\/", $arg);
//      $arg=addslashes($arg);
        $reg="/^$arg$/i";
//      echo "<li>$reg";
    }

    $out=array();
    $_SESSION['f']=array();//autocomplete
    $handle  = opendir($_SESSION["rep"]);
    //echo "<li>".$_SESSION["rep"]."\n";
    while ($file = @readdir($handle)) {//Browse
        
        if ($reg && !preg_match($reg, $file)) {
            continue;
        }
        
        if (@!$_SESSION['isroot'] && preg_match("/.*.php[\d]?[~]?$/i", $file)) {
            continue;
        }

        if (@is_dir($_SESSION["rep"]."/".$file)) {
            
            if ($file=="." || $file=="..") {
                continue;
            }

            if (preg_match("/^inc$/i", "$file") && preg_match("/^.[\/]?$/", "$_SESSION[rep]")) {//rep include
                //echo "<li>skip ".$_SESSION["rep"]." $file :\n";
                continue;
            }
            $out["dir"][]=$file;
            $_SESSION["f"][]=$file;
        } else {//le fichier
            $out['file'][]=$file;
            $_SESSION["f"][]=$file;
        }
    }
    
    if (count(@$out['dir'])) {
        sort($out['dir']);
    }
    
    if (count(@$out['file'])) {
        sort($out['file']);
    }

    closedir($handle);
    return $out;
}

//$arg genre *.mp3  // dir // ll
function ls($mode = '')
{
    global $args;
    $filter=$args[0];
//  if(!$arg){$arg="ls";}

    $filz = grab($filter);
    $out='';
    $totalsize=0;
    $f=0;

    switch($mode)
    {
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        case "-l":  //ls -l ou ll
            //echo "ls -l ou ll\n";
            ////correction dir
            for ($i=0; $i<count($filz["dir"]); $i++) {
                $filz["dir"][$i]=$filz["dir"][$i]."/";
            }
            
            $tree=[];
            if (is_array(@$filz["file"])) {
                $tree = array_merge($filz["dir"], @$filz["file"]);
            }

            sort($tree);//tri
            
            for ($i=0; $i<count($tree); $i++) {
                
                $fsize=@filesize($_SESSION["rep"]."/".$tree[$i]);//taille
                
                $out.=perms($tree[$i])." ";//permissions

                $out.=sprintf("%04d", owner($_SESSION["rep"]."/".$tree[$i]));

                //$out.=sprintf("% 8s",printsize($fsize))." ";
                $out.=sprintf("% 10s", $fsize)." ";
                $modtime= date("M d H:i", @filemtime($_SESSION["rep"]."/".$filz["file"][$i]));//date de modif
                $out.="$modtime ";
                $out.=$tree[$i]."\n";
            }
            break;////////////////////////////////////////////////////////////////////////////////////////////////



        case "dir":// dir
            //les repertoires :
            for ($i=0; $i<count($filz["dir"]); $i++) {
                $str=$filz["dir"][$i];
                $str=substr($str, 0, 30);
                $str= sprintf("% -35s", $str);
                if ($i==0) {
                } else {

                    $out.="$str &lt;DIR&gt;<br>";
    //              $_SESSION['f'][$f]=$folder[$i];
                    $f++;
                }
            }

            //les fichiers :
            for ($i=0; $i<count($filz["file"]); $i++) {
                $ext=ext($filz["file"][$i]);

                $f++;

                $sz=@filesize($_SESSION["rep"]."/".$filz["file"][$i]);
                $totalsize+=$sz;
                if ($sz<1) {
                    $sz=1;
                }
                $filesize=sprintf("% 8s", printsize($sz));
                $str=$filz["file"][$i];
                $str=substr($str, 0, 30);
                $str= sprintf("% -32s", $str);
                $out.="$str $filesize<br>";
            }
            $nfi=count($filz["file"]);
            $nfo=count($filz["dir"]);
            $out.="            $nfi file(s) ".printsize($totalsize)."<br>";
            $out.="            $nfo folder(s)<br>";
            break;////////////////////////////////////////////////////////////////////////////////////////////////

        default://ls tout seul //affichage colonne
            //echo "default:";    
            for ($i=0; $i<count($filz["dir"]); $i++) {
                $filz["dir"][$i]=$filz["dir"][$i]."/";
            }
            
            $tree=array_merge($filz["dir"], $filz["file"]);
            sort($tree);//tri
            $nbrow=round(count($tree)/4);//4 colonnes
            for ($i=0; $i<$nbrow; $i++) {
                $out.=sprintf("% -18s", substr($tree[$i], 0, 18))." ";
                $out.=sprintf("% -18s", substr($tree[$i+($nbrow)], 0, 18))." ";
                $out.=sprintf("% -18s", substr($tree[$i+($nbrow*2)], 0, 18))." ";
                $out.=sprintf("% -18s", substr($tree[$i+($nbrow*3)], 0, 18));
                $out.="<br>";
            }
            //$out.="<li>$nbrow";
            break;
    }

    return $out;
}


function printsize($b = 0)
{
    $o=$b."b";//return $o
    if ($b>=1024) {
        $o=round($b/1024)."Ko";
    }
    if ($b>=1048576) {
        $o=round($b/(1048576))."Mo";
    }
    if ($b>=1073741824) {
        $o=round($m/1073741824)."Go";
    }
    return $o;
}


/**
 * Create a folder
 * @return [type] [description]
 */
function makedir()
{
    global $args;
    if (!$args[0]) {
        return "call : mkdir [foldername]";
    } else {
        $folder=$args[0];
    }
    
    if (@is_dir($_SESSION["rep"]."/$folder")) {
        return "folder $folder already exist";
    }
    
    if (mkdir($_SESSION["rep"]."/$folder", 0777)) {
        return "Folder '$_SESSION[rep]/$folder' created";
    } else {
        return "Error creating folder '$rep/$folder'";
    }
}


///deplacer un fichier ou un repertoire
function movefile()
{
    global $args;
    if (!$args[0] || !$args[1]) {
        return "call : mv file(s) path";
    }

    $filz=grab($args[0]);
    $path=$args[1];
    if (!@is_dir($_SESSION["rep"]."/".$path)) {
        return "$_SESSION[rep]$path is not a valid directory";
    }

    for ($i=0; $i<count($filz['file']); $i++) {
        $f=$filz['file'][$i];
        
        if (!is_file($_SESSION["rep"]."/".$f)) {
            return "$f : no such file or directory";
        }

        if (rename($f, $_SESSION["rep"]."/$path/$f")) {
            $out.="$f moved to ".$_SESSION["rep"]."/$path<br>";
        } else {
            $out.="error moving $f to ".$_SESSION["rep"]."/$path<br>";
        }

    }
    return $out;

}


//rename
function ren()
{
    global $args;
    
    if (!$args[0]||!$args[1]) {
        return "call : rename filename newfilename<br>";
    }

    if (rename($_SESSION["rep"]."/".$args[0], $_SESSION["rep"]."/".$args[1])) {
        return $args[0]." renamed to ".$args[1];
    } else {
        return "Error renaming ".$args[0]." to ".$args[1];
    }
}


/**
 * Delete file(s)
 * @return [type] [description]
 */
function remove()
{
    global $args;
    
    if (!$args[0]) {
        return "call : rm filename<br>";
    } else {
        $arg=$args[0];
    }
    
    $filz=grab($arg);
    
    $out='';
    
    for ($i=0; $i<count($filz['file']); $i++) {
        
        $f=$filz['file'][$i];
        
        if (!is_file($_SESSION["rep"]."/$f")) {
            return "error : ".$_SESSION["rep"]."/$f do not exist";
        }

        if (unlink($_SESSION["rep"]."/$f")) {
            $out.="'$f' removed successfully<br>";
            //$_SESSION["info"]="$out";
        } else {
            $out.="error : cant remove '$f'<br>";
        }
    }
    return $out;
}


function upload()
{
    
    //le fichier se copie dans config.php
    echo "<form name=file method='POST' enctype='multipart/form-data'>";
    echo "Upload file to ".$_SESSION["rep"]."\n";
    echo "<input type=file name=uf style='font-family:Fixedsys,System,Terminal,Courier;'><br>";
    echo "\n<input type=submit value='[Click here to upload]'>";
    echo "</form>";

    exit;
    //BUG CHIANT A CAUSE DU FOCUS JAVASCRIPT ...
}



//supprimer un dossier
function remdir()
{
    global $args;
    if (!$args[0]) {
        return "call : rmdir foldername<br>";
    } else {
        $d=$args[0];
    }

    if (!@is_dir($_SESSION["rep"]."/$d")) {
        return "$d is not a directory";
    }
    
    if (rmdir($_SESSION["rep"]."/$d")) {
        return "Directory '$_SESSION[rep]/$d' deleted'<br>";
    } else {
        return "Error : Cant delete '$d' directory'<br>(maybe it's not empty)";
    }
}


function pwd()
{
    global $rep;
    if (!@realpath($rep)) {
        return "pwd disabled for security reason<br>path : $rep";
    } else {
        $srep=preg_replace("/^\./", "", $_SESSION["rep"]);//RELATIVE PATH
        $str ="RELATIVE : $srep\n";
        $rp=realpath("./$srep"); //ABSOLUTE PATH
        $str.="ABSOLUTE : $rp\n";
        return $str;
    }
}

function evale($arg)
{
    if (!$arg) {
        return "call : eval php";
    }
    eval("$arg");
}


function clean($url)
{
    $nurl=null;
    $url=explode("/", $url);
    $i=0;

    while ($i<count($url)) {
        if ($url[$i]=="..") {
            if (is_array($nurl)) {
                array_pop($nurl);
            }
        } elseif ($url[$i]=="" || $url[$i]==".") {
            //
        } else {
            $nurl[]=$url[$i];
        }
        $i++;
    }
    
    if (is_array($nurl)) {
        $nurl=implode("/", $nurl);
    }
    
    $nurl=str_replace("//", "/", $nurl);
    return $nurl;
}

function help()
{
    global $cmd;
    $k=0;
    $out="Commands :";
    $out.="\n----------------------------------------------\n";
    foreach ($cmd as $key => $value) {
        $out.= sprintf("% -10s", strtoupper($key));
        $k++;
        if ($k>=5) {
            $out.="\n";
            $k=0;
        }
    }
    $out.="\n----------------------------------------------\n";
    $out.="Get support @";
    $out.="<a href=http://fr.groups.yahoo.com/group/qdfs/ target=_blank>http://fr.groups.yahoo.com/group/qdfs/</a>";
    return $out;
}


function locat()
{
    global $args;
    global $rep;
    
    if (!$args[0]) {
        return "call : locate [name]";
    } else {
        $str=$args[0];
    }
    include "locate.php";
    
    if (!$rep) {
        $rep="./";
    }

    $out=locate($str);
    return $out;
}

function mane($arg)
{//un alias  pour man /help
    return man($arg);
}

//Command line help !!
function man($command)
{
    if (!$command) {
        return "call : man [command]";
    }
    global $cmd,$fp;
    $command=strtolower($command);
    if (in_array("$command", $cmd)) {
        //echo "HELP/MAN : $command<br>";

        $fnm="$fp/inc/hlp/$command.txt";
        if (is_file("$fnm")) {
            $h=file($fnm);
            return implode("<br>", $h);
        } else {
//          $hf=fopen($fnm,"w+");
//          fclose($hf);
//          return "Please edit file '$hf'";
            return "no man file for [$command]";
        }
    } else {
        return "Error : '$command' is not a command";
    }
}


function style()
{
    global $args;
    global $style;

    $arg=$args[0];
    
    if (!$arg) {
        $out= "call : style [name]\n\n";
        $out.="Available styles : ".str_repeat("-", 41)."\n";
        $i=0;
        foreach ($style as $st) {
            $out.="[".sprintf("%-10s", $st)."]";
            $i++;
            
            if ($i>=5) {
                $out.="\n";
                $i=0;
            }
        }
    
        $out.="\n".str_repeat("-", 60)."\n";
        $st=explode(".", $_SESSION['css']);
        $out.="\nCurrent style : [".$st[0]."]";
        return $out;
    } else {
        if (in_array($arg, $style)) {

            $css=implode("", file("cli/css/$arg.css"));
            $_SESSION['info']="<font color=#00FF00>".nl2br( $css )."</font>";
            $_SESSION['css']="$arg.css";
            die("<script>document.location.href='?'</script>");
        } else {
            return "Error : $arg invalid style";
        }
    }
}

function chmode()
{
    global $args;
    if (!$args) {
        return "call : chmod /somedir/somefile 0755";
    }
}

function hta($log = '', $pass = '')
{//htaccess
    if (!$log||!$pass) {
        return "call : htaccess login password<br>Should create an activated htaccess in the current directory";
    }
    include "htaccess.php";
/*
    $fo=fopen("htaccess.txt","w+");
    fclose($fo);
    $fo=fopen("htpasswd.txt","w+");
    fclose($fo);
*/
}

function edit()
{
   
    global $args;
    
    $filename=$args[0];
    //Light edit v0
    //Save : Ctrl +s
    //Quit : Ctrl +q
    if (!$filename) {
        return "call : edit filename";
    }
    $_SESSION["fn"]=$filename;
    echo "<script>document.location.href='cli/edit.php'</script>";
}


function touche()
{//touch : update, sinon cree le fichier
    global $args;

    if (!$args[0]) {
        return "call : touch filename<br>Update filename modification time to now";
    } else {
        $fn=$args[0];
    }

    $fn=$_SESSION["rep"]."/$fn";
    
    if (touch($fn)) {
        return "$fn modification time has been changed.";
    } else {
        return "Sorry, Could not change modification time of $fn";
    }
}

/**
 * Copy
 * @return [type] [description]
 */
function cp()
{
    global $args;
    global $rep;
    $f=$args[0];//from
    $t=$args[1];//to

    if (!$f||!$t) {
        return "call : cp filefrom fileto<br>";
    }

    if (copy($_SESSION["rep"]."/$f", $_SESSION["rep"]."/$t")) {
        return "$f copied to $t";
    } else {
        return "Error : ";
    }
}

function exite()
{
    if ($_SESSION['isroot']==true) {
        unset($_SESSION['isroot']);
        $_SESSION['rootstr']="";
    } else {
        die("logout<script>parent.window.close();</script>");
    }
}

/**
 * [unzip description]
 * @param  string $f    [description]
 * @param  string $path [description]
 * @return [type]       [description]
 */
function unzip($f = '', $path = '')
{
    if (!$f) {
        return "call : unzip filename<br>";
    }

    $cas="extract";
    $filename=$f;
    if (!$path) {
        $dir=$_SESSION["rep"];
    } else {
        $dir=$path;
    }
//  include "pclzip.lib.php";
    include "unzip.php";
}

function get()
{
    global $args;
    $arg=$args[0];
    
    if (!$arg) {
        return "call : get file<br>Open file in a new window";
    }

    if (is_file($_SESSION["rep"]."/$arg")) {
        echo "<script>window.status='please wait...';";
        echo "window.open(\"inc/download.php?fn=".$_SESSION["rep"]."/$arg\",'pop','width=100,height=100,top=1600,left=1200');";
        echo "</script>";
    } else {
        return "Error '".$_SESSION["rep"]."/$arg' invalid filename";
    }
}

//nothing yet
function pdf($arg = '')
{
    global $rep;
}


function perms($fn)
{
    $perms = @fileperms($_SESSION["rep"]."/".$fn);
    if (($perms & 0xC000) == 0xC000)        $info = 's'; // Socket
    elseif (($perms & 0xA000) == 0xA000)    $info = 'l'; // Symbolic Link
    elseif (($perms & 0x8000) == 0x8000)    $info = '-'; // Regular
    elseif (($perms & 0x6000) == 0x6000)    $info = 'b'; // Block special
    elseif (($perms & 0x4000) == 0x4000)    $info = 'd'; // Directory
    elseif (($perms & 0x2000) == 0x2000)    $info = 'c'; // Character special
    elseif (($perms & 0x1000) == 0x1000)    $info = 'p'; // FIFO pipe
    else                                    $info = 'u'; // Unknown
    
    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

    return $info." 0";
}

function owner($fn)
{//retourne le propriétaire du fichier.
    return @fileowner($fn);
    //$unm=posix_getpwuid($ido);//marche pas sur free.fr
    //print_r($unm);
    //return $unm[name];
}

function df()
{
    include_once "totalsize.php";
    locate("*");
}

function more()
{
    global $args;

    if (!$args[0]) {
        return "call : more filename\ndisplay filename";
    } else {
        $fn=$args[0];
    }
    $fn=$_SESSION["rep"]."/$fn";
    
    if (!is_file($fn)) {
        return "error : !".basename($fn);
    }

    $f=file($fn);
    return htmlentities(implode("", $f));
}

/**
 * Display a login form, to allow sudoer login
 * @return [type] [description]
 */
function su()
{
    echo "<body topmargin=0 leftmargin=0 onload=document.f.pwd.focus() onclick=document.f.pwd.focus()>";
    echo "<form name=f method=post>";
    echo "Password: <input type=password name=pwd>";
    echo "</form>";
    exit;
}


function password()
{
    global $args;
    
    if (!$args[0]) {
        return "call : password mypassword";
    }

    $str=$args[0];
    return md5("$str");
}



function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


//X
function send()
{
    global $tos;
    global $startime;

    $lap=getmicrotime()-$startime;
    $lap=substr($lap, 0, 5);
    $tos['lap']=$lap;
    
    $array=$tos;
    //if(!$debug)header('Content-type: application/x-www-urlformencoded');
    while ($var=each($array)) {
        if ($level!="") {
            echo "_level".$level."/:";
        }
        echo "&".$var[key]."=";
        echo $var[value];
    }
    echo "&loaded=ok";
    exit;
    $tos=array();
    return $out;
}


function connect()
{
    global $args;
    if ($args[0]) {
        $out.="<A HREF=\"$args[0]\">$args[0]</A>\n\n";
        $fc=$args[0]."/connect.php";
//      $fc="http://qdfs.free.fr/cli/connect.php";
        @include "$fc";
        if ($VERSION) {
            $out.="<FONT COLOR=#00FF00>$VERSION</FONT>\n";
            $out.="REQUEST_METHOD\t$REQUEST_METHOD\n";
            $out.="SERVER_ADDR\t$SERVER_ADDR\n";
            $out.="SERVER_ADMIN\t$SERVER_ADMIN\n";
            $out.="SERVER_NAME\t$SERVER_NAME\n";
            $out.="SERVER_SOFTWARE\t$SERVER_SOFTWARE\n";
            $out.="SERVER_PORT\t$SERVER_PORT\n";
            $out.="HTTP_ACCEPT_LANGUAGE\t$HTTP_ACCEPT_LANGUAGE\n";
            $out.="REQUEST_TIME\t$REQUEST_TIME\n"; //PHP5
            $out.="REMOTE_HOST\t$REMOTE_HOST\n";

        } else {
            $out.="Error : cant get $fc\n";
        }

/*
        if($con=@file($fc)){
            //
            $out.=implode("",$con);
        }else{
            //ERROR
            $out.="[<FONT COLOR=RED>Error</FONT>]\n";
            $out.="Cant read <A HREF=\"$fc\" TARGET=NEW>$fc</A>\n";
        }
*/
    } else {//NO ARGS
        $srvs="http://qdfs.free.fr/sources/servers.txt";
        if ($fc=file($srvs)) {
            $out.="call : connect [url]\n";
            $out.="---------------------------------------\n";
            foreach ($fc as $key => $value) {
                if (preg_match("/^(http:\/\/[\d\w_\/&\.-]+)/i", $value, $o)) {
                    $out.="<A HREF=\"$o[1]\" TARGET='NEW'>$o[1]</A>\n";
                }
            }
            $out.="---------------------------------------\n";
            //
        } else {
            return "error with $srvs\n";
            //
        }
    }
    return $out;
}

//generate .m3u //UPDATED 06/04/2005
function play()
{
    global $args;
    if (!$args[0]) {
        return "call : play musicfile(s) \n\nLoad/stream music via winamp style playlist (.m3u)\n";
    } else {//ok
        $mf=grab($args[0]);//music files
        
        if (count($mf["file"])==0) {
            return "0 file like $args[0]";
        }
        
        $dir=str_replace("\\", "/", $_SESSION["rep"]);
        $dir=preg_replace("/^.[\/]/", "", $dir);

        $href=preg_replace("/\?.*$/", "", $_SERVER["HTTP_REFERER"]);
        preg_match("/^(http:\/\/.*[\/])/i", $href, $o);
        $burl=$o[1];//clean basurl
        //echo "<li>$burl\n";//clean basurl

        $m3u=array();
        $m3u[]="#EXTM3U\n";//M3U HEADER
        $i=0;

        foreach ($mf["file"] as $file) {
            //echo $v;
            $i++;
            $m3u[]="#EXTINF:$i,$file";
            $m3u[]=str_replace(" ", "%20", $burl."$dir/$file");
        }

        $tmpfile="./tmp.m3u";
        $fo = fopen($tmpfile, "w+");//return "error : failed to create m3u";
        
        if ($fo) {
            fwrite($fo, implode("\n",$m3u));
            fclose($fo);
        } else {
            echo "Error opening $tmpfile\n";
        }
        
        $out=implode("\n", $m3u);
        
        //beurk
        $out.="\n<a href=$tmpfile>$tmpfile</a>";
        
        return "$out";
    }
}


function figlet()
{
    global $args;
    $flf=array();

    if (is_file("cli/phpfiglet_class.php")) {
        //echo "<LI>phpfiglet_class.php [OK]";
    } else {
        return "error : file not found : phpfiglet_class.php\n";
    }

    $handle  = opendir("./cli/flf");
    while ($file = @readdir($handle)){//Browse fonts
        if (preg_match("/\.flf$/i", "$file")) {
            $flf[]=$file;
        }
    }

    closedir($handle);
    
    if (!$args[0]) {

        foreach ($flf as $key => $value) {
            $fonts=true;
            $value=preg_replace("/\.flf$/i", "", $value);
            $out.="[".sprintf("%-10s", $value)."]";
            $p++;

            if ($p>=5) {
                $out.="\n";
                $p=0;
            }
        }
       
        if (!$fonts) {
            $out.="0 font found in /cli/flf\nCheck http://figlet.org to get some.\n\n";
        }
        $out.="\nFiglet font class, by Lucas Baltes (lucas@thebobo.com)\n";
        $out.="http://www.thebobo.com/\n";
        $out.="Copyright 2003 - Lucas Baltes\n";
        $out.="License GPL - http://www.gnu.org/licenses/gpl.html\n";
        $out.= "\ncall : figlet fontname\n";

        return $out;

    } else {//ok
        
        $fn=$args[0];
        
        if (!preg_match("/\.flf$/i", "$fn")) {
            //echo "<LI>Add ext '.flf'";
            $fn="$fn.flf";
        }
        
        if (!in_array($fn, $flf)) {
            return "error : font '$fn' not found";
        } else {
            $_SESSION["flf"]=$fn;
        }
    
        return "<script>if(a=prompt(\"Type string to magnify :\")){document.location.href='?str='+a;}</script>";
    }
}

function urlize()
{
    global $args;
    
    if (!$args[0]) {
        return "call : url address [filename]\ncreate internet shortcut file\n";
    } else {
        $address=$args[0];
    }
    
    if ($args[1]) {
        $fn=$_SESSION["rep"]."/".$args[1].".URL";
    } else {
        $fn=$_SESSION["rep"]."/".basename($address).".URL";
    }

    //$address=preg_replace("/^www/","http://www",$address);
    if (!preg_match("/^http:\/\//", $address)) {
        $address="http://$address";
    }
    
    $url ="[InternetShortcut]\n";
    $url.="URL=$address\n";

    if (!$fo=fopen("$fn", "w+")) {
        return "error: !fopen $fn";
    }

    fwrite($fo, $url);
    fclose($fo);
    return "Url saved to $fn";
}


function tar()
{
    global $args;
    if (!$args[0]) {
        return "call : tar filemask\n";
    }

    include "archive.php";
    if (!$args[1]) {
        $args[1]="tmp.tar";
    }

    $test = new tar_file($args[1]);
    $test->set_options(array('basedir'=>$_SESSION["rep"],'overwrite'=>1,'recurse'=>0,'level'=>1));
    $test->add_files($args[0]);
    $test->create_archive();
    return "archive saved to $_SESSION[rep]/$args[1]";
}


function zip()
{
    global $args;
    
    if (!$args[0]) {
        return "call : zip filemask\n";
    }

    /*--------------------------------------------------
    | TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.0
    | By Devin Doucette
    | Copyright (c) 2004 Devin Doucette
    | Email: darksnoopy@shaw.ca
    +--------------------------------------------------
    | Email bugs/suggestions to darksnoopy@shaw.ca
    +--------------------------------------------------
    | This script has been created and released under
    | the GNU GPL and is free to use and redistribute
    | only if this copyright statement is not removed
    +--------------------------------------------------*/

    include "archive.php";

    if (!$args[1]) {
        echo "arg1=".$args[1]."\n";
        $args[1]="tmp.zip";
    }
    
    $test = new zip_file($args[1]);
    $test->set_options(array('basedir'=>$_SESSION["rep"],'overwrite'=>1,'recurse'=>0,'level'=>1));
    $test->add_files($args[0]);
    $test->create_archive();
    
    echo "<li>$_SESSION[rep]\n";
    return "archive saved to $_SESSION[rep]/$args[1]";
}

//chrono
function gmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//Build file database
function pouet()
{
    echo "<li>updatedb";
    include "locate.php";
    $_SESSION["db"]=array();
    $t1=gmicrotime();
    $output=recurDir("./", array());
    $t2=gmicrotime()-$t1;
    echo "<li>updatedb";
    $f=fopen("cli/dbfs.txt", "w+");
    fwrite($f, implode("\n", $_SESSION["db"]));
    fclose($f);
    return "Generated in $t2 sec\n";
}


//browse et recopie des fichiers a distance :) (wget ammelioré)
function grabe()
{
    global $args,$opts;

    if (!$args[0]) {
        return "call : grab url filemask";
    }

    $url=$args[0];
    $filter=$args[1];

    if (!preg_match("/^http:\/\//", $url)) {
        $url="http://$url";
    }

    $out="GRAB <A HREF=$url><FONT COLOR=yellow>$url</FONT></A>\n\n";

    if (count($opts) && in_array("-v", $opts)) {
        $verbose=true;
    }

    if ($filter) {
        $filter=str_replace(".", "\.", $filter);
        $filter=str_replace("*", ".*", $filter);
        $filter=str_replace("[", "\[", $filter);
        $filter=str_replace("]", "\]", $filter);
        $filter=str_replace("(", "\(", $filter);
        $filter=str_replace(")", "\)", $filter);
        //echo $filter;return;
        $filter="/^$filter\$/i";
    } else {
        $filter="/.*/";
    }

    preg_match("/(http\:\/\/[\d\w\._-]+)/i", $url, $o);
    $BASEURL=$o[1];//echo "BASEURL=$BASEURL\n";
    $url=explode("?", $url);
    $url=$url[0];//Trash url args !
    $surl=explode("/", $url);
    $ext=$surl[count($surl)-1];
    
    if (preg_match("/\.(php[\d]?|htm[l]|js|txt)$/i", $ext)) {
        array_pop($surl);
    }

    $path=implode("/", $surl);//echo "Path=$path\n";

    if ($txt=@file($url)) {
        $txt=implode("", $txt);
        $html=htmlentities($txt);

        // LIENS HREF - Chargement
        $rgx="/\b(A HREF)=[\"']?([\:\/\d\w\?=@%._ ~&-]+)[\"']?/i";
        
        preg_match_all($rgx, $txt, $href);
        $href[2]=array_unique($href[2]);
        
        // EMBEDED FILES -Chargement
        $rgx="/\b(SRC)=[\"']?([\:\/\d\w@%._ ~&-]+)[\"']?/i";

        preg_match_all($rgx, $txt, $src);
        $src[2]=array_unique($src[2]);

        if (count($href[2]) && $verbose) {
            $out.= count($href[2])." distinct HREF files.\n";
        }

        if (count($src[2]) && $verbose) {
            $out.= count($src[2])." distinct EMBEDDED files.\n\n";
        }

        $fil=array();//Output file list
        foreach ($href[2] as $fn) {
            $bn=basename($fn);
            if (!preg_match("/\./", $bn)) {
                continue;//Zap si le fichier n'a pas d'extension !
            }
            if (preg_match("/(mailto\:)/", $bn)) {
                continue;//Zap si c'est un lien mail
            }

            $bp=str_replace($bn, "", $fn);//basepath

            //Exclusions : php(n) , htm(l), cgi (pour les href !!!)
            if (preg_match("/\.(asp|php[\d]?|cfm|jsp|cgi|net|com|org|fr|ch|be|ru|us)\b/i", $fn)) {
                if ($verbose) {
                    $out.="[<font color=red>SKIP</font>] $bn\n";
                    continue;
                }
            }

            if ($verbose) {
                $out.="href=$fn\n";
            }

            if (preg_match("/^\//i", $fn)) {
                $fil[]=$BASEURL.$fn;
            } elseif (preg_match("/^http\:/i", $fn)) {
                $fil[]=$fn;
            } else {
                $fil[]="$path/$bp/$bn";
            }
        }
        

        foreach ($src[2] as $fn) {
            $bn=basename($fn);
            if (!preg_match("/\./", $bn)) {
                continue;//Zap si le fichier n'a pas d'extension !
            }
            // basepath
            $bp=str_replace($bn, "", $fn);

            if ($verbose) {
                $out.="SRC=$fn\n";
            }

            if ($fn && $bn) {
                if (preg_match("/^\//", $fn)) {
                    $fil[]=$BASEURL.$fn;
                } elseif (preg_match("/^http\:/i", $fn)) {
                    $fil[]=$fn;
                } else {
                    $fil[]="$path/$fn";
                }
            }
        }
        
        //Comptage et copie des fichiers
        foreach ($fil as $f) {
            
            //filtre
            if (!preg_match($filter, $f)) {
                continue;
            }

            $ext=strtolower(ext($f));
            $dext["$ext"]++;//Comptage des extensions ! ;)
            $f=str_replace(" ", "%20", $f);
            if (!$args[1]) {
                continue;//ne copie pas de fichiers si pas de filemask ;)
            }

            $f2=str_replace("%20", " ", $f);
            
            if (!is_file($_SESSION["rep"]."/".basename($f2))) {
                copy($f, $_SESSION["rep"]."/".basename($f2));
                $out.= sprintf("%-32s", $f2)." copied";
            } else {
                $out.= sprintf("%-32s", basename($f2))." allready exist";
            }
            $out.= "\n";
        }

        if (count($dext)) {
            $out.="\nDistinct files found :\n";
            foreach ($dext as $k => $v) {
                $out.="$v * $k\n";
            }
            $out.=str_repeat("-", 32)."\n";
        } else {
            $out.="No file found width $args[1]\n";
        }
        return $out;
    } else {
        return "error : cant get $args[0]";
    }
}
