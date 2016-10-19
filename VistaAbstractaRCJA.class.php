<!-- VistaAbstractaRCJA -->

<?

include_once PATH_MODULOS . "DirectorioRCJA/Vista.interface.php";

abstract class VistaAbstractaRCJA implements Vista {
	var $name = __CLASS__;
	var $desc = "Clase Base para las Vista de Objetos";
	var $charset = "UTF-8";

	static $claseHija = "";
	private $obj = null;
	private $param = "param";
	private $iter = null;
	private $ficha = null;

	//public function printVista($vista) {
	//		La clase base no tiene nada que imprimir.
	//		echo $this->desc;
	//		Este metodo procede de la interface Vista.
	//		Dado que esta clase, al ser base, no la implementa
	//		obliga a declararla abstracta para que sea implementada
	//		en la clase concreta.
	//}

	public function printVista($vista) {
		if ( $vista == "copiarTodo" )
			$this->printCopiarTodo();
		else if ( $vista == "pegarTodo" )
			$this->printPegarTodo();
		else if ( $vista == "PreparaSQL" )
			$this->printPreparaSQL();
		else if ( $vista == "listacampos" )
			$this->printSeleccionCampos();
		else if ( $vista == "ejecutar" )
			$this->printEjecutar();
		else
			die("vista '{$vista}' NO DEFINIDA EN printVista() EN ".self::$claseHija."\n");
	}

	////
	//
	// Implementacion de get/set de miembros comunes: Objeto, Param e Iterador
	//
	////

	public function getObjeto() {
		return $this->obj;
	}

	public function setObjeto($obj) {
		$old = $this->obj;
		$this->obj=$obj;
		return $old;
	}

	public function getParam() {
		return $this->param;
	}

	public function setParam($param) {
		$old = $this->param;
		$this->param=$param;
		return $old;
	}

	public function getIterador() {
		return $this->iter;
	}

	public function setIterador($iter) {
		$old = $this->iter;
		$this->iter=$iter;
		return $old;
	}

	public function getFicha() {
		return $this->ficha;
	}

	public function setFicha($ficha) {
		$old = $this->ficha;
		$this->ficha=$ficha;
		return $old;
	}

	////
	//
	// Implementacion de Copiar y Pegar Todo heredable para todos los objetos
	//
	// Las operaciones de Copiar Todo y Pegar Todo se implementan como vistas para evitar un problema de reentrada.
	//
	////

	public function printCopiarTodo() {
		$param = $this->getParam();
		// print_r($param);
		/*if( substr($Tipo_obj, 0, strlen("EQUIPO")) == "EQUIPO" ) $Tipo_obj = "EQUIPO";*/
		$Tipo_obj = strtoupper($this->getObjeto()->name);
		//echo "printCopiarTodo::".$Tipo_obj;
		// is_subclass_of es case-sensitive
		if( is_subclass_of($this->getObjeto()->name, "Equipo") ) {
			$Tipo_obj = "EQUIPO";
		} else if( is_subclass_of($this->getObjeto()->name, "Recurso") ) {
			$Tipo_obj = "RECURSO";
		}
		// echo "printCopiarTodo: ".$this->getObjeto()->name."(".$Tipo_obj.")";
		$fic_pp = new FichaPortapapeles();
		$fic_pp->body( array('op' => 'copiar',
				'id_obj' => $this->getObjeto()->getID(),
				'Tipo_obj' => $Tipo_obj ) );
	}

