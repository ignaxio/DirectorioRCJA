<!-- BusquedaAvanzadaAbstracta -->

<?

include_once PATH_MODULOS . "DirectorioRCJA/BusquedaAvanzada.interface.php";

abstract class BusquedaAvanzadaAbstracta implements BusquedaAvanzada {
	
	////
	//
	// Implementacion de BusquedaAvanzada
	//
	////
	
 	public function listaTablas() {
 		return Array("Usuario",
 					 "Equipo",
 					 "Puesto",
 					 "Perfil");
 	}
}
?>