<!-- SincronizacionDirectorioRCJA -->

<?
//include_once PATH_MODULOS . "DirectorioRCJA/XXXX.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/SincronizacionConTercerosSistemas.interface.php";
include_once PATH_MODULOS . "DirectorioRCJA/WS/WSDirectorioRCJA.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/UsuarioRCJA.class.php";
include_once PATH_MODULOS . "DirectorioRCJA/ExtensionRCJA.class.php";
include_once PATH_MODULOS . "Inventario2/Usuarios.class.php";

class SincronizacionDirectorioRCJA implements SincronizacionConTercerosSistemas {

        var $name = __CLASS__;
        var $desc = "Sincronizacion Directorio RCJA - TAU";
        private $conn;
        private $usuariosTAU = array();
        private $usuariosRCJA = array();
        protected $miWSDirectorioRCJA;
        protected $arrayDatosLineasYEmpleados;

        function __construct() {
                // Abrimos la conexión con la BBDD de TAU
                $this->conn = TAU::conectaDB();
        }

        public function conectar() {
                ini_set("memory_limit", "-1");
                ini_set("max_execution_time", "0");

                $this->miWSDirectorioRCJA = new WSDirectorioRCJA();
        }

        public function obtenerDatos() {
                // Parámetros de Entrada del método "ConsultarDatosLineaEmpleado"
                $unidadOrganizativa = 'CEICE';
                //$unidadOrganizativa = 'CEC';
                $tecnologia = 'F';
                //$tecnologia = 'M';
                // Invocamos al método de "_consultarDatosLineaEmpleado" de la clase WSDirectorioRCJA (el cual ejecutará, a su vez, 
                // el método ConsultarDatosLineaEmpleado($parametros) del WS y parseará el resultado retornándonos un array con
                // dos componentes: datosLineas[] y datosEmpleados[], que son los arrays que contienen ya los datos que necesitamos)
                $this->arrayDatosLineasYEmpleados = $this->miWSDirectorioRCJA->consultarDatosLineaEmpleado($unidadOrganizativa, $tecnologia);
                //var_dump($this->arrayDatosLineasYEmpleados);
        }

