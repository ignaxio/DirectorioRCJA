<!-- FichaUsuariosEstanEnTauNoEstanEnRCJA -->

<?
include_once PATH_MODULOS . "DirectorioRCJA/UsuariosRCJA.class.php";
include_once PATH_MODULOS . "Inventario2/Usuarios.class.php";

//include_once PATH_MODULOS . "DirectorioRCJA/VistaUsuarioRCJA.class.php";

class UsuariosEstanEnTauNoEstanEnRCJA {

        var $name = __CLASS__;
        var $desc = "Imprime la ficha de UsuariosEstanEnTauNoEstanEnRCJA";
        private $view = null;

        function preview() {
                echo $desc;
        }

        function body($param) {
                $this->printFicha($param);
        }

        function printFicha($param) {
                $usuariosEstanEnTauNoEstanEnRCJA = array();
                $usuariosRCJA = array();
                $usuariosTAU = array();
                //Conectamos a BD
                $this->conn = TAU::conectaDBMysql();
                mysql_select_db('TAU');

                //Vamos a recoger los usuarios de RCJA que tenemos en la basse de datos.
                $usuarios = new UsuariosRCJA();
                $SQLusuariosRCJA = $usuarios->busca();
                $result = mysql_query($SQLusuariosRCJA);
                while ($row = mysql_fetch_array($result)) {
                        $usuarioRCJA = new UsuarioRCJA();
                        //Metemos los datos en la clase.
                        $usuarioRCJA->leerCampos($row);
                        //Vamos llenando el array.
                        $usuariosRCJA[] = $usuarioRCJA;
                }

                //Ahora los usuarios de TAU  que están en la base de datos "inventario".
                mysql_select_db('inventario');
                $usuarios = new Usuarios();
                $SQLusuarios = $usuarios->busca();
                //Conectamos a BD
                $result = mysql_query($SQLusuarios);
                while ($row = @mysql_fetch_array($result)) {
                        $usuarioTAU = new Usuario();
                        //Metemos los datos en la clase.
                        $usuarioTAU->leerCampos($row);
                        //Vamos llenando el array.
                        $usuariosTAU[] = $usuarioTAU;
                }
                //Liberamos la consulta
                @mysql_free_result($result);
                // Cerrar la conexión
                mysql_close($this->conn);

                //Ahora hay que comparar para ver los usuariso que hay en TAU que no están en RCJA
                foreach ($usuariosTAU as $keyUsuarioTAU => $usuarioTAU) {
                        $existe = false;
                        foreach ($usuariosRCJA as $keyUsuarioRCJA => $usuarioRCJA) {
                                //Buscamos por DNI
                                if ($usuarioTAU->getDNI() == $usuarioRCJA->getNIF()) {
                                        $existe = true;
                                }
                                //Buscamos por nomobre y apellidos.
                                $nombreTAU = $usuarioTAU->getNombre() . ' ' . $usuarioTAU->getApellidos();
                                if($nombreTAU == $usuarioRCJA->getNombre()) {
                                        $existe = true;
                                }
                        }
                        if($existe) {
                              $usuariosEstanEnTauNoEstanEnRCJA[] =  $usuarioTAU;
                        }
                }
                print_r($usuariosEstanEnTauNoEstanEnRCJA);
                
                //Ahora lo mandamos a la vista.
//                $this->view = $usuarios->fabricarVistaDecorada($param, $usuarios);
//		$this->view->setFicha($this);
//		$this->view->printPreVista($vista);
        }

        function fabricarObjeto() {
                return new UsuarioRCJA();
        }

// Singleton
// Crea el fichero solo en printPreVista() tras llamar a fabricarVistaDecorada, el resto de llamadas devuelve el mismo $handle
// NOTA: Se llama a Nombre_ExportExcelCSV() pasando el nombre del fichero
//	public function Crear_ExportCSV(){
//		static $handle = null;
//
//		if( $handle != null )
//			return $handle;
//
//		$tmpfname = tempnam(PATH_ROOT, "UsuariosTAU");
//		$this->Nombre_ExportCSV($tmpfname);
//		$handle = fopen($tmpfname, "w");
//		fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para UTF-8 segun http://php.net/manual/de/function.mb-detect-encoding.php
//		// print_r("Crear_ExportCSV:".$handle);
//		return $handle;
//	}
// Singleton
// Registra el nombre del fichero temporal creado
//	public function Nombre_ExportCSV($filename=""){
//		static $name = null;
//
//		if( $name != null )
//			return $name;
//		$name = $filename;
//		// print_r("Nombre_ExportCSV:".$name);
//		return $name;
//	}
// Singleton
// Crea el objeto solo en printPreVista() tras llamar a fabricarVistaDecorada, el resto de llamadas devuelve la misma referencia
// NOTA: es necesario que este aqui ya que el objeto vista no es persistente
//	public function objetoJSON($arrJSONin = null){
//		static $arrJSONst = null;
//		// echo "objetoJSON:";
//		if( $arrJSONin != null ){
//			// echo "asigna otro";
//			// print_r($arrJSONin);
//			$arrJSONst = $arrJSONin;
//			return $arrJSONst;
//		}
//
//		if( $arrJSONst != null ){
//			// echo "obtiene antiguo";
//			// print_r($arrJSONst);
//			return $arrJSONst;
//		}
//
//		$arrJSONst = array();
//		// echo "crea nuevo";
//		return $arrJSONst;
//	}

        function debug($param = NULL) {
                DirectorioRCJA::debug(($param ? $param : $this));
        }

}
?>
