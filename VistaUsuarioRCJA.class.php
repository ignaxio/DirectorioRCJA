<!-- UsuarioRCJA -->

<?

include_once PATH_MODULOS . "DirectorioRCJA/VistaAbstractaRCJA.class.php";

class VistaUsuarioRCJA extends VistaAbstractaRCJA {
	var $name = __CLASS__;
	var $desc = "Imprime Vistas de UsuarioRCJA";
	var $charset = "UTF-8";

	private $usr = null;


	function preview() {
		echo $desc;
	}

	function body() {
		echo $desc;
	}

	function __construct($usr, $param, $iter) {
		$this->setObjeto($usr);
		$this->setParam($param);
		$this->setIterador($iter);
	}

	public function printVista($vista) {
		if ($vista == "resumen")
			$this->printResumen();
		else if ($vista == "portapapeles")
			$this->printPortapapeles();
		else if ($vista == "basico")
			$this->printFicha();
		else if ($vista == "ficha")
			$this->printFicha();
		else if ($vista == "preview")
			$this->printPreview();
		else if ($vista == "lista")
			$this->printLista();
		else if ($vista == "mini")
			$this->printMini();
		else if ($vista == "lista_usuarios")
			$this->printLista_usuarios();
		else if ( $vista == "exportCSV" ) {
			$param = $this->getParam();
			if (array_key_exists('publicable', $param) && strtolower($param['publicable'])=="si") {
				//$this->printExportListinPublicableCSV(); // NOTA: Version para la comunidad
				$this->printExportListinPublicableCSV2(); // NOTA: Version ad-hoc solo para CEICE PENDIENTE: debe haber solo una version, no FORK!!
			} 
			else {
				$this->printExportCSV();
			}
		} 
		else if ($vista == "json")
			$this->printJSON();
		else {
			parent::$claseHija = basename(__FILE__);
			parent::printVista($vista);
			//echo "vista '{$vista}' NO DEFINIDA EN printVista() EN ".basename(__FILE__)."\n";
		}
	}

	private function getPatron($param) {
		$patron = "patron=" . $param['patron'];
		return $patron;
	}
	