        public function almacenarDatos() {

                // NOTA 1: Aunque inicialmente se estableció como campo clave el nif, al ver que existen registros (un total de 10 en el entorno de pruebas) que vienen con el campo NIF vacío, se opta por establecer como clave primaria el campo 'empleado', pues tan sólo hay una ocurrencia en la cual dicho campo esté vacío
                // NOTA 2: Aunque inicialmente se estableció como de tipo 'INT(3)' el campo grupo_Nivel, se observó con posterioridad que venían registros vacíos para dicho campo, lo cual provacaba errores en la inserción, al no aceptarse el valor '' para un campo de dicho tipo. Por ello, finalmente, se estableció el tipo de dicho campo como "VARCHAR (3)"
                $sentenciaCreateTableUsuario = "CREATE TABLE RCJA_Usuarios (
                                                        empleado VARCHAR(40) PRIMARY KEY,
                                                        nombre VARCHAR(140),
                                                        nif VARCHAR(9),
                                                        perfil_Usuario VARCHAR(25),
                                                        etiqueta_Emp VARCHAR(25),
                                                        observaciones_Emp VARCHAR(25),
                                                        centro_Directivo_Depart VARCHAR(50),
                                                        centro_Trabajo VARCHAR(50),
                                                        puesto_Trabajo VARCHAR(50),
                                                        servicio VARCHAR(50),
                                                        tipo_Usuario VARCHAR(10),
                                                        grupo_Nivel VARCHAR(3)
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";

                // NOTA: Se define el tipo del campo "num_Corto" como VARCHAR(9) en lugar de como INT(6) porque puede venir el valor literal 'NO APLICA' en el WS de DirectorioRCJA		
                $sentenciaCreateTableExtensiones = "CREATE TABLE RCJA_Extensiones (
                                                        num_Largo INT(9) PRIMARY KEY,
                                                        num_Corto VARCHAR(12),
                                                        tipo VARCHAR(6),
                                                        perfil_Terminal VARCHAR(15),
                                                        sede VARCHAR(20),
                                                        uso_Linea VARCHAR(12),
                                                        privado VARCHAR(5),
                                                        etiqueta_Ln VARCHAR(9),
                                                        observaciones_Ln VARCHAR(35),
                                                        uso_Especial VARCHAR(20),
                                                        asignacion_Especial VARCHAR(30)
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";

                $sentenciaCreateTableAsociacionesExt_Usu = "CREATE TABLE RCJA_AsociacionesExt_Usu (
                                                        num_Largo INT(9) NOT NULL,
                                                        empleado VARCHAR(40) NOT NULL,
                                                        PRIMARY KEY(num_Largo, empleado)
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";

                // En primer lugar, eliminamos las tablas (si ya existían) y, posteriormente, creamos las tablas vacías
                if ($this->existeTabla('RCJA_Usuarios')) {
                        $borrada = $this->borrarTabla('RCJA_Usuarios');
                        if ($borrada) {
                                $this->crearTabla($sentenciaCreateTableUsuario);
                        }
                } else {
                        $this->crearTabla($sentenciaCreateTableUsuario);
                }

                if ($this->existeTabla('RCJA_Extensiones')) {
                        $borrada = $this->borrarTabla('RCJA_Extensiones');
                        if ($borrada) {
                                $this->crearTabla($sentenciaCreateTableExtensiones);
                        }
                } else {
                        $this->crearTabla($sentenciaCreateTableExtensiones);
                }

                if ($this->existeTabla('RCJA_AsociacionesExt_Usu')) {
                        $borrada = $this->borrarTabla('RCJA_AsociacionesExt_Usu');
                        if ($borrada) {
                                $this->crearTabla($sentenciaCreateTableAsociacionesExt_Usu);
                        }
                } else {
                        $this->crearTabla($sentenciaCreateTableAsociacionesExt_Usu);
                }

                // A continuación, procedemos a realizar las inserciones oportunas de los datos rescatados del Web Services de 'consultarDatosLineaEmpleado()'
                // Recorremos el array de datos, y vamos creando las sentencias "INSERT INTO RCJA_XXX VALUES ()":
                /* 		
                  echo "\n\nRESULTADOS: \n";

                  echo "\n\nNUMERO TOTAL DE LINEAS = " . count($this->arrayDatosLineasYEmpleados["datosLineas"]) . "\n\n";

                  $i = 1;
                  foreach ($this->arrayDatosLineasYEmpleados["datosLineas"] as $linea) {
                  echo "DATOS DE LA LINEA NUMERO " . $i++ . ":\n";
                  echo "Numero Largo: " . $linea["numLargo"] . "\n";
                  echo "Numero Corto: " . $linea["numCorto"] . "\n";
                  echo "Tipo: " . $linea["tipo"] . "\n";
                  echo "Perfil Terminal: " . $linea["perfilTerminal"] . "\n";
                  echo "Sede: " . $linea["sede"] . "\n";
                  echo "Uso Linea: " . $linea["usoLinea"] . "\n";
                  echo "Privado: " . $linea["privado"] . "\n";
                  echo "etiquetaLn: " . $linea["etiquetaLn"] . "\n";
                  echo "observacionesLn: " . $linea["observacionesLn"] . "\n";
                  echo "usoEspecial: " . $linea["usoEspecial"] . "\n";
                  echo "asignacionEspecial: " . $linea["asignacionEspecial"] . "\n";

                  echo "NUMERO TOTAL DE EMPLEADOS ASOCIADOS A LA LINEA=" . count($linea["datosEmpleados"]) . "\n\n";

                  $j = 1;
                  foreach ($linea["datosEmpleados"] as $empleado) {
                  echo "DATOS DEL EMPLEADO NUMERO " . $j++ . ":\n";
                  echo "Empleado: " . $empleado["empleado"] . "\n";
                  echo "Nombre: " . $empleado["nombre"] . "\n";
                  echo "NIF: " . $empleado["nif"] . "\n";
                  echo "Perfil Usuario: " . $empleado["perfilUsuario"] . "\n";
                  echo "etiquetaEmp: " . $empleado["etiquetaEmp"] . "\n";
                  echo "observacionesEmp: " . $empleado["observacionesEmp"] . "\n";
                  echo "Centro Directivo Departamento: " . $empleado["centroDirectivoDepart"] . "\n";
                  echo "Centro de Trabajo: " . $empleado["centroTrabajo"] . "\n";
                  echo "Puesto de Trabajo: " . $empleado["puestoTrabajo"] . "\n";
                  echo "Servicio: " . $empleado["servicio"] . "\n";
                  echo "Tipo de Usuario: " . $empleado["tipoUsuario"] . "\n";
                  echo "Grupo Nivel: " . $empleado["grupoNivel"] . "\n\n";
                  }
                  }
                 */

                foreach ($this->arrayDatosLineasYEmpleados["datosLineas"] as $linea) {

//			$sentenciaInsertTableExtensiones = "INSERT INTO RCJA_Extensiones (num_Largo, num_Corto, tipo, perfil_Terminal, sede, uso_Linea, privado, etiqueta_Ln, observaciones_Ln, uso_Especial, asignacion_Especial) VALUES (";
//			$sentenciaInsertTableExtensiones = $sentenciaInsertTableExtensiones . $linea["numLargo"] . ", '" . $linea["numCorto"] . "', '" . $linea["tipo"] . "', '" . $linea["perfilTerminal"] . "', '" . $linea["sede"] . "', '" . $linea["usoLinea"] . "', '" . $linea["privado"] . "', '" . $linea["etiquetaLn"] . "', '" . $linea["observacionesLn"] . "', '" . $linea["usoEspecial"] . "', '" . $linea["asignacionEspecial"] . "'";
//			$sentenciaInsertTableExtensiones = $sentenciaInsertTableExtensiones . ");";
                        //echo $sentenciaInsertTableExtensiones;
                        // Ejecutar la operación de inserción
//			$resultadoInsertTableExtensiones = @odbc_exec($this->conn, $sentenciaInsertTableExtensiones);
//			if (!$resultadoInsertTableExtensiones) {
                        //die('No se pudo realizar la inserción en la tabla RCJA_Extensiones: ' . odbc_errormsg());
                        //echo 'No se pudo realizar la inserción en la tabla RCJA_Extensiones: ' . odbc_errormsg();
//			}
                        // Creamos un nuevo objeto de la clase ExtesionRCJA
                        $nuevaExtesionRCJA = new ExtensionRCJA();
                        $nuevaExtesionRCJA->setNumeroLargo($linea["numLargo"]);
                        $nuevaExtesionRCJA->setNumeroCorto($linea["numCorto"]);
                        $nuevaExtesionRCJA->setTipo($linea["tipo"]);
                        $nuevaExtesionRCJA->setPerfilTerminal($linea["perfilTerminal"]);
                        $nuevaExtesionRCJA->setSede($linea["sede"]);
                        $nuevaExtesionRCJA->setUsoLinea($linea["usoLinea"]);
                        $nuevaExtesionRCJA->setPrivado($linea["privado"]);
                        $nuevaExtesionRCJA->setEtiquetaLinea($linea["etiquetaLn"]);
                        $nuevaExtesionRCJA->setObservacionesLinea($linea["observacionesLn"]);
                        $nuevaExtesionRCJA->setUsoEspecial($linea["usoEspecial"]);
                        $nuevaExtesionRCJA->setAsignacionEspecial($linea["asignacionEspecial"]);

                        // Creamos un nuevo objeto de la clase ExtesionRCJA y ejecutamos la operación de inserción en la BBDD de dicha ExtesionRCJA
                        $nuevaExtesionRCJA->crear($this->conn);

                        //echo "ResultadoInsertTableExtensiones=";
                        //var_dump($resultadoInsertTableExtensiones);
                        //echo "NUMERO TOTAL DE EMPLEADOS ASOCIADOS A LA LINEA=" . count($linea["datosEmpleados"]) . "\n\n";

                        foreach ($linea["datosEmpleados"] as $empleado) {
                                $sentenciaInsertTableAsociacionesExt_Usu = "INSERT INTO RCJA_AsociacionesExt_Usu (num_Largo, empleado) VALUES (";
                                $sentenciaInsertTableAsociacionesExt_Usu = $sentenciaInsertTableAsociacionesExt_Usu . $linea["numLargo"] . ", '" . $empleado["empleado"] . "'";
                                $sentenciaInsertTableAsociacionesExt_Usu = $sentenciaInsertTableAsociacionesExt_Usu . ");";

                                //echo $sentenciaInsertTableAsociacionesExt_Usu;
                                // Ejecutar la operación de inserción
                                $resultadoInsertTableAsociacionesExt_Usu = @odbc_exec($this->conn, $sentenciaInsertTableAsociacionesExt_Usu);

                                if (!$resultadoInsertTableAsociacionesExt_Usu) {
                                        //die('No se pudo realizar la inserción en la tabla RCJA_AsociacionesExt_Usu: ' . odbc_errormsg());
                                        //echo 'No se pudo realizar la inserción en la tabla RCJA_AsociacionesExt_Usu: ' . odbc_errormsg();
                                }

                                //echo "ResultadoInsertTableAsociacionesExt_Usu=";
                                //var_dump($resultadoInsertTableAsociacionesExt_Usu);
                                // Creamos un nuevo objeto de la clase UsuarioRCJA
                                $nuevoUsuarioRCJA = new UsuarioRCJA();
                                $nuevoUsuarioRCJA->setEmpleado($empleado["empleado"]);
                                $nuevoUsuarioRCJA->setNombre($empleado["nombre"]);
                                $nuevoUsuarioRCJA->setNIF($empleado["nif"]);
                                $nuevoUsuarioRCJA->setPerfilUsuario($empleado["perfilUsuario"]);
                                $nuevoUsuarioRCJA->setEtiquetaEmp($empleado["etiquetaEmp"]);
                                $nuevoUsuarioRCJA->setObservacionesEmp($empleado["observacionesEmp"]);
                                $nuevoUsuarioRCJA->setCentroDirectivoDepart($empleado["centroDirectivoDepart"]);
                                $nuevoUsuarioRCJA->setCentroTrabajo($empleado["centroTrabajo"]);
                                $nuevoUsuarioRCJA->setPuestoTrabajo($empleado["puestoTrabajo"]);
                                $nuevoUsuarioRCJA->setServicio($empleado["servicio"]);
                                $nuevoUsuarioRCJA->setTipoUsuario($empleado["tipoUsuario"]);
                                $nuevoUsuarioRCJA->setGrupoNivel($empleado["grupoNivel"]);

                                // Creamos un nuevo objeto de la clase UsuarioRCJA y ejecutamos la operación de inserción en la BBDD de dicho UsuarioRCJA
                                $nuevoUsuarioRCJA->crear($this->conn);
                        }
                }

                // Finalmente, cerramos la conexión con la BBDD de TAU
                DirectorioRCJA::desconexionDB($this->conn);
        }

