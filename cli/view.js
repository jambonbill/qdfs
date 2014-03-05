
document.onkeyup = k;//keyboard

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

var cur;//current pic

function v(id){//view;
	cur=id;
	//alert('load \"'+fn+"\"");
	document.getElementById("pix").style.display="inline";
	document.getElementById("gallery").style.display="none";

	document.getElementById("pix").innerHTML="<DIV ID=MAIN><CENTER>Loading "+pixs[id]+" ...</DIV>";
	//document.getElementById("gal").style.display="none";


	z=1;//reset zoom factor;

	xhr.open("GET", "view.php?fn="+pixs[id], true);
	xhr.onreadystatechange = function() {
		window.status=xhrstatus[xhr.readyState];
		if(xhr.readyState == 4){	
			//alert(xhr.responseText);
			eval(xhr.responseText);
		}
	}
	xhr.send(null);
	//document.location.href="?of="+fn;
}

function gal(){//Back to gallery
	//document.getElementById("gal").style.display="inline";
	document.getElementById("gallery").style.display="inline";
	document.getElementById("pix").style.display="none";
}


function k(e){	//ON KEY UP !!!!
	var e;
	var n;
	n=getn(e);

	//window.status="key : "+n;
	if(n==13){gal();}//ENTREE

	if(n==27){// Key "ESC"
		cancel();
	}

	if(n==37||n==33){prv();}//Arrow Keys
	if(n==39||n==34){nxt();}//Arrow Keys
	if(n==46 && ir==true){del()} //REM
	if(n==113 && ir==true)rename();//F2 / RENAME
	if(n==107)zplus();//zoom plus
	if(n==109)zmoins();//zoom moins
}



function rename(){
   if(!ir)return false;
   if(nnam=prompt("Set new name for \""+fn+"\"",fn)){
       //alert(nnam);
       document.form.c.value="rename "+fn+" "+nnam;
       document.form.submit();
   }
}

function del(){//DELETE PICTURE
   if(!ir)return false;
   if(confirm("delete "+fn+" ?")){
       document.form.c.value="rm "+fn;
       document.form.submit();
   }

} 

var pw;//img width
var ph;//img height

function setzoom(z){
	detect(image_1);
	window.status=pw+"/"+ph;
//	image_1.width=320;
}

var z=1;//zoom factor;
function zplus(){z++;
	document.getElementById("img").style.width=w*z;
	document.getElementById("img").style.height=h*z;
}

function zmoins(){
	if(z>1){z--;
		document.getElementById("img").style.width=w*z;
		document.getElementById("img").style.height=h*z;
	}
}

function cancel(){
	document.location.href="index.php";
}

function nxt(){
	window.status="next";cur++;
	if(cur>pixs.length-1)cur=0;
	v(cur);//view
}

function prv(){
	window.status="prev";cur--;
	if(cur<0)cur=pixs.length-1;
	v(cur);//view
}
