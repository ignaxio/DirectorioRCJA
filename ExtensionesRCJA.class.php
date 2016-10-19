<!-- ExtensionesRCJA -->

<?

include_once PATH_MODULOS . "DirectorioRCJA/ExtensionRCJA.class.php";
//include_once PATH_MODULOS . "DirectorioRCJA/Iterador.interface.php";

class ExtensionesRCJA extends ExtensionRCJA implements Iterador {
	var $name = __CLASS__;
	var $desc = "Lista de ExtensionesRCJA";

	////
	//
	// Implementacion de Iterador
	//
	////

	public function iterador() {
		$this->conn = TAU::conectaDB();
		$this->rs = odbc_exec($this->conn, $this->SQL);
		return $this;
	}

	public function hayMas() {
		if (!($this->fila = odbc_fetch_array($this->rs))) {
			//DirectorioRCJA::DesconexionDB($this->conn);
		}
		return $this->fila; // NOTA: No asignar como Objeto. El resultado se debe evaluar como Boolean. Usar otroElemento
	}

	public function otroElemento() {
		$ext = new ExtensionRCJA();
		$ext->leerCampos($this->fila);
		return $ext;
	}

	public function cuenta() {
		return TAU::odbc_record_count($this->conn, $this->SQL);
	}
}

?>