        /**
         * Función que devuleve los usuarios que hay en la base de datos de RCJA y no estan en TAU.
         * Para identificar que usuarios están en los dos sistemas checkea que los los ocho dígitos del DNIs sean iguales 
         * y que el nombre y el apellido también sea igual.
         * 
         * @return array
         */
        public function obtenerUsuariosSoloTercerSistema() {
                // Aquí hay que coger todos los usuarios y almacenarlos en dos arrays
                $this->getUsuarioRCJA();
                $this->getUsuariosTAU();

                $usuariosSoloenRCJA = array();
                foreach ($this->usuariosRCJA as $keyUsuarioRCJA => $usuarioRCJA) {
                        $doble = false;
                        foreach ($this->usuariosTAU as $keyUsuarioTAU => $usuarioTAU) {

                                // Primero buscamos los fullMatching
                                if ($this->checkUsuariosConFullMatching($usuarioTAU, $usuarioRCJA)) {
                                        // Si son iguales no nos vale, marcamos como doble y cuando terminemos el boocle
                                        // checkeamos si se ha encontrado el doble.
                                        $doble = true;
                                }
                        }
                        // Si no hemos encontrado doble, lo metemos en el array.
                        if (!$doble) {
                                // Este usuario de RCJA no está en tau
                                $usuariosSoloenRCJA[$usuarioRCJA->getEmpleado()] = $usuarioRCJA;
                        }
                }
                return $usuariosSoloenRCJA;
        }

