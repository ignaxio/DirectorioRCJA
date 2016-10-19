<!-- SincronizacionConTercerosSistemas -->

<?
interface SincronizacionConTercerosSistemas {
	/* Este método implementará la funcionalidad específica necesaria en cada caso para establecer la conexión con el Tercer Sistema objeto de integración */ 
	public function conectar();
	
	/* En este método se realizarán las operaciones necesarias para poder obtener un listado de los Usuarios y Extensiones del Sistema con el que deseamos integrarnos */
	public function obtenerDatos();
	
	/* Mediante este método se almacenarán los datos recibidos del Tercer Sistema en un “formato común” (a priori, se almacenarán en una BBDD local, descartando otros formatos alternativos como XML, CSV,.. y realizando una separación de Usuarios, Extensiones y AsociacionesExt_Usu), para que éstos estén disponibles y puedan ser manipulados de manera homogénea */
	public function almacenarDatos();
	
	/* Nos devolverá una lista de Usuarios que están en el Tercer Sistema, y NO están en TAU (Estrictamente por "FULL-MATCHING" de todos los campos susceptibles de ser comparados) */
	public function obtenerUsuariosSoloTercerSistema();
	
	/* Este método devuelve una lista de Usuarios que están en TAU, y NO están en el Tercer Sistema (Estrictamente por "FULL-MATCHING" de todos los campos susceptibles de ser comparados) */
	public function obtenerUsuariosSoloTAU();
	
	/* Esta función retornará un Array Asociativo con una propuesta de Usuarios que están en ambos Sistemas (en TAU y en el Tercer Sistema) en base a determinados campos por los que hacer los emparejamientos o matching.
	   En caso de que el matching se produza sobre todos los campos objeto de emparejamiento, se incluirá en la key "FULL-MATCHING"
	   Por ejemplo: 
	   
	   array(2) {
		 ["DNI"] => array(3) {
				[0]=>array(11) {
					["Apellidos"]=>String(20) "CARRASCO SOLER"
					["Nombre"]=>String(20) "JUAN"
					...		
				}
				[1]=>...
				[2]=>...	
		 }
		 ["ApellidosyNombre"] => array(2) {
				[0]=>array(11) {
					["Apellidos"]=>String(20) "GARCIA CUMBRERAS"
					["Nombre"]=>String(20) "ALBERTO"
					...		
				}
				[1]=>...
		 }
		 ["FULL-MATCHING"] => array(8) {
				[0]=>array(11) {
					["Apellidos"]=>String(20) "GOMEZ ALCANTARA"
					["Nombre"]=>String(20) "EVA MARIA"
					...		
				}
				[2]=>...	
				[3]=>...
				[4]=>...	
				[5]=>...
				[6]=>...	
				[7]=>...
		 }
	   } 
	*/
	public function obtenerUsuariosAmbosSistemas();
	
	// CREAR TAMBIÉN MÉTODOS ANÁLOGOS PARA LOS MATCHINGS EXTENSIONES ENTRE AMBOS SISTEMAS
}
?>