	public function printPegarTodo() {
		$param = $this->getParam();
		// echo "printPegarTodo\n<BR>";
		// print_r($param);
		$id = $param['id'];
		$id_obj = $param['id_obj'];
		$Tipo_obj = $param['Tipo_obj'];
		$id_dst = $param['id_dst'];
		$Tipo_dst = $param['Tipo_dst'];

		

		$fic_dst = Inventario2::fabricarFicha($Tipo_dst);
		//echo "AQUI $Tipo_dst";
		//if( $fic_dst->PuedeContener($Tipo_obj) ) {
		//NOTA: Antes bastaba con pasar el $Tipo_obj pero Prestamo necesitaba el id del Objeto a prestar y del Usuario a quien prestar
		// (ambos obtenidos a traves de getIdObj del Objeto Portapapeles) asi que refactorizamos para pasar
		// el Objeto completo y dejamos que todo se compruebe en PuedeContener(), pero aqui en VistaAbstracta no recorremos los objetos
		// contenidos en el PortaPapeles como en VistaPortaPapeles2::printBotonPegar() sino los Objetos reales, asi que creamos uno ad-hoc
		// solo para pasar $Tipo_obj, (salvo en Prestamos, PuedeContener no necesita nada mas... por cierto, Prestamos es el unico que no
		// admite Pegar Todo, porque el orden en que se pegan los objetos influye en la constitucion del Prestamo)
		$pp = new Portapapeles();
		$pp->setTipoObj($Tipo_obj); 
		if( $fic_dst->PuedeContener($pp) ) {
			// El caso de LOCALIZACION como destino es especial ya que hay que actualizar las capas contenedoras de
			// los objetos (PUESTO y ROSETA) y esa informacion solo es tratable desde Javascript
			if( $Tipo_dst == 'LOCALIZACION' ) {
				?>
<script language="javascript">
					SpAJAX.Inventario_Pegar(<?= $id ?>, <?= $id_obj ?>, '<?= $Tipo_obj ?>', <?= $id_dst ?>, '<?= $Tipo_dst ?>');
				</script>
<?
			} else {
			// Para el resto de casos la operacion de pegado no necesita mostrar nada hasta el final por lo que es mas
			// eficiente una llamada interna
			$fic_pp = new FichaPortapapeles();
			$fic_pp->body( array('op' => 'pegar', 'id' => $id, // id de la fila en la tabla Portapapeles
								/* id_obj y Tipo_obj se obtendra del id	del Portapapeles */
								 'id_obj' => $id_obj, 'Tipo_obj' => $Tipo_obj,
							 	 'id_dst' => $id_dst, 'Tipo_dst' => $Tipo_dst ) );
			}
		}/*else
			echo "NO PUEDE CONTENER ".$Tipo_obj." (".$id_obj.")<BR>";*/
	}

	////
	//
	// Implementacion de BusquedaAvanzada heredable para todos los objetos
	//
	////

	public function printDatos() {
echo "printDatos()";
	}

	public function printSQL() {
echo "printSQL()";
	}

	public function printPreparaSQL() {
	?>
<form name='form_select_busquedaavanzada' action=''>
	<table border="1">
		<tr>
			<th>Lista de Tablas</th>
			<th colspan="2">Lista de Campos</th>
			<!--th colspan="2">Lista de Relaciones</th-->
		</tr>
		<tr>
			<td align="center"><? $this->printSeleccionTablas(); ?></td>
			<td><div id="Inventario_busquedaavanzada_campos">
					<? $this->printSeleccionCampos(); ?>
				</div></td>
			<td><input type="button" name="select" value="SELECT"
				onclick='SpAJAX.Inventario_Seleccionar_Campo(this.form.Tablas.options[this.form.Tablas.selectedIndex].value, this.form.Campos.options[this.form.Campos.selectedIndex].value, "Inventario_busquedaavanzada_campos_seleccionados");'><br>
				<input type="button" name="where" value="WHERE"><br> <input
				type="button" name="orderby" value="ORDER BY"><br></td>
			<!--td>
						<? $this->printSeleccionRelaciones(); ?></td>
					<td>
						<input type="button" name="eliminar" value="Eliminar" onclick='SpAJAX.Inventario_Deseleccionar_Campo("Inventario_busquedaavanzada_campos_seleccionados");'><br>
						<input type="button" name="vista" value="Vista Previa" onclick='SpAJAX.Inventario_Vista_Previa("Inventario_busquedaavanzada_campos_seleccionados");'></td-->
		</tr>
	</table>
	<table border="1">
		<tr>
			<th colspan="2">Lista de Campos Seleccionados</th>
		</tr>
		<tr>
			<td><? $this->printCamposSeleccionados(); ?></td>
			<td><input type="button" name="eliminar" value="Eliminar"
				onclick='SpAJAX.Inventario_Deseleccionar_Campo("Inventario_busquedaavanzada_campos_seleccionados");'><br>
				<input type="button" name="vista" value="Vista Previa"
				onclick='SpAJAX.Inventario_Vista_Previa("Inventario_busquedaavanzada_campos_seleccionados");'>
			</td>
		</tr>
	</table>
</form>
<?
	}

