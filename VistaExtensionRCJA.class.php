<!-- VistaExtensionRCJA -->

<?

class VistaExtensionRCJA extends VistaAbstracta {
	var $name = __CLASS__;
	var $desc = "Imprime Vistas de ExtensionRCJA";

	function preview() {
 		echo $desc;
	}

	function body() {
		echo $desc;
	}

	function __construct($ext, $param, $iter) {
		$this->setObjeto($ext);
		$this->setParam($param);
		$this->setIterador($iter);
	}

	public function printVista($vista) {
//echo "vista = '{$vista}'";		
		if ($vista == "preview")
			$this->printPreview();
		else if ($vista == "lista")
			$this->printLista();
		else if ($vista == "ficha")
			$this->printFicha();
		else if ($vista == "minitelefonos")
			$this->printMinitelefonos();
		else {
			parent::$claseHija = basename(__FILE__);
			parent::printVista($vista);
			//echo "vista '{$vista}' NO DEFINIDA EN printVista() EN ".basename(__FILE__)."\n";
		}
	}

	public function printPostVista($vista) {
		if ($vista == 'lista') {
			echo "</table>";
			DirectorioRCJA::tabla_despues();
		}
		if ($vista == 'preview') {
			echo "</table></div>";
		}
	}

	public function printPreVista($vista) {
		$param = $this->getParam();
		if ($vista == "lista") {
			$listaExtensiones = new ExtensionesRCJA(); 
			$listaExtensiones->buscaPorPatronYOrden(array_key_exists('patron', $param)?$param['patron']:"");
			$extensiones = $listaExtensiones->iterador();
			$cuenta = $extensiones->cuenta();
		} 
		else {
			$cuenta = $this->getIterador()->cuenta();
		}

		// Imprimimos el menu si estamos en vista lista
		if ($vista == 'lista' || $vista == 'ficha') {
			echo "<div style='background: #e5ecf9;' id='DirectorioRCJA_submenu'>\n";
			echo "	<span id='DirectorioRCJA_submenu_home'><a href='/dispatcherGET.php?class=DirectorioRCJA&method=body'><img style='border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/gohome.png' title='Volver a la pantalla de inicio' alt='Volver a la pantalla de inicio'></a></span><span id='DirectorioRCJA_submenu_portapapeles'><span id='DirectorioRCJA_submenu_portapapeles_estado'></span><div id='DirectorioRCJA_submenu_portapapeles_contenido' style='width: 100%; display: none;'></div></span>\n";
			echo "</div>\n";
		}
		
		// Preparamos los datos si la vista en en forma de Preview
		if ($vista == 'preview') {
			// border: 2px solid grey;background-color: white;
			echo "<div style='overflow: auto; height: 300px;width: 100%;  display: block; '><table class='DirectorioRCJA_previewtable'>";
			if ($cuenta < 1) {
				echo "<tr><td class='DirectorioRCJA_fontBlack'><img src='/MODULOS/DirectorioRCJA/images/important.png' width=32px>SIN RESULTADOS</td></tr>";
			}
		}

		if ($vista == 'mini' && $cuenta < 1) {
			echo "<table>";
			echo "<tr><td class='DirectorioRCJA_fontBlack'><img src='/MODULOS/DirectorioRCJA/images/important.png' width=48px></td><td class='DirectorioRCJA_fontBlackBold'>LA EXTENSION NO TIENE USUARIO(S) ASOCIADOS</td></tr></table>";
		}
		
		// Preparamos los datos si la vista es lista_rosetas_equipo
		if ($vista == 'lista_usuarios') {
			?>
			<script language='JavaScript'>
			 	DirectorioRCJA_actualiza_vista_mini_usuario = function (idusr){
					new Ajax.Updater('DirectorioRCJA_preview_usuario','/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=printFicha&ajax=true&vold=mini&contdest=fake&vista=mini&idros=' + idusr,{evalScripts: 'yes'});
					document.getElementById('DirectorioRCJA_lista_equipos_usuario').style.display = 'none';
				}
			</script>
			<?
			echo "<div style='background: rgb(229, 236, 249);height: 22px'><img style='cursor: pointer' src='/MODULOS/DirectorioRCJA/images/build.png' onClick='document.getElementById(\"DirectorioRCJA_lista_equipos_usuario\").style.display=\"block\"';><span class='DirectorioRCJA_fontBlack'> Se ha(n) encontrado <span class='DirectorioRCJA_fontBlueBold'>" . $cuenta . "</span> PC(s) o Port&acute;til(es)</span><div id='DirectorioRCJA_lista_equipos_usuario' style='overflow: auto; height: 300px; width: 100%; display: none;'><table class='DirectorioRCJA_listatable'>";
		}

		// Preparamos los datos si la vista es en forma de resumen
		if ($vista == 'lista') {
			if ($cuenta < 1) {
				DirectorioRCJA::tabla_antes('Se han encontrado ' . $cuenta . ' EXTENSIONES(S)', '3%');
				echo "<table>";
				echo "<tr><td class='DirectorioRCJA_fontBlack'><img src='/MODULOS/DirectorioRCJA/images/important.png' width=48px>NO SE HAN ENCONTRADO RESULTADOS</td></tr>";
			}
			else {
				?>
					<script language='JavaScript'>
						SuperAjax.prototype.DirectorioRCJA_ordena = function (campo, orden){
							url = '/dispatcherGET.php?&patron=<? echo $param['patron'];?>&class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=lista&ajax=true&campo=' + campo + '&orden=' + orden;
							new Ajax.Updater('layout_body', url, {evalScripts: 'yes' } );
						};
					</script>
				<?
				DirectorioRCJA::tabla_antes('Se han encontrado ' . $cuenta . ' EXTENSIONES(S)', '3%');
				$ordenes = array (1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1) ;
				if (array_key_exists('campo', $param)) {
					$campo = $param['campo'];
					$ordenes[$campo]= $param['orden']* (-1);
				}
				else {
					$campo[1] = '-1';
				}
				echo "<table class='DirectorioRCJA_listatable'>";
				echo "<tr class='DirectorioRCJA_table_head'><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(1,{$ordenes[1]})'>&nbsp;&nbsp;NÚMERO LARGO</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(2,{$ordenes[2]})'>&nbsp;&nbsp;NÚMERO CORTO</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(3,{$ordenes[3]})'>&nbsp;&nbsp;TIPO EXTENSIÓN</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(4,{$ordenes[4]})'>&nbsp;&nbsp;PERFIL TERMINAL</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(5,{$ordenes[5]})'>&nbsp;&nbsp;SEDE</td><td style='cursor: pointer; text-align: center' onClick='SpAJAX.DirectorioRCJA_ordena(6,{$ordenes[6]})'>&nbsp;&nbsp;USO LINEA</td><td style='cursor: pointer; text-align: center' onClick='SpAJAX.DirectorioRCJA_ordena(7,{$ordenes[7]})'>&nbsp;&nbsp;PRIVADO</td></tr>";
			}
		}
	}

