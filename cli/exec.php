<?php
// EXEC : DO THE COMMAND

if (!$_SESSION["rep"]) {
    $_SESSION["rep"]="./";//repertoire courant
}

if (!$_SESSION["f"]) {
    $_SESSION["f"]=array("./");//files (nauto complete)
}

if (!$_SESSION["css"]) {
    $_SESSION["css"]=$style[0];
}

if (!$_SESSION["history"]) {
    $_SESSION["history"]=array();
}

$rep=$_SESSION["rep"];

include_once "f_command.php";
include_once "style.php";
include_once "cmds.inc";//liste des commandes

///////////////////////////////////////////////////////////////
if (@$_POST["c"]) {//Parse la ligne de commande
    $c=trim($_POST["c"]);
    $c=str_replace("  ", " ", "$c");
    $c=explode(" ", "$c");//sépare les arguments
    $com=strtolower($c["0"]);
    for ($i=1; $i<count($c); $i++) {//parcoure les arguments
        if (preg_match("/^-[\w]+/i", $c["$i"])) {//  -qqchose (options)
            $opts[]=$c["$i"];//options
            //echo "option:$c[$i]\n";
        } else {//Args
            $args[]=$c["$i"];//Args
        }
    }

    if (preg_match("/^([-]+he?l?p?|\?)$/i", @$arg)) {
        man($com);
    } elseif (array_key_exists("$com", $cmd)) {
        
        eval("\$pipe=".$cmd["$com"]);

        $c=implode(" ", $c);
        if (count($_SESSION['history'])>10) {
            array_shift($_SESSION['history']);
        }
        if (!in_array($c, $_SESSION['history'])) {
            $_SESSION['history'][]=$c;
        }
        $_SESSION["error"]=0;
        $PIPE="$pipe";// !!!
    } else {
        $_SESSION["error"]++;
        if ($_SESSION["error"]>5) {
            $_SESSION["info"]="type 'help' for some help.\n\n";
            $_SESSION["error"]=0;
        }
        $_SESSION["info"].="\n-bash: $com : command not found";
    }
}

if (!$rep) {
    $rep="./";
}
