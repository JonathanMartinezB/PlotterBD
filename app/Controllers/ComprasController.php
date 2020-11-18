<?php

namespace App\Controllers;
require_once(__DIR__.'/../Models/Compras.php');
require_once(__DIR__.'/../Models/Usuarios.php');

use App\Models\GeneralFunctions;
use App\Models\Usuarios;
use App\Models\Compras;
use Carbon\Carbon;

if(!empty($_GET['action'])){
    ComprasController::main($_GET['action']);
}

class ComprasController{

    static function main($action)
    {
        if ($action == "create") {
            ComprasController::create();
        } else if ($action == "edit") {
            ComprasController::edit();
        } else if ($action == "searchForID") {
            ComprasController::searchForID($_REQUEST['idCompra']);
        } else if ($action == "searchAll") {
            ComprasController::getAll();
        } else if ($action == "activate") {
            ComprasController::activate();
        } else if ($action == "inactivate") {
            ComprasController::inactivate();
        }
    }

    static public function create()
    {
        try {
            $arrayCompra = array();
            $arrayCompra['numero_serie'] = 'FV'.'-'.date('Y-m-d');
            $arrayCompra['cliente_id'] = Usuarios::searchForId($_POST['cliente_id']);
            $arrayCompra['empleado_id'] = Usuarios::searchForId($_POST['empleado_id']);
            $arrayCompra['fecha_compra'] = Carbon::now(); //Fecha Completa Hoy
            $arrayCompra['monto'] = 0;
            $arrayCompra['estado'] = 'Activo';
            $Compra = new Compras($arrayCompra);
            if($Compra->create()){
                header("Location: ../../views/modules/compras/create.php?id=".$Compra->getId());
            }
        } catch (\Exception $e) {
            GeneralFunctions::console( $e, 'error', 'errorStack');
            header("Location: ../../views/modules/compras/create.php?respuesta=error&mensaje=" . $e->getMessage());
        }
    }

    static public function edit (){
        try {
            $arrayCompra = array();
            $arrayCompra['numero_serie'] = $_POST['numero_serie'];
            $arrayCompra['cliente_id'] = Usuarios::searchForId($_POST['cliente_id']);
            $arrayCompra['empleado_id'] = Usuarios::searchForId($_POST['empleado_id']);
            $arrayCompra['fecha_compra'] = $_POST['fecha_compra'];
            $arrayCompra['monto'] = $_POST['monto'];
            $arrayCompra['estado'] = $_POST['estado'];
            $arrayCompra['id'] = $_POST['id'];

            $Compra = new Compras ($arrayCompra);
            $Compra->update();

            header("Location: ../../views/modules/compras/show.php?id=".$Compra->getId()."&respuesta=correcto");
        } catch (\Exception $e) {
            GeneralFunctions::console( $e, 'error', 'errorStack');
            header("Location: ../../views/modules/compras/edit.php?respuesta=error&mensaje=".$e->getMessage());
        }
    }

    static public function activate (){
        try {
            $ObjCompra = Compras::searchForId($_GET['Id']);
            $ObjCompra->setEstado("Activo");
            if($ObjCompra->update()){
                header("Location: ../../views/modules/compras/index.php");
            }else{
                header("Location: ../../views/modules/compras/index.php?respuesta=error&mensaje=Error al guardar");
            }
        } catch (\Exception $e) {
            GeneralFunctions::console( $e, 'error', 'errorStack');
            header("Location: ../../views/modules/compras/index.php?respuesta=error&mensaje=".$e->getMessage());
        }
    }

    static public function inactivate (){
        try {
            $ObjCompra = Compras::searchForId($_GET['Id']);
            $ObjCompra->setEstado("Inactivo");
            if($ObjCompra->update()){
                header("Location: ../../views/modules/compras/index.php");
            }else{
                header("Location: ../../views/modules/compras/index.php?respuesta=error&mensaje=Error al guardar");
            }
        } catch (\Exception $e) {
            GeneralFunctions::console( $e, 'error', 'errorStack');
            header("Location: ../../views/modules/compras/index.php?respuesta=error");
        }
    }

    static public function searchForID ($id){
        try {
            return Compras::searchForId($id);
        } catch (\Exception $e) {
            GeneralFunctions::console( $e, 'error', 'errorStack');
            //header("Location: ../../views/modules/compras/manager.php?respuesta=error");
        }
    }

    static public function getAll (){
        try {
            return Compras::getAll();
        } catch (\Exception $e) {
            GeneralFunctions::console( $e, 'log', 'errorStack');
            header("Location: ../Vista/modules/persona/manager.php?respuesta=error");
        }
    }

