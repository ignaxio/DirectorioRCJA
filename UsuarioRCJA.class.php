<!-- UsuarioRCJA -->

<?
include_once PATH_MODULOS . "DirectorioRCJA/BusquedaAvanzadaAbstracta.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/VistaUsuarioRCJA.class.php";
include_once PATH_MODULOS . "Inventario2/Inventario2.class.php";

class UsuarioRCJA extends BusquedaAvanzadaAbstracta implements FabricarVista {

        var $name = __CLASS__;
        var $desc = "UsuarioRCJA";
        private $empleado = "";
        private $idTAU = "";
        private $nombre = "";
        private $NIF = "";
        private $perfilUsuario = "";
        private $etiquetaEmp = "";
        private $observacionesEmp = "";
        private $centroDirectivoDepart = "";
        private $centroTrabajo = "";
        private $puestoTrabajo = "";
        private $servicio = "";
        private $tipoUsuario = "";
        private $grupoNivel = "";
        // Hace global privadas la lista de campos y los criterios de ordenacion
        private $campos = array(1 => 'rcja_usuarios.empleado',
                2 => 'rcja_usuarios.nombre',
                3 => 'rcja_usuarios.nif',
                4 => 'rcja_usuarios.centro_Trabajo');
        private $ordenes = array(1 => 'ASC',
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
                return Array("empleado", "nombre", "nif", "perfil_Usuario", "etiqueta_Emp", "observaciones_Emp", "centro_Directivo_Depart", "centro_Trabajo", "puesto_Trabajo", "servicio", "tipo_Usuario", "grupo_Nivel");
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
                return new VistaUsuarioRCJA($this, $param, null);
        }

        public function fabricarVistaDecorada($param, $iter) {
                return new VistaUsuarioRCJA($this, $param, $iter);
        }

        ////
        //
	// funciones de acceso a las propiedades
        //
	////

        public function getEmpleado() {
                return $this->empleado;
        }

        public function setEmpleado($empleado) {
                $old = $this->empleado;
                $this->empleado = $empleado;
                return $old;
        }
        
        function getIdTAU() {
                return $this->idTAU;
        }

        function setIdTAU($idTAU) {
                $old = $this->idTAU;
                $this->idTAU = $idTAU;
                return $old;
        }

        
        public function getNombre() {
                return $this->nombre;
        }

        public function setNombre($nombre) {
                $old = $this->nombre;
                $this->nombre = $nombre;
                return $old;
        }

        public function getNIF() {
                return $this->NIF;
        }

        public function setNIF($NIF) {
                $old = $this->NIF;
                $this->NIF = substr($NIF, 0, 9); // coincide con la long del campo en la tabla
                return $old;
        }

        public function getPerfilUsuario() {
                return $this->perfilUsuario;
        }

        public function setPerfilUsuario($perfilUsuario) {
                $old = $this->perfilUsuario;
                $this->perfilUsuario = $perfilUsuario;
                return $old;
        }

        public function getEtiquetaEmp() {
                return $this->etiquetaEmp;
        }

        public function setEtiquetaEmp($etiquetaEmp) {
                $old = $this->etiquetaEmp;
                $this->etiquetaEmp = $etiquetaEmp;
                return $old;
        }

        public function getObservacionesEmp() {
                return $this->observacionesEmp;
        }

        public function setObservacionesEmp($observacionesEmp) {
                $old = $this->observacionesEmp;
                $this->observacionesEmp = $observacionesEmp;
                return $old;
        }

        public function getCentroDirectivoDepart() {
                return $this->centroDirectivoDepart;
        }

        public function setCentroDirectivoDepart($centroDirectivoDepart) {
                $old = $this->centroDirectivoDepart;
                $this->centroDirectivoDepart = $centroDirectivoDepart;
                return $old;
        }

        public function getCentroTrabajo() {
                return $this->centroTrabajo;
        }

        public function setCentroTrabajo($centroTrabajo) {
                $old = $this->centroTrabajo;
                $this->centroTrabajo = $centroTrabajo;
                return $old;
        }

