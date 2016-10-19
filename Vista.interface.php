<!-- Vista -->

<?
interface Vista {
	// Punto de entrada para imprimir cualquier Vista
	public function printVista($vista);
	
	// Miembros comunes de get/set: Objeto, Param e Iterador
	public function getObjeto();
	public function setObjeto($obj);
	public function getParam();
	public function setParam($param);
	public function getIterador();
	public function setIterador($iter);
}
?>