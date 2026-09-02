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

	/*=============================================
	Editar una mesa
	=============================================*/

	public $idTable;
	public $titleTableEdit;
	public $iconTableEdit;
	public $peopleTableEdit;
	public $statusTableEdit;
	public $idOfficeTableEdit;
	public $tokenEdit;

	public function updateTable(){

		$url = "tables?id=".$this->idTable."&nameId=id_table&token=".$this->tokenEdit."&table=admins&suffix=admin";
		$method = "PUT";
		$fields = http_build_query(array(
			"title_table" => $this->titleTableEdit,
			"icon_table" => $this->iconTableEdit,
			"people_table" => $this->peopleTableEdit,
			"status_table" => $this->statusTableEdit,
			"id_office_table" => $this->idOfficeTableEdit,
		));

		$updateTable = CurlController::request($url,$method,$fields);

		if($updateTable->status == 200){

			echo 200;

		}else{

			echo $updateTable->status;
		}

	}

	/*=============================================
	Eliminar una mesa
	=============================================*/

	public $idTableDelete;
	public $tokenDelete;

	public function deleteTable(){

		$url = "tables?id=".$this->idTableDelete."&nameId=id_table&token=".$this->tokenDelete."&table=admins&suffix=admin";
		$method = "DELETE";
		$fields = "id_table=".$this->idTableDelete;

		$deleteTable = CurlController::request($url,$method,$fields);

		if($deleteTable->status == 200){

			echo 200;

		}else{

			echo $deleteTable->status;
		}

	}

}

/*=============================================
Variables POST
=============================================*/

if(isset($_POST["title_table"]) && !isset($_POST["id_table"])){

	$ajax = new MesasAjaxController();
	$ajax -> title_table = $_POST["title_table"];
	$ajax -> icon_table = $_POST["icon_table"];
	$ajax -> people_table = $_POST["people_table"];
	$ajax -> status_table = $_POST["status_table"];
	$ajax -> id_office_table = $_POST["id_office_table"];
	$ajax -> token = $_POST["token"];
	$ajax -> createTable();

}

if(isset($_POST["id_table"]) && isset($_POST["tokenEdit"])){

	$ajax = new MesasAjaxController();
	$ajax -> idTable = $_POST["id_table"];
	$ajax -> titleTableEdit = $_POST["title_table"];
	$ajax -> iconTableEdit = $_POST["icon_table"];
	$ajax -> peopleTableEdit = $_POST["people_table"];
	$ajax -> statusTableEdit = $_POST["status_table"];
	$ajax -> idOfficeTableEdit = $_POST["id_office_table"];
	$ajax -> tokenEdit = $_POST["tokenEdit"];
	$ajax -> updateTable();

}

if(isset($_POST["idTableDelete"]) && isset($_POST["tokenDelete"])){

	$ajax = new MesasAjaxController();
	$ajax -> idTableDelete = $_POST["idTableDelete"];
	$ajax -> tokenDelete = $_POST["tokenDelete"];
	$ajax -> deleteTable();

}