        public function getPuestoTrabajo() {
                return $this->puestoTrabajo;
        }

        public function setPuestoTrabajo($puestoTrabajo) {
                $old = $this->puestoTrabajo;
                $this->puestoTrabajo = $puestoTrabajo;
                return $old;
        }

        public function getServicio() {
                return $this->servicio;
        }

        public function setServicio($servicio) {
                $old = $this->servicio;
                $this->servicio = $servicio;
                return $old;
        }

        public function getTipoUsuario() {
                return $this->tipoUsuario;
        }

        public function setTipoUsuario($tipoUsuario) {
                $old = $this->tipoUsuario;
                $this->tipoUsuario = $tipoUsuario;
                return $old;
        }

        public function getGrupoNivel() {
                return $this->grupoNivel;
        }

        public function setGrupoNivel($grupoNivel) {
                $old = $this->grupoNivel;
                $this->grupoNivel = $grupoNivel;
                return $old;
        }

        ////
        //
	// funciones de busca y seleccion
        //
	////

        public function busca() {
                $this->SQL = "SELECT rcja_usuarios.empleado, rcja_usuarios.nif, rcja_usuarios.nombre, ";
                $this->SQL .= "		 rcja_usuarios.perfil_Usuario, rcja_usuarios.etiqueta_Emp, rcja_usuarios.observaciones_Emp, rcja_usuarios.centro_Directivo_Depart, ";
                $this->SQL .= "		 rcja_usuarios.centro_Trabajo, rcja_usuarios.puesto_Trabajo, rcja_usuarios.servicio, rcja_usuarios.tipo_Usuario, rcja_usuarios.grupo_Nivel ";
                $this->SQL .= " FROM rcja_usuarios";
                //$this->debug($this->SQL);
                return $this->SQL;
        }

        public function buscaPorIdUsr($idusr) {
                $this->SQL = $this->busca();
                $this->SQL .= " WHERE rcja_usuarios.empleado = '{$idusr}'";
//		$this->debug($this->SQL);
                return $this->SQL;
        }

        public function buscaPorIdExtension($numeroLargo) {
                $this->SQL = $this->busca();
                $this->SQL .= ", rcja_extensiones, rcja_asociacionesExt_Usu WHERE (rcja_usuarios.empleado = rcja_asociacionesExt_Usu.empleado) AND (rcja_asociacionesExt_Usu.num_Largo = {$numeroLargo}) AND (rcja_asociacionesExt_Usu.num_Largo = rcja_extensiones.num_Largo);";
//		$this->debug($this->SQL);
                return $this->SQL;
        }

        public function buscaPorPatron($patron, $campo = 1, $orden = 1) { /* siempre ordena */
                $this->SQL = $this->busca();
                $this->SQL .= " WHERE (rcja_usuarios.nif Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.nombre Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.empleado Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.perfil_Usuario Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.observaciones_Emp Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.centro_Trabajo Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.servicio Like '%{$patron}%'  ";
                $this->SQL .= "    OR rcja_usuarios.tipo_Usuario Like '%{$patron}%')  ";
                $this->SQL .= " ORDER BY {$this->campos[$campo]} {$this->ordenes[$orden]}";
//		$this->debug($this->SQL);
                return $this->SQL;
        }
        
        public function buscaPorIdes($ids, $campo, $orden) {
                // Vamos ha buscar todos los usuarios que vengan en el arary.
                $this->SQL = "SELECT empleado, nif, nombre";
                $this->SQL .= " FROM rcja_usuarios";
                $contador = 0;
                foreach ($ids as $id) {
                        if($contador == 0) {
                                $this->SQL .= " WHERE empleado = '{$id['idRCJA']}'";
                        }
                        $this->SQL .= " OR empleado = '{$id['idRCJA']}'";
                        $contador++;
                }
                $this->SQL .= " ORDER BY {$this->campos[$campo]} {$this->ordenes[$orden]};";
//                $this->debug($this->SQL);
                return $this->SQL;
        }

        ////
        //
	// funciones de creacion y actualizacion
        //
	////

