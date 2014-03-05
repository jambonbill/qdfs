var xhr = null;
if(window.XMLHttpRequest)		xhr = new XMLHttpRequest(); // Firefox
else if(window.ActiveXObject)	xhr = new ActiveXObject("Microsoft.XMLHTTP");// Internet Explorer
else {	alert("! XMLHTTPRequest...");}// XMLHttpRequest non supporté par le navigateur

var xhrstatus=new Array();
xhrstatus[1]="loading";//début du transfert des données
xhrstatus[2]="loaded";//données transférées
xhrstatus[3]="interactive";//les données reçues sont accssibles en partie
xhrstatus[4]="complete";//ok