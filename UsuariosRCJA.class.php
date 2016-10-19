<!-- UsuariosRCJA -->

<?

//include_once PATH_MODULOS . "DirectorioRCJA/Iterador.interface.php";

class UsuariosRCJA extends UsuarioRCJA implements Iterador {
	var $name = __CLASS__;
	var $desc = "Lista de UsuariosRCJA";

	////
	//
	// Implementacion de Iterador
	//
	////

	public function iterador() {
		$this->conn = TAU::conectaDB();
		$this->rs = odbc_exec($this->conn, $this->SQL); // NOTA: $this->SQL es heredada de UsuarioRCJA
		return $this;
	}

	public function hayMas() {
		if( ! ($this->fila = odbc_fetch_array($this->rs)) ) {
			//DirectorioRCJA::DesconexionDB($this->conn);
		}
		return $this->fila; // NOTA: No asignar como UsuarioRCJA(). El resultado se debe evaluar como Boolean. Usar otroElemento
	}

	public function otroElemento() {
		$u = new UsuarioRCJA();
		$u->leerCampos($this->fila);
		return $u;
	}

	public function cuenta() {
		return TAU::odbc_record_count($this->conn, $this->SQL);
	}
}
?>