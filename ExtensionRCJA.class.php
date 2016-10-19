<!-- ExtensionRCJA -->

<?
include_once PATH_MODULOS . "DirectorioRCJA/BusquedaAvanzadaAbstracta.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/VistaExtensionRCJA.class.php";

class ExtensionRCJA extends BusquedaAvanzadaAbstracta implements FabricarVista {
	var $name = __CLASS__;
	var $desc = "ExtensionRCJA";

	private $numeroLargo;
	private $numeroCorto="";
	private $tipo="";
	private $perfilTerminal="";
	private $sede="";
	private $usoLinea="";
	private $privado="";
	private $etiquetaLinea="";
	private $observacionesLinea="";
	private $usoEspecial="";
	private $asignacionEspecial="";

	// Hace global privadas la lista de campos y los criterios de ordenacion
	private $campos = array (1 => 'rcja_extensiones.num_Largo', 
						 	 2 => 'rcja_extensiones.num_Corto', 
							 3 => 'rcja_extensiones.tipo',
							 4 => 'rcja_extensiones.perfil_Terminal',
							 5 => 'rcja_extensiones.sede',
							 6 => 'rcja_extensiones.uso_Linea',
							 7 => 'rcja_extensiones.privado');
							 
	private $ordenes = array (1 => 'ASC', 
							 -1 => 'DESC');
							 
	// reconstruye la jerarquia
	public function __construct() {
	}

	////
	//
	// Implementacion de BusquedaAvanzada
	//
	////

	public function listaCampos() {
		return Array("num_Largo", "num_Corto", "tipo", "perfil_Terminal", "sede", "uso_Linea", "privado", "etiqueta_Ln", "observaciones_Ln", "uso_Especial", "asignacion_Especial");
	}

	public function listaRelaciones() {
		return;
	}

	
	
	////
	//
	// Implementacion de FabricarVista
	//
	////
	
	public function fabricarVista($param) {
		return new VistaExtensionRCJA($this, $param, null);
	}

	public function fabricarVistaDecorada($param, $iter) {
		return new VistaExtensionRCJA($this, $param, $iter);
	}
	
	////
	//
	// funciones de acceso a las propiedades
	//
	////

	public function getNumeroLargo() {
		return $this->numeroLargo;
	}

	public function setNumeroLargo($numeroLargo) {
		$old = $this->numeroLargo;
		$this->numeroLargo=$numeroLargo;
		return $old;
	}

	public function getNumeroCorto() {
		return $this->numeroCorto;
	}

	public function setNumeroCorto($numeroCorto) {
		$old = $this->numeroCorto;
		$this->numeroCorto=$numeroCorto;
		return $old;
	}

	public function getTipo() {
		return $this->tipo;
	}

	public function setTipo($tipo) {
		$old = $this->tipo;
		$this->tipo=$tipo;
		return $old;
	}

    public function getPerfilTerminal() {
		return $this->perfilTerminal;
	}

	public function setPerfilTerminal($perfilTerminal) {
		$old = $this->perfilTerminal;
		$this->perfilTerminal=$perfilTerminal;
		return $old;
	}

	public function getSede() {
		return $this->sede;
	}

	public function setSede($sede) {
		$old = $this->sede;
		$this->sede=$sede;
		return $old;
	}

	public function getUsoLinea() {
		return $this->usoLinea;
	}

	public function setUsoLinea($usoLinea) {
		$old = $this->usoLinea;
		$this->usoLinea=$usoLinea;
		return $old;
	}

	public function getPrivado() {
		return $this->privado;
	}

	public function setPrivado($privado) {
		$old = $this->privado;
		$this->privado=$privado;
		return $old;
	}

	public function getEtiquetaLinea() {
		return $this->etiquetaLinea;
	}

	public function setEtiquetaLinea($etiquetaLinea) {
		$old = $this->etiquetaLinea;
		$this->etiquetaLinea=$etiquetaLinea;
		return $old;
	}

	public function getObservacionesLinea() {
		return $this->observacionesLinea;
	}

	public function setObservacionesLinea($observacionesLinea) {
		$old = $this->observacionesLinea;
		$this->observacionesLinea=$observacionesLinea;
		return $old;
	}
	
	public function getUsoEspecial() {
		return $this->usoEspecial;
	}

	public function setUsoEspecial($usoEspecial) {
		$old = $this->usoEspecial;
		$this->usoEspecial=$usoEspecial;
		return $old;
	}

	public function getAsignacionEspecial() {
		return $this->asignacionEspecial;
	}

	public function setAsignacionEspecial($asignacionEspecial) {
		$old = $this->asignacionEspecial;
		$this->asignacionEspecial=$asignacionEspecial;
		return $old;
	}	
	
	
	
	////
	//
	// funciones de busca y seleccion
	//
	////
	
	public function busca() {
		$this->SQL = "SELECT rcja_extensiones.num_Largo, rcja_extensiones.num_Corto, rcja_extensiones.tipo,";
		$this->SQL .= " rcja_extensiones.perfil_Terminal, rcja_extensiones.sede,";
		$this->SQL .= " rcja_extensiones.uso_Linea, rcja_extensiones.privado, rcja_extensiones.etiqueta_Ln,";
		$this->SQL .= " rcja_extensiones.observaciones_Ln, rcja_extensiones.uso_Especial, rcja_extensiones.asignacion_Especial";
		$this->SQL .= " FROM rcja_extensiones";
		//$this->debug($this->SQL);
		return $this->SQL;
	}
	
