<!-- Javascript Trim Member Functions --> 
<!-- http://www.somacon.com/p355.php -->

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}

<!-- DirectorioRCJA_Funciones.js --> 

DirectorioRCJA_busquedatmp = function (objeto, patron, e) {
	tecla = e.keyCode? e.keyCode : e.charCode;
	if (tecla == 27 || tecla == 8) {
		document.getElementById('DirectorioRCJA_' + objeto + '_busquedatmp').innerHTML="";
	}
	else {
		var patron = document.getElementById('DirectorioRCJA_' + objeto).value;
		document.getElementById('DirectorioRCJA_patron').innerHTML = patron;
		setTimeout('DirectorioRCJA_busca("' + objeto + '", "' + patron + '")', 500);
	}
}

DirectorioRCJA_busca = function (objeto, patron) {
	var apatron = document.getElementById('DirectorioRCJA_patron').innerHTML;
	if (patron == apatron) {
		new Ajax.Updater('DirectorioRCJA_' + objeto + '_busquedatmp','/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=Ficha' + objeto + '&submethod=printFicha&ajax=true&vista=preview&patron=' + apatron ,{evalScripts: 'yes'});
	}
}

SuperAjax.prototype.DirectorioRCJA_EjecutarWS = function () {	

	var div_sincronizacion = document.getElementById('sincronizacion');
	div_sincronizacion.style.display = "block";	
	document.getElementById("div_panel_informativo_feedback").innerHTML = 'Sincronizando con el Servicio Web de Directorio RCJA...';
	var loaded = false;
	var error = false;

	function startLoading() {
		loaded = false;
		showLoadingImage();
		// Nos conectamos al WS del Directorio RCJA
		var u = new Ajax.Updater('body', '/dispatcherGET.php?class=DirectorioRCJA&method=reentrada' +
										 '&subclass=SincronizacionDirectorioRCJA&submethod=sincronizar' +
										 '&vista=lista' +
										 "&patron=" +
										 '&ajax=true', 											 															 
		  {
			method: 'get',
			onSuccess: function(response) {
							respuesta = response.responseText;
//							console.log('Respuesta=' + respuesta);
							var indiceStringError = respuesta.indexOf("Error");
							// Examinamos la respuesta (objeto response) para ver si contiene el texto 'Error', y verificar así si ha concluido o no el proceso de Sincronización con el WS de Directorio RCJA
							if (indiceStringError > -1) {
								error = true;
								var finSubcadenaARecuperar = respuesta.indexOf("<br />");
								var mensajeErrorRecuperado = respuesta.substring(indiceStringError, finSubcadenaARecuperar);
								document.getElementById("div_panel_informativo_feedback").innerHTML = 'Ocurrió el Siguiente ERROR durante el proceso de Sincronización:<br> ' + mensajeErrorRecuperado;
								Element.hide('loading_box');
							}
							else {
								document.getElementById("div_panel_informativo_feedback").innerHTML = 'Sincronización finalizada';
							}	
					   },
			onFailure: function() { 
							alert('Ocurrió un error durante el proceso de sincronización con el Web Service de Directorio RCJA'); 
					   },
			onComplete: function() { 
							// Si no ha habido ningún error, esperamos casi un segundo y medio para que se vea durante algunos instantes el mensaje de "Sincronización finalizada", ya que primero se ejecuta la función de onSuccess() y posteriormente la de onComplete()
							// En caso contrario (se ha producido un algún error en el proceso de Sincronización), dejamos el error en la pantalla para que el usuario lo examine y pulse manualmente el botón "Salir" cuando haya terminado de examinar la información de error reportada
							if (!error) {
								setTimeout(stopLoading, 1200);
							}
						}	
		  }
		);												
	}

	function showLoadingImage() { 
		var el = document.getElementById("loading_box");
		if (el && !loaded) {
			el.innerHTML = '<img src="MODULOS/DirectorioRCJA/images/loading.gif" class="DirectorioRCJA_imagen_loading">';
			Element.show('loading_box');
		}
	} 
  
	function stopLoading() {
		Element.hide('loading_box');
		Element.hide('sincronizacion');	
		Element.show('inicio');	
		loaded = true;
	}
	
	startLoading();
}