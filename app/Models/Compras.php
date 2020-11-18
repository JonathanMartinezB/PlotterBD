<?php

namespace App\Models;

require_once (__DIR__ .'/../../vendor/autoload.php');
require_once ('Usuarios.php');
require_once('BasicModel.php');

use Carbon\Carbon;
use App\Models\Usuarios;

class Compras extends BasicModel
{
    private int $id;
    private string $numero_serie;
    private ?Usuarios $cliente_id;
    private ?Usuarios $empleado_id;
    private Carbon $fecha_compra;
    private float $monto;
    private string $estado;

    /**
     * Compras constructor.
     * @param int $id
     * @param string $numero_serie
     * @param Usuarios $cliente_id
     * @param Usuarios $empleado_id
     * @param Carbon $fecha_compra
     * @param float $monto
     * @param string $estado
     */
    public function __construct($compra = array())
    {
        parent::__construct();
        $this->id = $compra['id'] ?? 0;
        $this->numero_serie = $compra['numero_serie'] ?? '';
        $this->cliente_id = $compra['cliente_id'] ?? null;
        $this->empleado_id = $compra['empleado_id'] ?? null;
        $this->fecha_compra = $compra['fecha_compra'] ?? new Carbon();
        $this->monto = $compra['monto'] ?? 0.0;
        $this->estado = $compra['estado'] ?? '';
    }

    /**
     *
     */
    function __destruct()
    {
        $this->Disconnect();
    }

    /**
     * @return int|mixed
     * @return int|mixed
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int|mixed $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed|string
     */
    public function getNumeroSerie() : string
    {
        return $this->numero_serie;
    }

    /**
     * @param mixed|string $numero_serie
     */
    public function setNumeroSerie(string $numero_serie): void
    {
        $this->numero_serie = $numero_serie;
    }

    /**
     * @return Usuarios|mixed|null
     */
    public function getClienteId() : Usuarios
    {
        return $this->cliente_id;
    }

    /**
     * @param Usuarios|mixed|null $cliente_id
     */
    public function setClienteId(Usuarios $cliente_id): void
    {
        $this->cliente_id = $cliente_id;
    }

    /**
     * @return Usuarios|mixed|null
     */
    public function getEmpleadoId() : Usuarios
    {
        return $this->empleado_id;
    }

    /**
     * @param Usuarios|mixed|null $empleado_id
     */
    public function setEmpleadoId(Usuarios $empleado_id): void
    {
        $this->empleado_id = $empleado_id;
    }

    /**
     * @return Carbon|mixed
     */
    public function getFechaCompra() : Carbon
    {
        return $this->fecha_compra->locale('es');
    }

    /**
     * @param Carbon|mixed $fecha_compra
     */
    public function setFechaCompra(Carbon $fecha_compra): void
    {
        $this->fecha_compra = $fecha_compra;
    }

    /**
     * @return float|mixed
     */
    public function getMonto() : float
    {
        return $this->monto;
    }

    /**
     * @param float|mixed $monto
     */
    public function setMonto(float $monto): void
    {
        $this->monto = $monto;
    }

    /**
     * @return mixed|string
     */
    public function getEstado() : string
    {
        return $this->estado;
    }

    /**
     * @param mixed|string $estado
     */
    public function setEstado(float $estado): void
    {
        $this->estado = $estado;
    }

    /**
     * @return mixed
     */
    public function create() : bool
    {
        $result = $this->insertRow("INSERT INTO plotter.compras VALUES (NULL, ?, ?, ?, ?, ?, ?)", array(
                $this->numero_serie,
                $this->cliente_id->getId(),
                $this->empleado_id->getId(),
                $this->fecha_compra->toDateTimeString(), //YYYY-MM-DD HH:MM:SS
                $this->monto,
                $this->estado
            )
        );
        $this->setId(($result) ? $this->getLastId() : null);
        $this->Disconnect();
        return $result;
    }

    /**
     * @return mixed
     */
    public function update() : bool
    {
        $result = $this->updateRow("UPDATE plotter.compras SET numero_serie = ?, cliente_id = ?, empleado_id = ?, fecha_compra = ?, monto = ?, estado = ? WHERE id = ?", array(
                $this->numero_serie,
                $this->cliente_id->getId(),
                $this->empleado_id->getId(),
                $this->fecha_compra->toDateTimeString(),
                $this->monto,
                $this->estado,
                $this->id
            )
        );
        $this->Disconnect();
        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleted($id) : bool
    {
        $Compra = Compras::searchForId($id); //Buscando un usuario por el ID
        $Compra->setEstado("Inactivo"); //Cambia el estado del Usuario
        return $Compra->update();                    //Guarda los cambios..
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function search($query) : array
    {
        $arrCompras = array();
        $tmp = new Compras();
        $getrows = $tmp->getRows($query);

        foreach ($getrows as $valor) {
            $Compra = new Compras();
            $Compra->id = $valor['id'];
            $Compra->numero_serie = $valor['numero_serie'];
            $Compra->cliente_id = Usuarios::searchForId($valor['cliente_id']);
            $Compra->empleado_id = Usuarios::searchForId($valor['empleado_id']);
            $Compra->fecha_compra = Carbon::parse($valor['fecha_compra']);
            $Compra->monto = $valor['monto'];
            $Compra->estado = $valor['estado'];
            $Compra->Disconnect();
            array_push($arrCompras, $Compra);
        }

        $tmp->Disconnect();
        return $arrCompras;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function searchForId($id) : Compras
    {
        $Compra = null;
        if ($id > 0) {
            $Compra = new Compras();
            $getrow = $Compra->getRow("SELECT * FROM plotter.compras WHERE id =?", array($id));
            $Compra->id = $getrow['id'];
            $Compra->numero_serie = $getrow['numero_serie'];
            $Compra->cliente_id = Usuarios::searchForId($getrow['cliente_id']);
            $Compra->empleado_id = Usuarios::searchForId($getrow['empleado_id']);
            $Compra->fecha_compra = Carbon::parse($getrow['fecha_compra']);
            $Compra->monto = $getrow['monto'];
            $Compra->estado = $getrow['estado'];
        }
        $Compra->Disconnect();
        return $Compra;
    }

    /**
     * @return mixed
     */
    public static function getAll() : array
    {
        return Compras::search("SELECT * FROM plotter.compras");
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return "Numero Serie: $this->numero_serie, Cliente: $this->cliente_id->nombresCompletos(), Empleado: $this->empleado_id->nombresCompletos(), Fecha Compras: $this->fecha_compra->toDateTimeString(), Monto: $this->monto, Estado: $this->estado";
    }

}