	public function buscaPorIdExt($numeroLargo) {
		$this->SQL = "SELECT rcja_extensiones.num_Largo, rcja_extensiones.num_Corto, rcja_extensiones.tipo, rcja_extensiones.perfil_Terminal,";
		$this->SQL .=" rcja_extensiones.sede, rcja_extensiones.uso_Linea, rcja_extensiones.privado, rcja_extensiones.etiqueta_Ln, rcja_extensiones.observaciones_Ln, rcja_extensiones.uso_Especial, rcja_extensiones.asignacion_Especial";
		$this->SQL .=" FROM rcja_extensiones";
		$this->SQL .= " WHERE rcja_extensiones.num_Largo = {$numeroLargo};";
//		$this->debug($this->SQL);
		return $this->SQL;
	}
	
	public function buscaPorIdUsuario($empleado) {
		$this->SQL = "SELECT rcja_extensiones.num_Largo, rcja_extensiones.num_Corto, rcja_extensiones.tipo, rcja_extensiones.perfil_Terminal,";
		$this->SQL .=" rcja_extensiones.sede, rcja_extensiones.uso_Linea, rcja_extensiones.privado, rcja_extensiones.etiqueta_Ln, rcja_extensiones.observaciones_Ln, rcja_extensiones.uso_Especial, rcja_extensiones.asignacion_Especial";
		$this->SQL .=" FROM rcja_extensiones, rcja_usuarios, rcja_asociacionesExt_Usu";
		$this->SQL .= " WHERE (rcja_usuarios.empleado = rcja_asociacionesExt_Usu.empleado) AND (rcja_asociacionesExt_Usu.empleado = '{$empleado}') AND (rcja_asociacionesExt_Usu.num_Largo = rcja_extensiones.num_Largo);";
//		$this->debug($this->SQL);
		return $this->SQL;
	}

	public function buscaPorPatronYOrden($patron, $campo=1, $orden=1) {
		$campos = array (1 => 'rcja_extensiones.num_Largo' , 2 => 'rcja_extensiones.num_Corto', 3 => 'rcja_extensiones.tipo', 4 => 'rcja_extensiones.perfil_Terminal', 5 => 'rcja_extensiones.sede', 6 => 'rcja_extensiones.uso_Linea', 7 => 'rcja_extensiones.privado');
		$ordenes = array (1 => 'ASC', -1 => 'DESC');
		$this->SQL = $this->busca();
		$this->SQL .= " WHERE rcja_extensiones.num_Largo='{$patron}'";
		$this->SQL .= "    OR rcja_extensiones.num_Corto Like '%{$patron}%' ";
		$this->SQL .= "    OR rcja_extensiones.sede	Like '%{$patron}%' ";
		$this->SQL .= "    OR rcja_extensiones.uso_Linea Like '%{$patron}%' ";
		$this->SQL .= "    OR rcja_extensiones.etiqueta_Ln Like '%{$patron}%' ";
		$this->SQL .= " ORDER BY {$campos[$campo]} {$ordenes[$orden]}";
//		$this->debug($this->SQL);
		return $this->SQL;
	}

	

	////
	//
	// funciones de creacion y actualizacion
	//
	////

	public function crear($conn) {
		$this->SQL = "INSERT IGNORE INTO rcja_extensiones (";
		
		foreach ($this->listaCampos() as $campo) {
			$this->SQL .= $campo . ", ";
		}
		
		// Eliminamos la subcadena final sobrante (", ")
		$this->SQL = substr($this->SQL, 0, -2);
		
		$this->SQL .= ") VALUES (";
		$this->SQL .= "         " . Inventario2::to_SQL($this->getNumeroLargo(), "integer") . ", " . Inventario2::to_SQL($this->getNumeroCorto()) . ", ";
		$this->SQL .= " 	    " . Inventario2::to_SQL($this->getTipo()) . ", " . Inventario2::to_SQL($this->getPerfilTerminal()) . ", ";
		$this->SQL .= " 	    " . Inventario2::to_SQL($this->getSede()) . ", " . Inventario2::to_SQL($this->getUsoLinea()) . ", ";
		$this->SQL .= " 	    " . Inventario2::to_SQL($this->getPrivado()) . ", " . Inventario2::to_SQL($this->getEtiquetaLinea()) . ", ";
		$this->SQL .= " 	    " . Inventario2::to_SQL($this->getObservacionesLinea()) . ", " . Inventario2::to_SQL($this->getUsoEspecial()) . ",";
		$this->SQL .= "		    " . Inventario2::to_SQL($this->getAsignacionEspecial()) . ");";

		// Ejecutar la operación de inserción
		$resultado = TAU::ejecutarSQL($conn, $this->SQL);
		
		if (!$resultado) {
			echo "SQL=" . $this->SQL;
			die('No se pudo completar la operación de insercion en la tabla rcja_extensiones: ' . odbc_errormsg());
		}
		
		return $this->getNumeroLargo();
	}
	
	public function leerCampos($fila) {
//		$this->debug($fila);
		$this->setNumeroLargo($fila['num_Largo']);
		$this->setNumeroCorto($fila['num_Corto']);
		$this->setTipo($fila['tipo']);
		$this->setPerfilTerminal($fila['perfil_Terminal']);
		$this->setSede($fila['sede'] );
		$this->setUsoLinea($fila['uso_Linea']);
		$this->setPrivado($fila['privado']);
		$this->setEtiquetaLinea($fila['etiqueta_Ln']);
		$this->setObservacionesLinea($fila['observaciones_Ln']);
		$this->setUsoEspecial($fila['uso_Especial']);
		$this->setAsignacionEspecial($fila['asignacion_Especial']);
	}

	public function debug($param) {
		DirectorioRCJA::debug(($param?$param:$this));
	}
}
?>