	public function printPreVista($vista) {
		$cuenta = $this->getIterador()->cuenta();
		$param = $this->getParam();
// echo "printPreVista:";
// print_r($param);

		// Imprimimos el menu si estamos en vista lista
		if ($vista == 'lista' || $vista == 'ficha') {
			echo "<div style='background: #e5ecf9;' id='DirectorioRCJA_submenu'>\n";
			echo "	<span id='DirectorioRCJA_submenu_home'><a href='/dispatcherGET.php?class=DirectorioRCJA&method=body'><img style='border-width: 0px;' src='/MODULOS/DirectorioRCJA/images/gohome.png' title='Volver a la pantalla de inicio' alt='Volver a la pantalla de inicio'></a></span>";
			if ($vista != 'ficha') {
				echo "<span>";
				$patron = $this->getPatron($param);
				echo "<a onClick='return SpAJAX.Inventario_CopiarTodo(\"/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&{$patron}&vista=copiarTodo\", \"USUARIO\");'><img style='cursor: pointer; border-width: 0px;height: 22px' src='/MODULOS/DirectorioRCJA/images/copy.png' title='Copiar Todo'></a>";
				echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&{$patron}&vista=exportCSV'><img style='cursor: pointer; border-width: 0px;height: 22px' src='/MODULOS/DirectorioRCJA/images/exportCSV.png' title='Exportar como CSV'></a>";
				echo "</span>";
				echo "<span id='DirectorioRCJA_notificacion_portapapeles_0_USUARIO'></span>";
			}
			echo "</div>\n";
		}
		// Preparamos los datos si la vista es en forma de Preview
		if ($vista == 'preview') {
			// border: 2px solid grey;background-color: white;
			echo "<div style='overflow: auto;height: 300px;width: 100%;display: block;'><table class='DirectorioRCJA_previewtable'>";
			if ($cuenta < 1) {
				echo "<tr><td class='DirectorioRCJA_fontBlack'><img src='/MODULOS/DirectorioRCJA/images/important.png' width=32px>SIN RESULTADOS</td></tr>";
			}
		}

		// Vista mini
		if ($vista == 'mini' && $cuenta < 1) {
			echo "<table>";
			echo "<tr><td class='DirectorioRCJA_fontBlack'><img src='/MODULOS/DirectorioRCJA/images/important.png' width=48px></td><td class='DirectorioRCJA_fontBlackBold'>LA EXTENSIÓN NO TIENE USUARIO(S) ASOCIADOS</td></tr></table>";
		}
		
		// Preparamos los datos si la vista es lista_usuarios
		if ($vista == 'lista_usuarios') {
			?>
			<script language='JavaScript'>
			 	DirectorioRCJA_actualiza_vista_mini_usuario = function (idusr) {
					new Ajax.Updater('DirectorioRCJA_preview_usuario','/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&contdest=DirectorioRCJA_preview_usuario&vold=mini&submethod=printFicha&ajax=true&vista=mini&idusr=' + idusr ,{evalScripts: 'yes'});
					document.getElementById('DirectorioRCJA_lista_usuarios').style.display = 'none';
				}
			</script>
			<script language='JavaScript'>
			 	DirectorioRCJA_oculta_vista_mini_usuario = function () {
					if (document.getElementById('DirectorioRCJA_lista_usuarios').style.display == 'block')
						document.getElementById('DirectorioRCJA_lista_usuarios').style.display = 'none';
					else
						document.getElementById('DirectorioRCJA_lista_usuarios').style.display = 'block';
				}
			</script>
<?
			echo "<div style='background: rgb(229, 236, 249);height: 22px'><img style='cursor: pointer' src='/MODULOS/DirectorioRCJA/images/build.png' onClick='DirectorioRCJA_oculta_vista_mini_usuario();';><span class='DirectorioRCJA_fontBlack'> Se ha(n) encontrado <span class='DirectorioRCJA_fontBlueBold'>" . $cuenta . "</span> USUARIO(s)</span><div id='DirectorioRCJA_lista_usuarios' style='position:absolute; height: 300px;  display: none;'><table width=100% class='DirectorioRCJA_listatable'>";
		}

		// Preparamos los datos si la vista es en forma de resumen
		if ($vista == 'lista') {
			if ($cuenta < 1) {
				DirectorioRCJA::tabla_antes('Se han encontrado ' . $cuenta . ' USUARIO(S)', '3%');
				echo "<table>";
				echo "<tr><td class='DirectorioRCJA_fontBlack'><img src='/MODULOS/DirectorioRCJA/images/important.png' width=48px>NO SE HAN ENCONTRADO RESULTADOS</td></tr>";
			}
			else {
				?>
				<script language='JavaScript'>
						SuperAjax.prototype.DirectorioRCJA_ordena = function (campo, orden){
				<?
				$patron = $this->getPatron($param);
				?>
						url = '/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&<? echo $patron; ?>&vista=lista&ajax=true&campo=' + campo + '&orden=' + orden;
							new Ajax.Updater('layout_body', url, {evalScripts:'yes'});
						};
				</script>
<?
				DirectorioRCJA::tabla_antes('Se han encontrado ' . $cuenta . ' USUARIO(S)', '3%');
				$ordenes = array (1 => 1, 2 => 1, 3 => 1, 4 => 1);
				if (array_key_exists('campo', $param)) {
					$campo = $param['campo'];
					$ordenes[$campo]= $param['orden']* (-1);
				}
				else {
					$campo[1] = '-1';
				}
				echo "<table class='DirectorioRCJA_listatable'>";
				echo "<tr class='DirectorioRCJA_table_head'><td></td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(1,{$ordenes[1]})'>&nbsp;&nbsp;EMPLEADO</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(2,{$ordenes[2]})'>&nbsp;&nbsp;NOMBRE</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(3,{$ordenes[3]})'>&nbsp;&nbsp;&nbsp;&nbsp;NIF</td><td style='cursor: pointer' onClick='SpAJAX.DirectorioRCJA_ordena(4,{$ordenes[4]})'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CENTRO TRABAJO</td></tr>";
			}
		}

		if ($vista == "exportCSV") {
			set_time_limit ( 2*3600 );
			ini_set("memory_limit","1024M");

			// Crea el fichero, el resto de llamadas devuelve el mismo $handle
			$handle = $this->getFicha()->Crear_ExportCSV();
			// print_r("printPreVista:".$handle);
			// Vuelca la cabecera adecuada
			fwrite($handle, mb_convert_encoding("empleado,Nombre,NIF,perfilUsuario,etiquetaEmpleado,observacionesEmpleado,centroDirectivoDepart,centroTrabajo,puestoTrabajo,servicio,tipoUsuario,grupoNivel".PHP_EOL, $this->charset));
		}

		if ($vista == "json"){
			// Crea el objeto JSON que ira almacenando cada elemento
			$objetoJSON = $this->getFicha()->objetoJSON();
			// echo "preVista json:";
			// print_r($objetoJSON);
		}
	}