    /*public static function personaIsInArray($idPersona, $ArrPersonas){
        if(count($ArrPersonas) > 0){
            foreach ($ArrPersonas as $Persona){
                if($Persona->getIdPersona() == $idPersona){
                    return true;
                }
            }
        }
        return false;
    }

    static public function selectPersona ($isMultiple=false,
                                          $isRequired=true,
                                          $id="idConsultorio",
                                          $nombre="idConsultorio",
                                          $defaultValue="",
                                          $class="",
                                          $where="",
                                          $arrExcluir = array()){
        $arrPersonas = array();
        if($where != ""){
            $base = "SELECT * FROM persona WHERE ";
            $arrPersonas = Persona::buscar($base.$where);
        }else{
            $arrPersonas = Persona::getAll();
        }

        $htmlSelect = "<select ".(($isMultiple) ? "multiple" : "")." ".(($isRequired) ? "required" : "")." id= '".$id."' name='".$nombre."' class='".$class."'>";
        $htmlSelect .= "<option value='' >Seleccione</option>";
        if(count($arrPersonas) > 0){
            foreach ($arrPersonas as $persona)
                if (!UsuariosController::personaIsInArray($persona->getIdPersona(),$arrExcluir))
                    $htmlSelect .= "<option ".(($persona != "") ? (($defaultValue == $persona->getIdPersona()) ? "selected" : "" ) : "")." value='".$persona->getIdPersona()."'>".$persona->getNombres()." ".$persona->getApellidos()."</option>";
        }
        $htmlSelect .= "</select>";
        return $htmlSelect;
    }*/

    /*
    public function buscar ($Query){
        try {
            return Persona::buscar($Query);
        } catch (Exception $e) {
            header("Location: ../Vista/modules/persona/manager.php?respuesta=error");
        }
    }

    static public function asociarEspecialidad (){
        try {
            $Persona = new Persona();
            $Persona->asociarEspecialidad($_POST['Persona'],$_POST['Especialidad']);
            header("Location: ../Vista/modules/persona/managerSpeciality.php?respuesta=correcto&id=".$_POST['Persona']);
        } catch (Exception $e) {
            header("Location: ../Vista/modules/persona/managerSpeciality.php?respuesta=error&mensaje=".$e->getMessage());
        }
    }

    static public function eliminarEspecialidad (){
        try {
            $ObjPersona = new Persona();
            if(!empty($_GET['Persona']) && !empty($_GET['Especialidad'])){
                $ObjPersona->eliminarEspecialidad($_GET['Persona'],$_GET['Especialidad']);
            }else{
                throw new Exception('No se recibio la informacion necesaria.');
            }
            header("Location: ../Vista/modules/persona/managerSpeciality.php?id=".$_GET['Persona']);
        } catch (Exception $e) {
            var_dump($e);
            //header("Location: ../Vista/modules/persona/manager.php?respuesta=error");
        }
    }

    public static function login (){
        try {
            if(!empty($_POST['Usuario']) && !empty($_POST['Contrasena'])){
                $tmpPerson = new Persona();
                $respuesta = $tmpPerson->Login($_POST['Usuario'], $_POST['Contrasena']);
                if (is_a($respuesta,"Persona")) {
                    $hydrator = new ReflectionHydrator(); //Instancia de la clase para convertir objetos
                    $ArrDataPersona = $hydrator->extract($respuesta); //Convertimos el objeto persona en un array
                    unset($ArrDataPersona["datab"],$ArrDataPersona["isConnected"],$ArrDataPersona["relEspecialidades"]); //Limpiamos Campos no Necesarios
                    $_SESSION['UserInSession'] = $ArrDataPersona;
                    echo json_encode(array('type' => 'success', 'title' => 'Ingreso Correcto', 'text' => 'Sera redireccionado en un momento...'));
                }else{
                    echo json_encode(array('type' => 'error', 'title' => 'Error al ingresar', 'text' => $respuesta)); //Si la llamda es por Ajax
                }
                return $respuesta; //Si la llamada es por funcion
            }else{
                echo json_encode(array('type' => 'error', 'title' => 'Datos Vacios', 'text' => 'Debe ingresar la informacion del usuario y contraseña'));
                return "Datos Vacios"; //Si la llamada es por funcion
            }
        } catch (Exception $e) {
            var_dump($e);
            header("Location: ../login.php?respuesta=error");
        }
    }

    public static function cerrarSession (){
        session_unset();
        session_destroy();
        header("Location: ../Vista/modules/persona/login.php");
    }*/

}