        public function crear($conn) {
                $this->SQL = "INSERT IGNORE INTO rcja_usuarios (";

                foreach ($this->listaCampos() as $campo) {
                        $this->SQL .= $campo . ", ";
                }

                // Eliminamos la subcadena final sobrante (", ")
                $this->SQL = substr($this->SQL, 0, -2);

                $this->SQL .= ") VALUES (";
                $this->SQL .= "         " . Inventario2::to_SQL($this->getEmpleado()) . ", " . Inventario2::to_SQL($this->getNombre()) . ", ";
                $this->SQL .= " 	    " . Inventario2::to_SQL($this->getNIF()) . ", " . Inventario2::to_SQL($this->getPerfilUsuario()) . ", ";
                $this->SQL .= " 	    " . Inventario2::to_SQL($this->getEtiquetaEmp()) . ", " . Inventario2::to_SQL($this->getObservacionesEmp()) . ", ";
                $this->SQL .= " 	    " . Inventario2::to_SQL($this->getCentroDirectivoDepart()) . ", " . Inventario2::to_SQL($this->getCentroTrabajo()) . ", ";
                $this->SQL .= " 	    " . Inventario2::to_SQL($this->getPuestoTrabajo()) . ", " . Inventario2::to_SQL($this->getServicio()) . ",";
                $this->SQL .= "		    " . Inventario2::to_SQL($this->getTipoUsuario()) . ", " . Inventario2::to_SQL($this->getGrupoNivel()) . ");";

                // Ejecutar la operación de inserción
                $resultado = TAU::ejecutarSQL($conn, $this->SQL);

                if (!$resultado) {
                        die('No se pudo completar la operación de insercion en la tabla rcja_usuarios: ' . odbc_errormsg());
                }

                return $this->getNIF();
        }

        public function getUsuarios($conn) {
                $SQL = "SELECT empleado, nombre, nif FROM rcja_usuarios";
                $empleados = array();
                $result = TAU::ejecutarSQL($conn, $SQL);
                while (odbc_fetch_row($result)) {
                        $empleados[odbc_result($result, 'empleado')] = array(
                                'nombre' => odbc_result($result, 'nombre'),
                                'nif' => odbc_result($result, 'nif')
                        );
                }
//                odbc_close($this->conn);
                return $empleados;
        }

        public function leerCampos($fila) {
                $this->setEmpleado(isset($fila["empleado"]) ? $fila["empleado"] : '?');
                $this->setIdTAU(isset($fila["id_Usuario"]) ? $fila["id_Usuario"] : '?');
                $this->setNombre(isset($fila["nombre"]) ? $fila["nombre"] : '?');
                $this->setNIF(isset($fila["nif"]) ? $fila["nif"] : '?');
                $this->setPerfilUsuario(isset($fila["perfil_Usuario"]) ? $fila["perfil_Usuario"] : '?');
                $this->setEtiquetaEmp(isset($fila["etiqueta_Emp"]) ? $fila["etiqueta_Emp"] : '?');
                $this->setObservacionesEmp(isset($fila["observaciones_Emp"]) ? $fila["observaciones_Emp"] : '?');
                $this->setCentroDirectivoDepart(isset($fila["centro_Directivo_Depart"]) ? $fila["centro_Directivo_Depart"] : '?');
                $this->setCentroTrabajo(isset($fila["centro_Trabajo"]) ? $fila["centro_Trabajo"] : '?');
                $this->setPuestoTrabajo(isset($fila["puesto_Trabajo"]) ? $fila["puesto_Trabajo"] : '?');
                $this->setServicio(isset($fila["servicio"]) ? $fila["servicio"] : '?');
                $this->setTipoUsuario(isset($fila["tipo_Usuario"]) ? $fila["tipo_Usuario"] : '?');
                $this->setGrupoNivel(isset($fila["grupo_Nivel"]) ? $fila["grupo_Nivel"] : '?');
        }

        function debug($param = NULL) {
                DirectorioRCJA::debug($param ? $param : $this);
        }

}
?>