	public function printPostVista($vista) {
		if ($vista == 'preview') {
			echo "</table></div>";
		}
		if ($vista == 'lista_usuarios') {
			echo "</table></div></div>";
		}
		if ($vista == 'lista') {
			echo "</table>";
			DirectorioRCJA::tabla_despues();
		}
		if ($vista == "exportCSV"){
			$handle = $this->getFicha()->Crear_ExportCSV();
			// print_r("printPostVista:".$handle);
			fclose($handle);
			unset($handle); // sin esto dara function.unlink: Permission denied: http://es.php.net/manual/es/function.unlink.php#49728

			$fileExport = $this->getFicha()->Nombre_ExportCSV();
			// print_r("printPostVista:".$fileExport);
			ob_clean();

			//header("Content-type: application/octet-stream");
			//header('Content-Type: application/csv');
			header("Content-type: text/csv; charset=".$this->charset);
			header("Content-Disposition: attachment; filename=UsuariosTAU.csv");
			header('Content-Length: ' . filesize($fileExport));

			//flush();
			readfile($fileExport);

			unlink($fileExport);
			exit;
		}
	}

	public function printPreview (){
		echo "<tr onmouseover='this.style.background=\"#fffaaf\"' onMouseOut='this.style.background=\"\"'>";
		echo "<td>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'></a>\n";
		echo "</td>";
		echo "<td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>{$this->getObjeto()->getNIF()}</a></td><td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>&nbsp;&nbsp;{$this->getObjeto()->getNombre()}</a></td><td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>&nbsp;&nbsp;{$this->getObjeto()->getEmpleado()}</a></td>";
		echo "</tr>";
	}

	public function printLista (){
		echo "<tr onmouseover='this.style.background=\"#fffaaf\"' onMouseOut='this.style.background=\"\"'>";
		echo "<td>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'></a>\n";
		echo "</td>";
		echo "<td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>{$this->getObjeto()->getEmpleado()}</a></td>";
		echo "<td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>&nbsp;&nbsp;{$this->getObjeto()->getNombre()}</a></td>";
		echo "<td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>&nbsp;&nbsp;&nbsp;&nbsp;{$this->getObjeto()->getNIF()}</a></td>";
		echo "<td class='DirectorioRCJA_fontBlack'><a class='DirectorioRCJA_fontBlack' href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&idusr={$this->getObjeto()->getEmpleado()}'>&nbsp;&nbsp;&nbsp;&nbsp;{$this->getObjeto()->getCentroTrabajo()}</a></td>";
		echo "</tr>";
	}

	public function printFicha() {
		$param = $this->getParam();

		// Construimos el acceso al Portapapeles
		$fic_pp = new FichaPortaPapeles();
		$id_dst = $this->getObjeto()->getEmpleado();
		$Tipo_dst = 'USUARIO RCJA';

		echo "<div id='ficha_usuario_ficha'>\n";

		echo "<table><tr><td style='vertical-align: top' width=50%>";
		DirectorioRCJA::tabla_antes('FICHA DE USUARIO RCJA', '3%' );

		// Construimos el menu para el usuario
		echo "<div style='background: #eeeeff;' id='DirectorioRCJA_submenu_usuario'>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getEmpleado()}'><img style='height: 22px;border-width: 0px' src='/MODULOS/DirectorioRCJA/images/attach.png' title='Recargar ficha de Usuario RCJA'></a>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&idusr={$this->getObjeto()->getEmpleado()}&vista=exportCSV'><img style='cursor: pointer; border-width: 0px;height: 22px' src='/MODULOS/DirectorioRCJA/images/exportCSV.png' title='Exportar como CSV'></a>";
		// FIN DE MENU DE USUARIO
		
		echo "<table><tr><td></td><td>";
		echo "<table><tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>EMPLEADO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getEmpleado()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>NOMBRE</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getNombre()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>NIF</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getNIF()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>PERFIL USUARIO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getPerfilUsuario()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>ETIQUETA</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getEtiquetaEmp()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>OBSERVACIONES</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getObservacionesEmp()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>C.DIRECTIVO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getCentroDirectivoDepart()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>C.TRABAJO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getCentroTrabajo()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>PUESTO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getPuestoTrabajo()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>SERVICIO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getServicio()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>TIPO USUARIO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getTipoUsuario()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>GRUPO NIVEL</td><td class='DirectorioRCJA_fontBlack'>	{$this->getObjeto()->getGrupoNivel()}</td></tr>";

		echo "<tr><td class='DirectorioRCJA_fontBlackBold'>EXTENSIONES</td><td class='DirectorioRCJA_fontBlack'>";
		$extensionesRCJA = new FichaExtensionRCJA();
		$extensionesRCJA->body(Array('idusr' => $this->getObjeto()->getEmpleado(), 'vista' => 'minitelefonos'));
		echo "</td></tr>";
		echo "</table>";
		echo "</td></tr>";
		echo "</table>";
		DirectorioRCJA::tabla_despues();

		echo "</td></tr></table>";

		echo "<!--div id='ficha_usuario_ficha'-->\n";
		echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
}