	public function printPreview (){
		$numero = $this->getObjeto()->getNumeroLargo();

		echo "<tr onmouseover='this.style.background=\"#fffaaf\"' onMouseOut='this.style.background=\"\"'>";
		echo "<td>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>";
		echo "		<img style='border: 0px' src='/MODULOS/DirectorioRCJA/images/phone.png'></a></td>";
			
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getNumeroLargo()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getNumeroCorto()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getSede()}</a></td>";

		echo "</tr>";
	}

	public function printLista (){
// print_r($this->getObjeto());
		echo "<tr onmouseover='this.style.background=\"#fffaaf\"' onMouseOut='this.style.background=\"\"'>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getNumeroLargo()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getNumeroCorto()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getTipo()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getPerfilTerminal()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getSede()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getUsoLinea()}</a></td>";
		
		echo "<td class='DirectorioRCJA_fontBlack'>";
		echo "	<a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'>&nbsp;&nbsp;{$this->getObjeto()->getPrivado()}</a></td>";

		echo "</tr>";
	}

	public function printFicha() {
		$param = $this->getParam();

		// Construimos el acceso al Portapapeles
		$fic_pp = new FichaPortaPapeles();
		$id_dst = $this->getObjeto()->getNumeroLargo();
		$Tipo_dst = 'EXTENSION RCJA';

		echo "<table><tr><td style='vertical-align: top' width=50%>";
		DirectorioRCJA::tabla_antes('FICHA DE EXTENSION RCJA ' . $this->getObjeto()->getNumeroLargo(), '3%');

		// Construimos el menu para la roseta
		echo "<div style='background: #eeeeff;' id='DirectorioRCJA_submenu_extension'>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'><img style='height: 22px;border-width: 0px' src='/MODULOS/DirectorioRCJA/images/attach.png'></a>";

		//echo "<span><img onClick='new Ajax.Updater(\"DirectorioRCJA\", \"/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&id=" .  $this->getObjeto()->getNumeroLargo() . "&vista=ediciontelefono2&edest=DirectorioRCJA_extension_edit_menu&ajax=true&vold=ficha&contdest=layout_body\", {evalScripts: \"yes\" } )' src='/MODULOS/DirectorioRCJA/images/Gedicion.png' style='cursor: pointer'><div style='background: rgb(238, 238, 255) none repeat scroll;position: absolute;' id='DirectorioRCJA_extension_edit_menu'></div></span>";
		echo "</div>";

		echo "<div>\n";
		echo "<table><tr><td><a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'><img style='height: 128px;border-width: 0px' src='/MODULOS/DirectorioRCJA/images/Gphone.png'></a></td><td>";
		echo "<table><td class='DirectorioRCJA_fontBlackBold'>NUM.CORTO&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getNumeroCorto()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>TIPO&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getTipo()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>PERFIL&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getPerfilTerminal()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>SEDE&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getSede()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>USO LINEA&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getUsoLinea()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>PRIVADO&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getPrivado()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>ETIQUETA&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getEtiquetaLinea()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>OBSERVACIONES&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getObservacionesLinea()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>USO ESPECIAL&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getUsoEspecial()}</td></tr>";
		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>ASIGNACION&nbsp;&nbsp; </td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getAsignacionEspecial()}</td></tr>";

		echo "</td></tr>";
		echo "</table>";
		echo "</td></tr>";
		echo "</table>";
		DirectorioRCJA::tabla_despues();
		echo "</td><td style='vertical-align: top;width: 50%'>";

		DirectorioRCJA::tabla_antes('DATOS DE USUARIOS ADSCRITOS A LA EXTENSION RCJA', '3%' );
		$usuariosRCJA = new FichaUsuarioRCJA();
		echo "<div><span></span><span></span></div>";
		$usuariosRCJA->body(Array('idextension' => $this->getObjeto()->getNumeroLargo(), 'vista' => 'lista_usuarios'));
		echo "<div id='DirectorioRCJA_preview_usuario'>";
		$usuariosRCJA->body(Array('idextension' => $this->getObjeto()->getNumeroLargo(), 'vista' => 'mini', 'vold' => 'mini', 'contdest' => 'DirectorioRCJA_preview_usuario'));
		echo "</div>";
		DirectorioRCJA::tabla_despues();
		echo "</td></tr></table>";
		echo "<div id='DirectorioRCJA_cuerpo_recursos'></div>";
		echo "<!--div id='ficha_usuariosRCJA_ficha'-->\n";
		echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
	}

	public function printMinitelefonos() {
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getNumeroLargo()}'><img style='height: 8px;border-width: 0px' src='/MODULOS/DirectorioRCJA/images/pattach.png'></a>&nbsp;&nbsp;";
		echo "<span title='Llamar mediante TAUPhone' onClick='new Ajax.Request(\"/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaExtensionRCJA&submethod=TAUphone&ajax=true&telefono=" . $this->getObjeto()->getNumeroLargo() . "\",{evalScripts: \"yes\"});' style='cursor: pointer;' class='DirectorioRCJA_fontRedBigBold'>";
		echo $this->getObjeto()->getNumeroLargo() . "</span>&nbsp;&nbsp; ";
	}
}
?>
