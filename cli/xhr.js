var xhr = null;
if(window.XMLHttpRequest)		xhr = new XMLHttpRequest(); // Firefox
else if(window.ActiveXObject)	xhr = new ActiveXObject("Microsoft.XMLHTTP");// Internet Explorer
else {	alert("! XMLHTTPRequest...");}// XMLHttpRequest non support� par le navigateur

var xhrstatus=new Array();
xhrstatus[1]="loading";//d�but du transfert des donn�es
xhrstatus[2]="loaded";//donn�es transf�r�es
xhrstatus[3]="interactive";//les donn�es re�ues sont accssibles en partie
xhrstatus[4]="complete";//ok