        /**
         * Función que devuleve los usuarios que hay en la base de datos de tau y no estan en RCJA
         * Para identificar que usuarios están en los dos sistemas checkea que los los ocho dígitos del DNIs sean iguales 
         * y que el nombre y el apellido también sea igual.
         * 
         * @return array
         */
        public function obtenerUsuariosSoloTAU() {
                // Aquí hay que coger todos los usuarios y almacenarlos en dos arrays
                $this->getUsuarioRCJA();
                $this->getUsuariosTAU();

                $usuariosSoloenTau = array();
                foreach ($this->usuariosTAU as $keyUsuarioTAU => $usuarioTAU) {
                        $doble = false;
                        foreach ($this->usuariosRCJA as $keyUsuarioRCJA => $usuarioRCJA) {

                                // Primero buscamos los fullMatching
                                if ($this->checkUsuariosConFullMatching($usuarioTAU, $usuarioRCJA)) {
                                        // Si son iguales no nos vale, marcamos como doble y cuando terminemos el boocle
                                        // checkeamos si se ha encontrado el doble.
                                        $doble = true;
                                }
                        }
                        // Si no hemos encontrado doble, lo metemos en el array.
                        if (!$doble) {
                                // Este usuario de tau no está en RCJA
                                $usuariosSoloenTau[$usuarioTAU->getID()] = $usuarioTAU;
                        }
                }
                return $usuariosSoloenTau;
        }

