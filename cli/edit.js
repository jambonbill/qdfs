window.status="edit";
document.onkeyup = k;
document.onkeydown = k2;
document.body.scroll="NO";

var ctrol=false;//true si en mode "command"
var fn;//filename;

function init(){// (ONLOAD)
	//alert("init() "+document.f.fn.value);
	document.f.title.value=document.f.fn.value;
}

function refocus(){//Remet le focus correctement dans le textarea
	ctrol=false;
	document.f.txt.className="";//normal
	document.f.title.value=fn;
	window.status=fn;
	document.f.txt.focus();
	return false;
}


function ctrl(){
	ctrol=true;
	document.f.txt.className="i";
	var hlp="";
	document.f.title.value=hlp;
	document.f.title.focus();
//	window.status=hlp+" Q , S , N , H";
}



function getn(e){//retourne la touche
	var n;
//	var e;
    if (document.all){ //ie 
		e = window.event;
		n = e.keyCode;
	}else if (document.layers) { //"moz"
		n = e.which; 
	}else{// ?
		n = e.which; 
	}
	return n;
}

function k(e){	//ON KEY UP !!!!
	var e;
	var n;
	n=getn(e);


	if(n==27 && ctrol==true){
		e=false;
		return false;
	}

	//window.status="key : "+n;
	//if(n==13){go();}//ENTREE

	if(n==27){// Key "ESC"
		ctrl();
		window.status="ESC : ";
	}

	return false;
}


function k2(e){	//ON KEY DOWN
	var e;
	var n;
	n=getn(e);

//	if(n==191){}// Key ":"

	if(ctrol==true){ // !!!

		if(n==27)return;//re-escape
		document.title="CTRL : "+n;
		window.status="CTRL : "+n;
		if(n==70){ // "FIND"
			if(f=prompt("find what ?","searchstr")){
				alert("find("+f+")");
			}else{
				//
				//e=false;
				//ctrol=false;
				//refocus();	
			}
			e=false;
			ctrol=false;
			refocus();				
		}
		if(n==72){ // "H" - REPLACE
			replace();
		}

		else if(n==77){//"M"
			mailto();
		}

		else if(n==78){
			if(confirm("new")){
				newtxt();
				refocus();
			}else{
				e=false;
				ctrol=false;
				refocus();
			}
		} // "N"
		else if(n==79){// "O"pen
			alert("open");
			//document.location.href="editor.php?open=1";
			document.f.action.value="open";
			document.f.submit();
		} 
		else if(n==81){//Q QUIT
			quit();
		}

		else if(n==83){// "S" SAVE
			if(confirm("Save file "+fn+" ?")){
				//alert("youpi");
				document.f.action.value="save";
				document.f.submit();
				this.event.returnValue=false;
			}else{
				abort();
			}
		}else{
			e=false;
			ctrol=false;//libère le ctrol
			window.status="Unknow command : "+n;
			refocus();
			return false;
		}
	}else{
		//normal
	}
}


function mailto(){
	email=document.f.mail.value;
	if(!email)email="bill@parishq.net";
	if(addr=prompt("Mail to : ",email)){
		document.f.mail.value=addr;
		document.f.action.value="mailto";
		post();
	}
}


function newtxt(){
	if(!confirm("Create new file ?"))return;
	window.status="New";
	document.f.fn.value="";
	document.f.txt.value="";
	cancel();
	init();
}

function save(){
	if(document.f.fn.value==""){saveas();return;}
	document.f.action.value="save";
	post();
}

var nrep="";
function browse(nrep){
	if(nrep){
		//alert("nrep="+nrep);
	}else if(nrep=="undefined"){
		nrep="";
	}
	document.f.action.value="browse";
	document.getElementById("brs").style.display="inline";
	document.getElementById("txt").style.display="none";
	document.getElementById("brs").innerHTML="Loading...";
	document.f.title.value=document.f.rep.value;
	xhr.open("POST", "edit.php?nrep="+nrep, true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
	data="&action="+document.f.action.value;
	xhr.onreadystatechange = function() {
		window.status=xhrstatus[xhr.readyState];
		if(xhr.readyState == 4){	
			document.getElementById("brs").innerHTML=xhr.responseText+"<A HREF=# ONCLICK=cancel()>----------------[Cancel]-----------------</A>";
		}
	}
	xhr.send(data);
}

function cancel(){
	init();
	document.getElementById("brs").style.display="none";
	document.getElementById("txt").style.display="inline";
}

function opn(fname){
	init();
	document.f.action.value="open";
	document.f.fn.value=fname;
	//document.f.txt.value=fname;

	data="&fn="+document.f.fn.value;
	data+="&action="+document.f.action.value;


	//xhr.open("GET", "edit.php?"+data, true);
	xhr.open("POST", "edit.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8"); 
	//xhr.setRequestHeader("Content-type",  "text/html; charset=iso-8859-1");
	//data+="&fn="+document.f.fn.value;
	//data+="&action="+document.f.action.value;
	xhr.onreadystatechange = function() {
		window.status=xhrstatus[xhr.readyState];
		if(xhr.readyState == 4){
			//alert(xhr.responseText);
			document.getElementById("txt").style.display="inline";
			document.f.txt.value=xhr.responseText;
			document.getElementById("brs").style.display="none";
			init();
		}
	}
	xhr.send(data);

}

function saveas(){
	if(savename=prompt("Save As :",document.f.fn.value)){
		document.f.fn.value=savename;
		save();
		//post();
	}
}

function post(){
	//alert("post();");
	xhr.open("POST", "edit.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
	data="&txt="+escape(document.f.txt.value);
	data+="&fn="+document.f.fn.value;
	data+="&mail="+document.f.mail.value;
	data+="&action="+document.f.action.value;
	xhr.onreadystatechange = function() {
		window.status=xhrstatus[xhr.readyState];
		if(xhr.readyState == 4){	
			//alert(xhr.responseText);
			eval(xhr.responseText);
			init();
		}
	}
	xhr.send(data);
}


function abort(){//ABANDON (commande non conforme)
	e=false;
	ctrol=false;//libère le ctrol
	refocus();
	return false;
}

var a="string";
var b="string";
function replace(){
	if(a=prompt("Replace (regular expression) :",a)){
		if(b=prompt("Replace "+a+" with :",b)){
			alert("replace(\""+a+"\" with \""+b+"\")");
			document.f.txt.value=document.f.txt.value.replace(eval("new RegExp(/"+a+"/g)"), b);
		}
	}
}


function quit(){if(confirm("Quit, are you sure ?"))document.location.href="./";}