	// 	public function printQBY() {
	// echo "printQBY()";
	// 		$columnas = 5;
	// 		? >
	// 		<!--div id="Inventario_busquedaavanzada_pizarra">
	// 			Inventario_busquedaavanzada_pizarra
	// 		</div-->
	// 		<!--form action="/dispatcherGET.php?class=Inventario2&method=reentrada&subclass=FichaBusquedaAvanzada&submethod=ProcesarQBY" method="GET"-->
	// 		<form action="/dispatcherPOST.php" method="POST">
	// 			<input type="hidden" name="class" value="Inventario2" />
	// 			<input type="hidden" name="method" value="reentrada" />
	// 			<input type="hidden" name="subclass" value="FichaBusquedaAvanzada" />
	// 			<input type="hidden" name="submethod" value="ProcesarQBY" />
	// 			<input type="hidden" name="vista" value="lista" />
	// 		<table border="1">
	// 			<tr><th align="right">Tabla:</th>
	// 				<?
	// 					for($i=0; $i<$columnas; $i++) {
// 						echo "<td>";
// 							$this->printSeleccionTablas($i);
// 						echo "</td>";
// 					}
// 				? >
// 			</tr>
// 			<tr><th align="right">Campos:</th>
// 				<?
// 					for($i=0; $i<$columnas; $i++) {
// 						//echo "<div id='Inventario_busquedaavanzada_campo_{$i}'></div>";
// 						echo "<td>";
// 							$this->printSeleccionCampos($i);
// 						echo "</td>";
// 					}
// 				? >
// 			</tr>
// 			<tr><th align="right">Orden:</th>
// 				<?
// 					for($i=0; $i<$columnas; $i++) {
// 						echo "<td>";
// 							$this->printSeleccionOrdenes($i);
// 						echo "</td>";
// 					}
// 				? >
// 			</tr>
// 			<tr><th align="right">Mostrar:</th>
// 				<?
// 					for($i=0; $i<$columnas; $i++) {
// 						echo "<td align='center'>";
// 						echo "	<input type='checkbox' name='Inventario_busquedaavanzada_mostrar_{$i}' value='' >";
// 						echo "</td>";
// 					}
// 				? >
// 			</tr>
// 			<tr><th align="right">o:</th><td>5</td></tr>
// 		</table>
// 		<input type="submit">
// 		</form>
// 		<?
// 	}

// 	public function printSeleccionTablas($i) {
// 		$param = $this->getParam();
// 		$tabla = $param['tabla'];
// 		echo "<select name='Inventario_busquedaavanzada_tabla_{$i}'>";
// 		if($i != 0) echo "<option value ='' selected='selected'></option>";
// 		foreach( $this->getObjeto()->listaTablas() as $key => $value ) {
// 			echo "<option value ='$value'";
// 			if($tabla == $value) echo " selected='selected'";
// 			echo ">$value</option>";
// 		}
// 		echo "</select>";
// 	}

	public function printSeleccionTablas() {
		$param = $this->getParam();
		$div_pp_contenido = 'Inventario_busquedaavanzada_campos';
		$Tipo = $param['Tipo'];
		$fic = Inventario2::fabricarFicha($Tipo);
		$obj = $fic->fabricarObjeto();
		$n = count($obj->listaTablas());
		echo "<select size='{$n}' name='Tablas' onChange=\"SpAJAX.Inventario_Actualizar_ListaCampos(this.form.Tablas.options[this.form.Tablas.selectedIndex].value, '{$div_pp_contenido}' )\">";
		foreach( $this->getObjeto()->listaTablas() as $key => $value ) {
			echo "<option value='$value'>$value</option>";
		}
		echo "</select>";
	}

	public function printSeleccionCampos() {
		$param = $this->getParam();
		$n = 1+count($this->getObjeto()->listaCampos());
		echo "<select size={$n} name='Campos' >";
		echo "<option value ='*'>*</option>";
		foreach( $this->getObjeto()->listaCampos() as $key => $value ) {
			echo "<option value ='$value'>$value</option>";
		}
		echo "</select>";
	}

	public function printSeleccionRelaciones() {
		$param = $this->getParam();
		$n = count($this->getObjeto()->listaRelaciones());
		echo "<select size={$n} >";
		foreach( $this->getObjeto()->listaRelaciones() as $key => $value ) {
			echo "<option value ='$value'>$key</option>";
		}
		echo "</select>";
	}

	public function printCamposSeleccionados() {
		$param = $this->getParam();
		$n = 1;//+count($this->getObjeto()->listaCampos());
		echo "<select size='{$n}' id='Inventario_busquedaavanzada_campos_seleccionados'>";
		//foreach( $this->getObjeto()->listaCampos() as $key => $value ) {
		//echo "<option value =''></option>";
		//}
		echo "</select>";
	}

	public function printSeleccionOrdenes($i) {
		?>
<select
	name='Inventario_busquedaavanzada_orden_<?= $i ?>'>";
	<!--option value =''></option-->
	<option value=''>(sin ordenar)</option>
	<option value='ASC'>Ascendente</option>
	<option value='DESC'>Descendente</option>
</select>
<?
	}

	public function printEjecutar() {
			foreach($this->getObjeto()->getMiembros() as $key => $value ) {
				echo "<td>$value</td>";
			}
	}
	
	public function debug($param) {
		Inventario2::debug(($param?$param:$this));
}
}
?>