        /**
         * Función que devuelve un array con tres grupos.
         * Usuarios con nombre y apellidos similares dependiendo del porcentaje pasado como parámetro
         * Usuarios con DNI similares dependiendo del porcentaje pasado como parámetro
         * Usuarios Full Matching, los que coinciden exactamente el DNI, el nombre y los apellidos
         * 
         * @return array
         */
        public function obtenerUsuariosAmbosSistemas() {
                // Aquí hay que coger todos los usuarios y almacenarlos en dos arrays
                $this->getUsuarioRCJA();
                $this->getUsuariosTAU();

                $usuariosNombreApellidosSimilar = array();
                $usuariosDniSimilar = array();
                $usuariosFullMatching = array();
                foreach ($this->usuariosTAU as $keyUsuarioTAU => $usuarioTAU) {
                        foreach ($this->usuariosRCJA as $keyUsuarioRCJA => $usuarioRCJA) {
                                // Primero buscamos los fullMatching
                                if ($this->checkUsuariosConFullMatching($usuarioTAU, $usuarioRCJA)) {
                                        $usuariosFullMatching[$usuarioTAU->getID()] = array(
                                                'usuarioTAU' => $usuarioTAU,
                                                'usuarioRCJA' => $usuarioRCJA
                                        );
                                        // Saltamos al siguiente usuario para que no analice en los siguientes pasos  
                                        break;
                                }
                                // Ahora los que tienen un DNI similar o igual
                                if ($this->checkUsuariosDniSimilar($usuarioTAU, $usuarioRCJA, 80)) {
                                        $usuariosDniSimilar[$usuarioTAU->getID()] = array(
                                                'usuarioTAU' => $usuarioTAU,
                                                'usuarioRCJA' => $usuarioRCJA
                                        );
                                }
                                // Ahora los que tienen un nombre y apellidos similares o iguales
                                if ($this->checkUsuariosNombreApellidosSimilar($usuarioTAU, $usuarioRCJA, 80)) {
                                        $usuariosNombreApellidosSimilar[$usuarioTAU->getID()] = array(
                                                'usuarioTAU' => $usuarioTAU,
                                                'usuarioRCJA' => $usuarioRCJA
                                        );
                                }
                        }
                }
                return array(
                        'DNI' => $usuariosDniSimilar,
                        'ApellidosyNombre' => $usuariosNombreApellidosSimilar,
                        'FULL-MATCHING' => $usuariosFullMatching
                );
        }

