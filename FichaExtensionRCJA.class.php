<!-- FichaExtensionRCJA -->

<?

include_once PATH_MODULOS . "DirectorioRCJA/ExtensionesRCJA.class.php";

class FichaExtensionRCJA {
	var $name = __CLASS__;
	var $desc = "Imprime la ficha de ExtensionRCJA";

	function body($param) {
		$this->printFicha($param);
	}

	function printFicha($param) {
		//print_r($param);
		//echo "<BR>\n";
		$vista = $param['vista'];
		$lista = new ExtensionesRCJA();
		if (array_key_exists('ext', $param)) {
			$lista->buscaPorIdExt($param['ext']);
		}
		else if (array_key_exists('idusr', $param)) {
			$lista->buscaPorIdUsuario($param['idusr']);
		}
		else if (array_key_exists('id', $param) && $param['id'] != '*')	{
			$lista->buscaPorIdExt($param['id']);
		}
		else if (array_key_exists('idext', $param)) {
			$lista->buscaTodos();
		}
		else if (array_key_exists('patron', $param)) {
			if (array_key_exists('orden', $param ) ){
				$lista->buscaPorPatronYOrden($param['patron'], $param['campo'], $param['orden']);
			}
			else {
				$lista->buscaPorPatronYOrden($param['patron']);
			}
		}
		else
			echo "FALTA PARAM ID/PATRON en printFicha() FichaExtensionRCJA.class.php\n";

		$extensiones = $lista->iterador();
		$cuenta = $extensiones->cuenta();
		if ($vista == 'lista' && $cuenta == 1)
			$vista = 'ficha';
		$this->view = $lista->fabricarVistaDecorada($param, $extensiones );
		$this->view->printPreVista($vista);

		// Y ahora imprimimos a los usuarios encontrados
		while ($extensiones->hayMas()) {
			$extension = $extensiones->otroElemento(); // Cast (UsuarioRCJA) implicito
			$this->view = $extension->fabricarVista($param);
			$this->view->printVista($vista);
		}

		$this->view = $lista->fabricarVistaDecorada($param, $extensiones);
		$this->view->printPostVista($vista);
	}

	function fabricarObjeto() {
		return new ExtensionRCJA();
	}
}
?>
