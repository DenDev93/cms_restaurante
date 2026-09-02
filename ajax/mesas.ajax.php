<?php

require_once "../controllers/curl.controller.php";

class MesasAjaxController{

	/*=============================================
	Crear nueva mesa
	=============================================*/

	public $title_table;
	public $icon_table;
	public $people_table;
	public $status_table;
	public $id_office_table;
	public $token;

	public function createTable(){

		$url = "tables?token=".$this->token."&table=admins&suffix=admin";
		$method = "POST";
		$fields = array(
			"title_table" => $this->title_table,
			"icon_table" => $this->icon_table,
			"people_table" => $this->people_table,
			"status_table" => $this->status_table,
			"id_office_table" => $this->id_office_table,
			"date_created_table" => date("Y-m-d")
		);

		$createTable = CurlController::request($url,$method,$fields);

		if($createTable->status == 200){

			echo 200;

		}else{

			echo $createTable->status;
		}

	}

}

/*=============================================
Variables POST
=============================================*/

if(isset($_POST["title_table"])){

	$ajax = new MesasAjaxController();
	$ajax -> title_table = $_POST["title_table"];
	$ajax -> icon_table = $_POST["icon_table"];
	$ajax -> people_table = $_POST["people_table"];
	$ajax -> status_table = $_POST["status_table"];
	$ajax -> id_office_table = $_POST["id_office_table"];
	$ajax -> token = $_POST["token"];
	$ajax -> createTable();

}