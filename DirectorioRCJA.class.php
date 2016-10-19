<!-- DirectorioRCJA -->

<?

include_once PATH_MODULOS . "DirectorioRCJA/SincronizacionDirectorioRCJA.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/FichaUsuarioRCJA.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/FichaExtensionRCJA.class.php";
//include_once PATH_MODULOS . "DirectorioRCJA/UsuariosEstanEnTauNoEstanEnRCJA.class.php";


class DirectorioRCJA {
	var $name = __CLASS__;
	var $desc = "Directorio RCJA";
	
	function __construct() {
	}
	
	// Ponemos esta función aquí porque estamos usando el patrón Singleton implementado en la clase KERNEL\TAU\TAU.class.php, pero en TAU::desconectaDB() está comentado el "odbc_close($conn);", y es conveniente cerrar siempre la conexión.
	static public function desconexionDB($conn) {
		return odbc_close($conn);
	}
	
	private function includeJavaScript() {
		echo "<script type=\"text/javascript\" language=\"JavaScript\" src=\"/" . PATH_MODULOS ."{$this->name}/js/DirectorioRCJA_Funciones.js\"></script>\n";
	}
	
	function preview() {
 		$this->includeJavaScript();
		$this->menuPrincipalPreview();
	}
	
	function body() {
		Presentacion::titulo("DIRECTORIO RCJA");
		$this->includeJavaScript();
		$this->menuPrincipal();
	}
        
        function prueba($param) {
		Presentacion::titulo("mi módulo");
		$this->includeJavaScript();
                $sincronizar = new SincronizacionDirectorioRCJA();
		$sincronizar->prueba();
	}
	
	function reentrada($param) {
 //echo "<br>reentrada ";
 //print_r($param);
		$this->includeJavaScript();
		$m = new $param['subclass']();
		$m->{$param['submethod']}($param);
	}
	
	static public function matrizAutorizacion() {
		return array(
			array('body'),
			array('preview'),
			array('reentrada', 'FichaUsuarioRCJA', 'printFicha', 'preview'),
			array('reentrada', 'FichaExtensionRCJA', 'printFicha', 'preview'),
			array('reentrada', 'FichaUsuarioRCJA', 'body', 'nuevo'),
			array('reentrada', 'FichaExtensionRCJA', 'body', 'nuevo'),
			array('reentrada', 'FichaUsuarioRCJA', 'body', 'lista'),
			array('reentrada', 'FichaExtensionRCJA', 'body', 'lista')
		);
	}

	// Funcion de depuracion	
	function debug($param) {
		print_r($param);
	}
	