        /**
         * Función que inicia una conexión a la Base de Datos
         */
        private function iniciarDBConexion() {
                $this->conn = TAU::conectaDBMysql();
                mysql_select_db('TAU');
        }

        /**
         * Función que cierra y libera una conexión a la Base de Datos
         * @param String $result
         */
        private function cerrarDBConexion($result) {
                //Liberamos la consulta
                @mysql_free_result($result);
                // Cerrar la conexión
                mysql_close($this->conn);
        }
        
        public function prueba() {
                $usuarios = new UsuariosRCJA();
                $SQLusuariosRCJA = $usuarios->busca();
                $resultado = TAU::ejecutarSQL($this->conn, $SQLusuariosRCJA);
                
                
                
                
                print_r($resultado);
        }

        /**
         * Función que carga en el array usuariosRCJA los usuarios de este tipo
         */
        private function getUsuarioRCJA() {
                $this->iniciarDBConexion();

                // Recogemos los usuarios de RCJA que tenemos en la Base de Datos
                $usuarios = new UsuariosRCJA();
                $SQLusuariosRCJA = $usuarios->busca();
                $result = mysql_query($SQLusuariosRCJA);
                while ($row = mysql_fetch_array($result)) {
                        $usuarioRCJA = new UsuarioRCJA();
                        // Metemos los datos en la clase
                        $usuarioRCJA->leerCampos($row);
                        // Vamos llenando el array
                        $this->usuariosRCJA[] = $usuarioRCJA;
                }
                $this->cerrarDBConexion($result);
        }

        /**
         * Función que carga en el array usuariosTAU los usuarios de este tipo
         */
        private function getUsuariosTAU() {
                $this->iniciarDBConexion();

                // Ahora los usuarios de TAU  que están en la Base de Datos "inventario".
                mysql_select_db('inventario');
                $usuarios = new Usuarios();
                $SQLusuarios = $usuarios->busca();
                // Conectamos a BBDD
                $result = mysql_query($SQLusuarios);
                while ($row = @mysql_fetch_array($result)) {
                        $usuarioTAU = new Usuario();
                        // Metemos los datos en la clase
                        $usuarioTAU->leerCampos($row);
                        // Vamos llenando el array
                        $this->usuariosTAU[] = $usuarioTAU;
                }
                $this->cerrarDBConexion($result);
        }

        /**
         * Función que analiza los nombres y apellidos de los usuarios para devolver los similares según el porcentaje pasado
         * @param Usuario $usuarioTAU
         * @param UsuarioRCJA $usuarioRCJA
         * @param int $porcentajeRequerido
         * @return boolean
         */
        private function checkUsuariosNombreApellidosSimilar($usuarioTAU, $usuarioRCJA, $porcentajeRequerido) {
                $percent = 0;
                // Vamos a montar los nombres y apellidos y compararlos...
                $nombreTAU = trim($usuarioTAU->getNombre()) . ' ' . trim($usuarioTAU->getApellidos());
                similar_text($nombreTAU, trim($usuarioRCJA->getNombre()), $percent);
                return ($porcentajeRequerido <= $percent) ? true : false;
        }

        /**
         * Función que analiza dos usuarios para hallar aquellos que tengan un DNI parecido dentro de un porcentaje de semejanza.
         * @param Usuario $usuarioTAU
         * @param UsuarioRCJA $usuarioRCJA
         * @param int $porcentajeRequerido
         * @return boolean
         */
        private function checkUsuariosDniSimilar($usuarioTAU, $usuarioRCJA, $porcentajeRequerido) {
                $percent = 0;
                similar_text($this->getNumerosDNI($usuarioTAU->getDNI()), $this->getNumerosDNI($usuarioRCJA->getNIF()), $percent);
                return ($porcentajeRequerido <= $percent) ? true : false;
        }

