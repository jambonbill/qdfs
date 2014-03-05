if(top.location != self.location){top.location = self.location;}

document.onkeyup = k;

//document.attachEvent('onkeydown',k);
//document.onkeydown('onkeydown',k);
//document.body.scroll="No";

var p=0;//pointeur (historique)
var n=0;
var rownum=20;
var dfi=document.form.i;//input

function go(){
//	document.form.c.value=document.form.i.value;
	document.form.c.value=dfi.value;
//	document.form.i.value="";
	dfi.value="";
	document.form.submit();
}

//AUTOCOMPLETION DES COMMANDES
//var cmpl;
//f=f.concat(cmpl);//ajoute les commandes a la completion
//window.status=f;
function cmp(){//complete
	window.status=f[0];//file[0]
	var dfiv=dfi.value;
	var inp=dfiv.split(" ");//copie de la commande
	il=inp.length;//nbre de morceaux
	piec=inp[inp.length-1];//la partie sur laquelle on cherche
	if(!piec)return;
	/*
	myString = new String(piec);
	var results = myString.search("/^"+piec+"/g");
	alert(results);
	*/
	var m=0;
	var got="";
	var lv="";
//	var mem="/ ";
	var mem= new Array();
	for(i=0;i<f.length;i++){
		strn=new String(f[i]);
		lv=strn.search(eval("/^"+piec+"/g"));
		if(lv==0){
//			alert(piec+" match "+f[i]+" : "+lv);
			got=f[i];
			mem[m]=f[i];
			m++;
		}
	}

	if(m==1){//ouai!!!! on l'a !

		rem=inp.pop();//ie error ?

		inpl=inp.length;
		//alert(inpl);
		inp[inpl]=got;

		dfi.value=inp.join(" ");

//		dfi.focus();
//		setTimeout("document.focus()", 1000);
		//document.getElementById("d").focus();
		locat();
		return false;
	}else if(m>1){//chaud les marrons
		recmp(mem);
	}
}

function recmp(mem){	//RECOMPLETE (SUPA COOL)
	var inp=dfi.value;
	window.status=mem.join(";");//Affiche les possibilitées
	inp=inp.split(" ");//copie de la commande
	ml=mem.length;
	t=0;//index diff
	//alert("memlen:"+ml);
	var test="";//str
	while(t<20){//nbr de chr a tester
		for(i=0;i<ml;i++){//test letters
			test=mem[i].substring(t,t+1);//fn
			if(i==0)var last=test;
			if(last!=test){//BREAK
				cmpstr=mem[i].substring(0,t);
				t=99;
				i=99;
				break;
			}
			last=test;//recopie le chr
		}
		t++;
	}
	inp.pop();
	inp[inp.length]=cmpstr;
	dfi.value=inp.join(" ");
	//alert("hop");
//	setTimeout("dfi.focus()", 100);
	//dfi.focus();
	return false;
}

function moz(){
	cmp();
}


function k(e){

	var n;
	var e;

	//	n=window.event.keyCode;

	if (document.all){ //ie 
		e = window.event;
		n = e.keyCode;
		window.status="ie";
	}else if (document.layers) { 
		n = e.which; 
		window.status="moz";
	}else{
		n = e.which; 
		window.status="?";
	}
	//window.status="k:"+n;
	
	var ctrl=false;
	var shift=false;

	
//	if(event)ctrl=event.ctrlKey;
//	if(event)shift=event.shiftKey;
/*
	if(n==13){//ENTREE
		go();
	}
*/
	if(n==9){//TAB
		cmp();//complete
		return false;
	}

	if(n==40){//Key Dwn, hist
		if(p<h.length-1){p++;}else{p=0;}
		dfi.value=h[p];
	}

	if(n==38){//Key Up, hist
		if(p>0){p--;}else{p=h.length-1;}
		dfi.value=h[p];
	}

	if(n==67 && ctrl==true){window.status="ctrl-C";}
	if(n==78 && ctrl==true){window.status="ctrl-N";}
	if(n==79 && ctrl==true){window.status="ctrl-O";}
	if(n==81 && ctrl==true){window.status="quit";}
	if(n==83 && ctrl==true){window.status="save";save();}
	dfi.focus();
	return false;
}

function locat(){
	document.form.i.focus();
	return false;
}

function rez(){
//	window.resizeTo(600,500);
}



///REGEX !!! :)
/*
myString = new String(test);
rExp = /clipboard/g;
var results = myString.search(rExp);
*/

