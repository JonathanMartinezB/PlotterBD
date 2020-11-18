<?php

namespace App\Models;

use App\Controllers\DetalleComprasController;

require_once (__DIR__ .'/../../vendor/autoload.php');
require_once('BasicModel.php');

class DetalleCompras extends BasicModel
{
    private int $id;
    private Compras $compras_id;
    private Productos $producto_id;
    private int $cantidad;
    private float $precio_compra;

    /**
     * DetalleCompras constructor.
     * @param int $id
     * @param Compras $compras_id
     * @param Productos $producto_id
     * @param int $cantidad
     * @param float $precio_compra
     */
    public function __construct($compra = array())
    {
        parent::__construct();
        $this->id = $compra['id'] ?? 0;
        $this->compras_id = $compra['compras_id'] ?? new Compras();
        $this->producto_id = $compra['producto_id'] ?? new Productos();
        $this->cantidad = $compra['cantidad'] ?? 0;
        $this->precio_compra = $compra['precio_compra'] ?? 0.0;
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
     * @return Compras|mixed
     */
    public function getComprasId() : Compras
    {
        return $this->compras_id;
    }

    /**
     * @param Compras|mixed $compras_id
     */
    public function setComprasId(Compras $compras_id): void
    {
        $this->compras_id = $compras_id;
    }

    /**
     * @return Productos
     */
    public function getProductoId(): Productos
    {
        return $this->producto_id;
    }

    /**
     * @param Productos $producto_id
     */
    public function setProductoId(Productos $producto_id): void
    {
        $this->producto_id = $producto_id;
    }

    /**
     * @return int|mixed
     */
    public function getCantidad() : int
    {
        return $this->cantidad;
    }

    /**
     * @param int|mixed $cantidad
     */
    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return float|mixed
     */
    public function getPrecioCompra() : float
    {
        return $this->precio_compra;
    }

    /**
     * @param float|mixed $precio_compra
     */
    public function setPrecioCompra(float $precio_compra): void
    {
        $this->precio_compra = $precio_compra;
    }


    /**
     * @return mixed
     */
    public function create() : bool
    {
        $result = $this->insertRow("INSERT INTO plotter.detalle_compras VALUES (NULL, ?, ?, ?, ?)", array(
                $this->compras_id->getId(),
                $this->producto_id->getId(),
                $this->cantidad,
                $this->precio_compra
            )
        );
        $this->Disconnect();
        return $result;
    }

    /**
     * @return mixed
     */
    public function update() : bool
    {
        $result = $this->updateRow("UPDATE plotter.detalle_compras SET compras_id = ?, producto_id = ?, cantidad = ?, precio_compra = ? WHERE id = ?", array(
                $this->compras_id->getId(),
                $this->producto_id->getId(),
                $this->cantidad,
                $this->precio_compra,
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
        $DetalleCompra = DetalleCompras::searchForId($id); //Buscando un usuario por el ID
        $deleterow = $DetalleCompra->deleteRow("DELETE FROM detalle_compras WHERE id = ?", array($id));
        return $deleterow;                    //Guarda los cambios..
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function search($query) : array
    {
        $arrDetalleCompra = array();
        $tmp = new DetalleCompras();
        $getrows = $tmp->getRows($query);

        foreach ($getrows as $valor) {
            $DetalleCompra = new DetalleCompras();
            $DetalleCompra->id = $valor['id'];
            $DetalleCompra->compras_id = Compras::searchForId($valor['compras_id']);
            $DetalleCompra->producto_id = Productos::searchForId($valor['producto_id']);
            $DetalleCompra->cantidad = $valor['cantidad'];
            $DetalleCompra->precio_compra = $valor['precio_compra'];
            $DetalleCompra->Disconnect();
            if(count($getrows) == 1){ // Si solamente hay un registro encontrado devuelve este objeto y no un array
                return $DetalleCompra;
            }
            array_push($arrDetalleCompra, $DetalleCompra);
        }
        $tmp->Disconnect();
        return $arrDetalleCompra;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function searchForId($id) : DetalleCompras
    {
        $DetalleCompra = null;
        if ($id > 0) {
            $DetalleCompra = new DetalleCompras;
            $getrow = $DetalleCompra->getRow("SELECT * FROM plotter.detalle_compras WHERE id =?", array($id));
            $DetalleCompra->id = $getrow['id'];
            $DetalleCompra->compras_id = Compras::searchForId($getrow['compras_id']);
            $DetalleCompra->producto_id = Productos::searchForId($getrow['producto_id']);
            $DetalleCompra->cantidad = $getrow['cantidad'];
            $DetalleCompra->precio_compra= $getrow['precio_compra'];
        }
        $DetalleCompra->Disconnect();
        return $DetalleCompra;
    }

    /**
     * @return mixed
     */
    public static function getAll() : array
    {
        return DetalleCompras::search("SELECT * FROM plotter.detalle_compras");
    }

    /**
     * @param $nombres
     * @return bool
     */
    public static function productoEnFactura($producto_id): bool
    {
        $result = DetalleCompras::search("SELECT id FROM plotter.detalle_compras where producto_id = '" . $producto_id. "'");
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return "Compra: $this->compras_id->getNumeroSerie(), Producto: $this->producto_id->getNombres(), Cantidad: $this->cantidad, Precio Compra: $this->precio_compra";
    }
}