        /**
         * Función que analiza dos usuarios para hallar aquellos que tengan el mismo DNI y el mismo nombre y apellidos.       
         * @param Usuario $usuarioTAU
         * @param UsuarioRCJA $usuarioRCJA
         * @return boolean
         */
        private function checkUsuariosConFullMatching($usuarioTAU, $usuarioRCJA) {
                if ($this->getNumerosDNI($usuarioTAU->getDNI()) == $this->getNumerosDNI($usuarioRCJA->getNIF())) {
                        // Buscamos por nomobre y apellidos.
                        $nombreTAU = trim($usuarioTAU->getNombre()) . ' ' . trim($usuarioTAU->getApellidos());
                        return $nombreTAU == trim($usuarioRCJA->getNombre()) ? true : false;
                }
                return false;
        }

        /**
         * Función que analiza un número de DNI quitandole la letra y añadiendo los ceros delante que necesite 
         * hasta completar los ocho dígitos.
         * @param string $dni
         * @return string
         */
        private function getNumerosDNI($dni) {
                $cadena = $dni;
                // En primer lugar, eliminamos la última letra si es un string
                if (!is_numeric(substr($dni, -1))) {
                        $cadena = substr($dni, 0, -1);
                }
                // Ahora hay que contar los números para que haya 8, si no es así, hay que añadirle "0" delante hasta completar 8 dígitos
                switch (strlen($cadena)) {
                        case 8:
                                $cadena = '' . $cadena;
                                break;
                        case 7:
                                $cadena = '0' . $cadena;
                                break;
                        case 6:
                                $cadena = '00' . $cadena;
                                break;
                        case 5:
                                $cadena = '000' . $cadena;
                                break;
                        case 4:
                                $cadena = '0000' . $cadena;
                                break;
                        case 3:
                                $cadena = '00000' . $cadena;
                                break;
                        case 2:
                                $cadena = '000000' . $cadena;
                                break;
                        case 1:
                                $cadena = '0000000' . $cadena;
                                break;
                        case 0:
                                $cadena = '00000000' . $cadena;
                                break;
                }
                return $cadena;
        }

        /* Esta es la operación que, de forma atómica, se invocará desde la interfaz por parte del usuario */

        public function sincronizar() {
                $sincronizacionConRCJA = new SincronizacionDirectorioRCJA();
                $sincronizacionConRCJA->conectar();
                $sincronizacionConRCJA->obtenerDatos();
                $sincronizacionConRCJA->almacenarDatos();
        }

        public function existeTabla($tabla) {
                $query = "SELECT COUNT(*) as TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'tau' AND TABLE_NAME = '$tabla'";

                // Ejecutar la consulta
                $resultado = TAU::ejecutarSQL($this->conn, $query);

                if (!$resultado) {
                        die('No se pudo verificar la existencia de la tabla $tabla: ' . odbc_errormsg());
                }

                $totalRegistros = odbc_result($resultado, 1);
                return ($totalRegistros > 0);
        }

        public function borrarTabla($tabla) {
                $delete = "DROP TABLE IF EXISTS $tabla";

                // Ejecutar la operación de borrado
                $resultado = TAU::ejecutarSQL($this->conn, $delete);

                if (!$resultado) {
                        die('No se pudo eliminar la tabla $tabla: ' . odbc_errormsg());
                }

                //echo "Tabla $tabla eliminada correctamente<BR>";

                return $resultado;
        }

        public function crearTabla($sentenciaCreacion) {

                // Ejecutar la operación de creación
                $resultado = TAU::ejecutarSQL($this->conn, $sentenciaCreacion);

                if (!$resultado) {
                        die('No se pudo crear la tabla: ' . odbc_errormsg());
                }

                //echo "Tabla creada correctamente\n";

                return $resultado;
        }

}
?>	
