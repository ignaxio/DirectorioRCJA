<!-- BusquedaAvanzada -->

<?
interface BusquedaAvanzada {
	// Devuelve la lista de Tablas
	public function listaTablas();
	// Devuelve la lista de Campos
	public function listaCampos();
	// Devuelve la lista de Relaciones. Indexado por nombre de campo
	public function listaRelaciones();
}
?>