	public function printMini(){
		$param = $this->getParam();
		// print_r($param);
		echo "<div style='background: #eeeeff;' id='DirectorioRCJA_submenu_usuario_{$param['contdest']}_" . $this->getObjeto()->getEmpleado() . "'>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getEmpleado()}'><img style='height: 22px;border-width: 0px' src='/MODULOS/DirectorioRCJA/images/attach.png'></a>";
		echo "<a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&idusr={$this->getObjeto()->getEmpleado()}&vista=exportCSV'><img style='cursor: pointer; border-width: 0px;height: 22px' src='/MODULOS/DirectorioRCJA/images/exportCSV.png' title='Exportar como CSV'></a>";

		// Construimos el acceso al Portapapeles
		$fic_pp = new FichaPortaPapeles();
		$id_dst = $this->getObjeto()->getEmpleado();
		$Tipo_dst = 'USUARIO RCJA';

		// Construimos el submenu para el objeto
		$id_obj = $this->getObjeto()->getEmpleado();
		$Tipo_obj = "USUARIO RCJA";
		if (array_key_exists('id_org', $param)) {
			$id_org = $param['id_org'];
			$Tipo_org = $param['Tipo_org'];
			$fic_pp->body(array('op' => 'botones', 'vista' => 'copiar/cortar/quitar',
							 'id_obj' => $id_obj, 'Tipo_obj' => $Tipo_obj,
							 'id_org' => $id_org, 'Tipo_org' => $Tipo_org,
							 'id_dst' => $id_dst, 'Tipo_dst' => $Tipo_dst,
							 'div_dst' => 'DirectorioRCJA_submenu_usuario_'. $param['contdest'] . "_" . $this->getObjeto()->getEmpleado() ));
		} 
		else {
			$fic_pp->body(array('op' => 'botones', 'vista' => 'copiar',
							 'id_obj' => $id_obj, 'Tipo_obj' => $Tipo_obj,
							 'id_dst' => $id_dst, 'Tipo_dst' => $Tipo_dst,
							 'div_dst' => 'DirectorioRCJA_submenu_usuario_'. $param['contdest'] . "_" . $this->getObjeto()->getEmpleado() ));
		}
		echo "</div>"; // fin de menu usuario
		// FIN DE MENU DE USUARIO

		echo "<table><tr><td><a href='/dispatcherGET.php?class=DirectorioRCJA&method=reentrada&subclass=FichaUsuarioRCJA&submethod=body&vista=ficha&id={$this->getObjeto()->getEmpleado()}'></a></td><td>";
		echo "<table><tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>EMPLEADO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getEmpleado()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>NOMBRE</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getNombre()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>NIF</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getNIF()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>PERFIL USUARIO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getPerfilUsuario()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>ETIQUETA</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getEtiquetaEmp()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>OBSERVACIONES</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getObservacionesEmp()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>C.DIRECTIVO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getCentroDirectivoDepart()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>C.TRABAJO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getCentroTrabajo()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>PUESTO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getPuestoTrabajo()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>SERVICIO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getServicio()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>TIPO USUARIO</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getTipoUsuario()}</td></tr>";
		echo "<tr><td style='vertical-align: top' class='DirectorioRCJA_fontBlackBold'>GRUPO NIVEL</td><td class='DirectorioRCJA_fontBlack'>{$this->getObjeto()->getGrupoNivel()}</td></tr>";
		
		echo "</table>";
		echo "</table>";
	}

	public function printLista_usuarios() {
		$empleado = $this->getObjeto()->getEmpleado();
		$nombre = $this->getObjeto()->getNombre();
		$nif = $this->getObjeto()->getNIF();
		echo "<tr style='cursor: pointer;' onmouseover='this.style.background=\"#fffaaf\"'  onMouseOut='this.style.background=\"\"' onClick='DirectorioRCJA_actualiza_vista_mini_usuario(\"" . $this->getObjeto()->getEmpleado() . "\");'>";
		echo "<td class='DirectorioRCJA_fontBlack'>&nbsp;&nbsp;" . $empleado . "</td><td class='DirectorioRCJA_fontBlack'>&nbsp;&nbsp;" . $nombre . "</td><td class='DirectorioRCJA_fontBlack'>&nbsp;&nbsp;" . $nif . "</td></tr>";
	}

	public function printExportCSV(){
		$this->charset = "";
		$handle = $this->getFicha()->Crear_ExportCSV();
		// print_r("printExportCSV:".$handle);
		fwrite($handle, "\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getEmpleado(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getNombre(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getNIF(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getPerfilUsuario(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getEtiquetaEmp(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getObservacionesEmp(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getCentroDirectivoDepart(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getCentroTrabajo(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getPuestoTrabajo(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getServicio(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getTipoUsuario(), $this->charset));
		fwrite($handle, "\",\"");
		fwrite($handle, mb_convert_encoding($this->getObjeto()->getGrupoNivel(), $this->charset));
		fwrite($handle, "\"");
		fwrite($handle, PHP_EOL);
	}

}
?>