	public function Configuracion(){
		$conf = array ();
		$conf[] = array ('DirectorioRCJA-WS-Security-User', 'Credenciales WS: Usuario','WS-Security: Estas credenciales son necesarias para la autenticación (usuario)', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-Security-User", 0, "ceec.ws.directorio.rcja"));
		$conf[] = array ('DirectorioRCJA-WS-Security-Pwd', 'Credenciales WS: Contraseña','WS-Security: Estas credenciales son necesarias para la autenticación (contraseña)', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-Security-Pwd", 0, "Sandetel12"));
		$conf[] = array ('DirectorioRCJA-WS-URL-Servicio-ConsultarDatosLineaEmpleado', 'URL del WS ConsultarDatosLineaEmpleado','URL del WS de ConsultarDatosLineaEmpleado', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-ConsultarDatosLineaEmpleado", 0, "http://bus.esbsio.des.junta-andalucia.es/services/ConsultarDatosLineaEmpleado_DirRCJAv02?wsdl"));
		$conf[] = array ('DirectorioRCJA-WS-URL-Servicio-VincularEmpleadoATelefono', 'URL del WS VincularEmpleadoATelefono','URL del WS de VincularEmpleadoATelefono', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-VincularEmpleadoATelefono", 0, "https://bus.esbsio.des.junta-andalucia.es/services/VincularEmpleadoATelefono_DirRCJAv02?wsdl"));
		$conf[] = array ('DirectorioRCJA-WS-URL-Servicio-DesvincularEmpleadoDeTelefono', 'URL del WS DesvincularEmpleadoDeTelefono','URL del WS de DesvincularEmpleadoDeTelefono', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-DesvincularEmpleadoDeTelefono", 0, "https://bus.esbsio.des.junta-andalucia.es/services/DesvincularEmpleadoDeTelefono_DirRCJAv01?wsdl"));
		$conf[] = array ('DirectorioRCJA-WS-URL-Servicio-EstablecerDatosLinea', 'URL del WS EstablecerDatosLinea','URL del WS de EstablecerDatosLinea', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-EstablecerDatosLinea", 0, "https://bus.esbsio.des.junta-andalucia.es/services/EstablecerDatosLinea_DirRCJAv01?wsdl"));
		$conf[] = array ('DirectorioRCJA-WS-URL-Servicio-EstablecerDatosEmpleado', 'URL del WS EstablecerDatosEmpleado','URL del WS de EstablecerDatosEmpleado', Config::get(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-EstablecerDatosEmpleado", 0, "https://bus.esbsio.des.junta-andalucia.es/services/EstablecerDatosEmpleado_DirRCJAv02?wsdl"));
		return $conf;
	}
	
	public function Configurar($param){
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-Security-User", $param['DirectorioRCJA-WS-Security-User'], 0);
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-Security-Pwd", $param['DirectorioRCJA-WS-Security-Pwd'], 0);
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-ConsultarDatosLineaEmpleado", $param['DirectorioRCJA-WS-URL-Servicio-ConsultarDatosLineaEmpleado'], 0);
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-VincularEmpleadoATelefono", $param['DirectorioRCJA-WS-URL-Servicio-VincularEmpleadoATelefono'], 0);
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-DesvincularEmpleadoDeTelefono", $param['DirectorioRCJA-WS-URL-Servicio-DesvincularEmpleadoDeTelefono'], 0);
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-EstablecerDatosLinea", $param['DirectorioRCJA-WS-URL-Servicio-EstablecerDatosLinea'], 0);
		Config::put(Config::getModuleID("DirectorioRCJA"), "DirectorioRCJA-WS-URL-Servicio-EstablecerDatosEmpleado", $param['DirectorioRCJA-WS-URL-Servicio-EstablecerDatosEmpleado'], 0);
	}	
	
	function tabla_antes_2($titulo, $margen, $cerrar = "false", $id = ""){
		echo "<table style='background: #fafaff;border-width: 1px; border-color: grey;border-style: dashed'><tr style='background: #eeeeee'><td><img style='cursor: pointer' onclick='document.getElementById(\"{$id}\").innerHTML=\"\"' src='/MODULOS/DirectorioRCJA/images/close.png'></td><td>&nbsp;</td></tr>";
		echo "<tr><td>&nbsp;</td><td>";
	}

	function tabla_despues_2(){
		echo "</td></tr></table>";
	}
	
	function tabla_antes($titulo, $margen, $cerrar = "false", $id = ""){
	?>
	<!-- inicio tabla_antes <?= $titulo ?> -->
		<br>
	<table  border="0" align=letf style='margin-left: <?= $margen ?>' cellspacing="0" cellpadding="0">
                <tr> 
                 <td  width="6" align="left" valign="top"><img src="/MODULOS/DirectorioRCJA/images/roundc_ltcorner.gif" width="6" height="19"></td>
                 <td align="left" valign="top" background="/MODULOS/DirectorioRCJA/images/roundc_toptile.gif">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
                       <tr>
		        <? if ( $cerrar == "true" ){ ?>
			 <td width=1 onclick='document.getElementById("<?= $id ?>").innerHTML=""'><img width=19px src='/MODULOS/DirectorioRCJA/images/close.png'></td>
		          <?}else{ ?>
			<td width="1"><img src="/MODULOS/DirectorioRCJA/images/roundc_topheadbullet.gif" width="12" height="19"></td>
	      		 <?} ?>
                              
			      
		      <? if ($cerrar == "true") { ?>
	     
		      <? } 
				 else { ?>
				<td></td>
	      	  <?} ?>
		      <td class="DirectorioRCJA_fontBlackBold" style="padding-left:6px"><? echo $titulo ?></td>
                            </tr>
                          </table>
		  </td>
                  <td width="6" align="right" valign="top"><img src="/MODULOS/DirectorioRCJA/images/roundc_rtcorner.gif" width="6" height="19"></td>
	          </tr>
	          <tr>
		 <td align="left" valign="top" background="/MODULOS/DirectorioRCJA/images/roundc_lefttile.gif"><img src="/MODULOS/DirectorioRCJA/images/roundc_lefttile.gif" width="6" height="2"></td>
                        <td align="left" valign="top">
				<table cellSpacing=2 cellPadding=1 width="100%" border=0>
				 <tr vAlign=top>
					<td> 
		<!-- fin tabla_antes <?= $titulo ?>  -->
		<?
	}
	
	function tabla_despues(){
	?>
	<!-- inicio tabla_despues -->
	</td>
				</tr>
				</table>
			</td>
			<td align="right" valign="top" background="/MODULOS/DirectorioRCJA/images/roundc_righttile.gif"><img src="/MODULOS/DirectorioRCJA/images/roundc_righttile.gif" width="6" height="3"></td>
                      </tr>
		  
			<tr> 
                        <td align="left" valign="bottom"><img src="/MODULOS/DirectorioRCJA/images/roundc_lbcorner.gif" width="6" height="6"></td>
                        <td background="/MODULOS/DirectorioRCJA/images/roundc_bottomtile.gif"><img src="/MODULOS/DirectorioRCJA/images/roundc_bottomtile.gif" width="5" height="6"></td>

                        <td align="right" valign="bottom"><img src="/MODULOS/DirectorioRCJA/images/roundc_rbcorner.gif" width="6" height="6"></td>
                      </tr>
		    
	    </table>
	    <!-- fin tabla_despues -->
	<?
	}
	
	function menuPrincipal() {
		echo "<div id='inicio'>";
		self::tabla_antes("Acciones", "10%");
		?>
		<div id='DirectorioRCJA_patron' style='display: none'></div>
		<table style='background: white;'>
		<tr>
			<td>
			  <!--
			  <a href="/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=SincronizacionDirectorioRCJA&submethod=sincronizar&vista=lista&patron=">
				<img class='DirectorioRCJA_sin_borde' src="/MODULOS/DirectorioRCJA/images/sync.jpg" title='Sincronizar datos Usuarios y Extensiones WS Directorio RCJA'>
			  </a>
			  -->
			  <a style='cursor: pointer;' onclick='return SpAJAX.DirectorioRCJA_EjecutarWS();'>
				<img class='DirectorioRCJA_sin_borde' src="/MODULOS/DirectorioRCJA/images/sync.jpg" title='Sincronizar datos Usuarios y Extensiones WS Directorio RCJA'>
			  </a>
			</td>
			<td class="DirectorioRCJA_fontBlackBold">
				<!--
				<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=SincronizacionDirectorioRCJA&submethod=sincronizar&vista=lista&patron='>
				Sincronizar con WS DirectorioRCJA
				</a>
				-->
				<a style='text-decoration: none;color: black; cursor: pointer;' onclick='return SpAJAX.DirectorioRCJA_EjecutarWS();' title='Sincronizar datos Usuarios y Extensiones WS Directorio RCJA'>
				Sincronizar con WS DirectorioRCJA
				</a>
			</td>
		</tr>
		</table>
		
		<?
		self::tabla_despues();

		echo "<br>";
		self::tabla_antes("Consultas", "10%");
		?>
		
		<table style='background: white;'>
		<tr>
		<td>
		  <a href="/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=lista&patron=">
		  <img class='DirectorioRCJA_sin_borde' src="/MODULOS/DirectorioRCJA/images/users.png" title='Muestra todos los usuarios'></a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
				Usuarios
		</td>
		<td>
		  <img class='DirectorioRCJA_sin_borde' src="/MODULOS/DirectorioRCJA/images/look.png">
		</td>
		<td>
			<form method="get" action="/dispatcherGET.php" name="DatosUsuario">
				<input type="text" id='DirectorioRCJA_UsuarioRCJA' name="patron" value="" size="30" onkeyup="DirectorioRCJA_busquedatmp('UsuarioRCJA', this.value, event);" class="DirectorioRCJA_data"/>
				<input type="hidden" name="class" value="DirectorioRCJA" />
				<input type="hidden" name="method" value="reentrada" />
				<input type="hidden" name="subclass" value="FichaUsuarioRCJA" />
				<input type="hidden" name="submethod" value="body" />
				<input type="hidden" name="vista" value="lista" />
			</form>		
		</td>
		</tr>
		<tr><td></td><td></td><td></td><td colspan=3>	<table><span style='position: fixed' id='DirectorioRCJA_UsuarioRCJA_busquedatmp'></span></table></td></tr>	
		

		<tr>
		<td>
		  <a href="/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=lista&patron=">
		  <img title='Muestra todas las l&iacute;neas' class='DirectorioRCJA_sin_borde' src='/MODULOS/DirectorioRCJA/images/phone.png'/></a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
				Extensiones
		</td>
		<td>
		<img class='DirectorioRCJA_sin_borde' src='/MODULOS/DirectorioRCJA/images/look.png'/>
		</td>
		<td>
			<form method="get" action="/dispatcherGET.php" name="DatosExtension">
				<input type="text" id='DirectorioRCJA_ExtensionRCJA' name="patron" value="" size="30" onkeyup="DirectorioRCJA_busquedatmp('ExtensionRCJA', this.value, event);" class="DirectorioRCJA_data"/>
				<input type="hidden" name="class" value="DirectorioRCJA" />
				<input type="hidden" name="method" value="reentrada" />
				<input type="hidden" name="subclass" value="FichaExtensionRCJA" />
				<input type="hidden" name="submethod" value="body" />
				<input type="hidden" name="vista" value="lista" />
			</form>
			</td>
		</tr>
		<tr><td></td><td></td><td></td><td colspan=3>	<table><span style='position: fixed' id='DirectorioRCJA_ExtensionRCJA_busquedatmp'></span></table></td></tr>
		
		</table>

		<?
		self::tabla_despues();

		echo "<br>";
		self::tabla_antes("Asistentes", "10%");
		?>
		<table>
		
		<tr>
		<td>
		<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=UsuariosEstanEnTauNoEstanEnRCJA&submethod=body&vista=lista'>
			<img title='Listado de Usuarios existentes en Directorio RCJA que no est&aacute;n presentes en Inventario' style='cursor:pointer;border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/users.png' width=16px>
			</a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
			<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=UsuariosEstanEnTauNoEstanEnRCJA&submethod=body&vista=lista'>
			Listado de Usuarios existentes en Directorio RCJA que no est&aacute;n presentes en Inventario
			</a>
		</td>
		</tr>

		<tr>
		<td>
		<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			<img title='Listado de Usuarios existentes en Inventario que no est&aacute;n presentes en Directorio RCJA' style='cursor:pointer;border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/users.png' width=16px>
			</a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
			<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			Listado de Usuarios existentes en Inventario que no est&aacute;n presentes en Directorio RCJA
			</a>
		</td>
		</tr>
		
		<tr>
		<td>
		<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			<img title='Listado de Extensiones existentes en Directorio RCJA que no est&aacute;n presentes en Inventario' style='cursor:pointer;border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/phone.png' width=16px>
			</a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
			<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			Listado de Extensiones existentes en Directorio RCJA que no est&aacute;n presentes en Inventario
			</a>
		</td>
		</tr>

		<tr>
		<td>
		<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			<img title='Listado de Extensiones existentes en Inventario que no est&aacute;n presentes en Directorio RCJA' style='cursor:pointer;border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/phone.png' width=16px>
			</a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
			<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			Listado de Extensiones existentes en Inventario que no est&aacute;n presentes en Directorio RCJA
			</a>
		</td>
		</tr>
		
		<tr>
		<td>
		<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			<img title='Listado de Asociaciones existentes en Directorio RCJA que no est&aacute;n presentes en Inventario' style='cursor:pointer;border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/user-phone.png' width=16px>
			</a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
			<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			Listado de Asociaciones existentes en Directorio RCJA que no est&aacute;n presentes en Inventario
			</a>
		</td>
		</tr>

		<tr>
		<td>
		<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			<img title='Listado de Asociaciones existentes en Inventario que no est&aacute;n presentes en Directorio RCJA' style='cursor:pointer;border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/user-phone.png' width=16px>
			</a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
			<a style='text-decoration: none;color: black;' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaIP&submethod=body&vista=asistenterangoip'>
			Listado de Asociaciones existentes en Inventario que no est&aacute;n presentes en Directorio RCJA
			</a>
		</td>
		</tr>
		
		</table>
		<?
		self::tabla_despues();
		
		echo "</div>";
		echo "<div id='sincronizacion' class='DirectorioRCJA_div_modal_sincronizacion'>";
		
			echo "<section class='DirectorioRCJA_div_modal_sincronizacion_content'>";
			
			echo	"<form action=\"#\" method=\"post\">";
			echo		"<fieldset>";
			echo			"<legend>Proceso de sincronización con el WS de Directorio RCJA</legend>";
			echo			"<div id=\"div_panel_informativo_feedback\">";
			echo			"</div>";
			echo 			"<div id=\"loading_box\" style=\"display:none;\"></div>";
			//echo 			"<img class='DirectorioRCJA_img_cancel_only' onClick=\"location.href='/dispatcherGET.php?class=DirectorioRCJA&method=body'\" src=\"/MODULOS/DirectorioRCJA/images/cancel.png\">";
			echo 			"<img class='DirectorioRCJA_img_cancel_only' onClick='var res = confirm(\"¿Confirma que desea salir del Proceso de Sincronización?\");if (res){ location.href=\"/dispatcherGET.php?class=DirectorioRCJA&method=body\"; }else{alert(\"Accion Cancelada\");}' src=\"/MODULOS/DirectorioRCJA/images/cancel.png\">";
			echo		"</fieldset>";
			echo	"</form>";
			
			echo "</section>";
		
		echo "</div>";
	}

	function menuPrincipalPreview() {
		//self::tabla_antes( "Consultas, Altas y Modificaciones", "10%" );
		?>

		<div id='DirectorioRCJA_patron' style='display: none'></div>
		<table style='background: white;'>

		<tr>
		<td>
		  <a href="/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=lista&patron=">
		  <img title='Muestra todos los usuarios' class='DirectorioRCJA_sin_borde' src='/MODULOS/DirectorioRCJA/images/users.png'/></a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
				Usuarios
		</td>
		<td>
		<img class='DirectorioRCJA_sin_borde' src='/MODULOS/DirectorioRCJA/images/look.png'/>
		</td>
		<td>
			<form method="get" action="/dispatcherGET.php" name="DatosUsuario">
				<input type="text" id='DirectorioRCJA_Usuario' name="patron" value="" size="30" class="DirectorioRCJA_data"/>
				<input type="hidden" name="class" value="DirectorioRCJA" />
				<input type="hidden" name="method" value="reentrada" />
				<input type="hidden" name="subclass" value="FichaUsuarioRCJA" />
				<input type="hidden" name="submethod" value="body" />
				<input type="hidden" name="vista" value="lista" />
			</form>
		</td>
		</tr>
		<tr><td></td><td></td><td></td><td colspan=3></td></tr>	
		
		<tr>
		<td>
		  <a href="/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaEquipo&submethod=body&vista=lista&patron=">
		  <img title='Muestra todas las l&iacute;neas' class='DirectorioRCJA_sin_borde' src='/MODULOS/DirectorioRCJA/images/roseta.png'/></a>
		</td>
		<td class="DirectorioRCJA_fontBlackBold">
				Extensiones
		</td>
		<td>
		<img class='DirectorioRCJA_sin_borde' src='/MODULOS/DirectorioRCJA/images/look.png'/>
		</td>
		<td>
			<form method="get" action="/dispatcherGET.php" name="DatosExtension">
				<input type="text" id='DirectorioRCJA_Extension' name="patron" value="" size="30" class="DirectorioRCJA_data"/>
				<input type="hidden" name="class" value="DirectorioRCJA" />
				<input type="hidden" name="method" value="reentrada" />
				<input type="hidden" name="subclass" value="FichaExtension" />
				<input type="hidden" name="submethod" value="body" />
				<input type="hidden" name="vista" value="lista" />
			</form>
		</td>
		</tr>
		<tr><td></td><td></td><td></td><td colspan=3>	</td></tr>
				
		</table>
		<?
		//self::tabla_despues();
	}


	public function getVariable($nombreVariable, $valorPorDefecto) {
		return Config::get(0, $nombreVariable, '0', $valorPorDefecto);
	}

}
?>	
		