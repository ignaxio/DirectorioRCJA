<!-- FichaUsuarioRCJA -->

<?
include_once PATH_MODULOS . "DirectorioRCJA/UsuariosRCJA.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/VistaUsuarioRCJA.class.php";

class FichaUsuarioRCJA {

        var $name = __CLASS__;
        var $desc = "Imprime la ficha de UsuarioRCJA";
        private $view = null;

        function preview() {
                echo $desc;
        }

        function body($param) {
                $this->printFicha($param);
        }

        function printFicha($param) {
//		$this->debug($param);
                $vista = $param['vista'];

                $listaUsr = new UsuariosRCJA();
                if (array_key_exists('id', $param) && $param['id'] != '*')
                        $listaUsr->buscaPorIdUsr($param['id']);
                else if (array_key_exists('idusr', $param))
                        $listaUsr->buscaPorIdUsr($param['idusr']);
                else if (array_key_exists('idextension', $param))
                        $listaUsr->buscaPorIdExtension($param['idextension']);
                else if (array_key_exists('patron', $param)) {
                        if (array_key_exists('orden', $param)) {
                                $listaUsr->buscaPorPatron($param['patron'], $param['campo'], $param['orden']);
                        } else {
                                if (!array_key_exists('publicable', $param)) {
                                        $listaUsr->buscaPorPatron($param['patron']); /* siempre ordena */
                                } else {
                                        $listaUsr->buscaPorPublicable($param['publicable']); // vista=listintelefonicoalfabetico, vista=exportCSV
                                }
                        }
                } elseif ($vista == 'lista_usuarios_dos_sistemas') {
                        $sincronizar = new SincronizacionDirectorioRCJA();
                        $usuarios = $sincronizar->obtenerUsuariosAmbosSistemas();
//                        $idUsuarioTau = $usuarios['idRCJA'];
                        $orden = isset($param['orden']) ? $param['orden'] : 1;
                        $campo = isset($param['campo']) ? $param['campo'] : 1;
                        $listaUsr->buscaPorIdes($sincronizar->obtenerUsuariosAmbosSistemas(), $campo, $orden);
                        $this->arrayUsuariosDosSistemas = $usuarios;
                } else {
                        echo "FALTA PARAM ID/PATRON en printFicha() FichaUsuarioRCJA.class.php\n";
                }

                $usuarios = $listaUsr->iterador();
//                $this->debug($usuarios);
                $cuenta = $usuarios->cuenta();
                if ($vista == 'lista' && $cuenta == 1)
                        $vista = 'ficha';
                $this->view = $listaUsr->fabricarVistaDecorada($param, $usuarios);
                $this->view->setFicha($this);
                $this->view->printPreVista($vista);

                if (!(array_key_exists('ObjSinLoc', $param) && $vista == 'cuenta')) { // El trabajo ya esta hecho en la PreVista
                        // Y ahora imprimimos a los usuarios encontrados
                        while ($usuarios->hayMas()) {
                                $usuario = $usuarios->otroElemento(); // Cast (UsuarioRCJA) implicito
                                $this->view = $usuario->fabricarVista($param);
                                $this->view->setFicha($this);
                                $this->view->printVista($vista);

                                if ($vista == 'mini') // en la vista mini nos quedamos con el primero de la lista
                                        break;
                        }
                }

                $this->view = $listaUsr->fabricarVistaDecorada($param, $usuarios);
                $this->view->setFicha($this);
                $this->view->printPostVista($vista);            
        }

        function fabricarObjeto() {
                return new UsuarioRCJA();
        }

        // Singleton
        // Crea el fichero solo en printPreVista() tras llamar a fabricarVistaDecorada, el resto de llamadas devuelve el mismo $handle
        // NOTA: Se llama a Nombre_ExportExcelCSV() pasando el nombre del fichero
        public function Crear_ExportCSV() {
                static $handle = null;

                if ($handle != null)
                        return $handle;

                $tmpfname = tempnam(PATH_ROOT, "UsuariosTAU");
                $this->Nombre_ExportCSV($tmpfname);
                $handle = fopen($tmpfname, "w");
                fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para UTF-8 segun http://php.net/manual/de/function.mb-detect-encoding.php
                // print_r("Crear_ExportCSV:".$handle);
                return $handle;
        }

        // Singleton
        // Registra el nombre del fichero temporal creado
        public function Nombre_ExportCSV($filename = "") {
                static $name = null;

                if ($name != null)
                        return $name;
                $name = $filename;
                // print_r("Nombre_ExportCSV:".$name);
                return $name;
        }

        // Singleton
        // Crea el objeto solo en printPreVista() tras llamar a fabricarVistaDecorada, el resto de llamadas devuelve la misma referencia
        // NOTA: es necesario que este aqui ya que el objeto vista no es persistente
        public function objetoJSON($arrJSONin = null) {
                static $arrJSONst = null;
                // echo "objetoJSON:";
                if ($arrJSONin != null) {
                        // echo "asigna otro";
                        // print_r($arrJSONin);
                        $arrJSONst = $arrJSONin;
                        return $arrJSONst;
                }

                if ($arrJSONst != null) {
                        // echo "obtiene antiguo";
                        // print_r($arrJSONst);
                        return $arrJSONst;
                }

                $arrJSONst = array();
                // echo "crea nuevo";
                return $arrJSONst;
        }

        function debug($param = NULL) {
                DirectorioRCJA::debug(($param ? $param : $this));
        }

}
?>
