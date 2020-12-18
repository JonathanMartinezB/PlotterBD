<?php

namespace App\Models;

use App\Interfaces\Model;
use Carbon\Carbon;
use Exception;
use JsonSerializable;

class DetalleCompras extends AbstractDBConnection implements Model, JsonSerializable
{
    private ?int $id;
    private int $compras_id;
    private int $producto_id;
    private int $cantidad;
    private float $precio_compra;
    private Carbon $created_at;

    /* Relaciones */
    private ?Compras $compra;
    private ?Productos $producto;

    /**
     * Detalle Compra constructor. Recibe un array asociativo
     * @param array $detalle_compra
     */
    public function __construct(array $detalle_compra = [])
    {
        parent::__construct();
        $this->setId($detalle_compra['id'] ?? NULL);
        $this->setComprasId($detalle_compra['compra_id'] ?? 0);
        $this->setProductoId($detalle_compra['producto_id'] ?? 0);
        $this->setCantidad($detalle_compra['cantidad'] ?? 0);
        $this->setPrecioCompra($detalle_compra['precio_compra'] ?? 0.0);
        $this->setCreatedAt(!empty($categoria['created_at']) ? Carbon::parse($categoria['created_at']) : new Carbon());
    }

    /**
     *
     */
    function __destruct()
    {
        $this->Disconnect();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|mixed
     */
    public function getComprasId() : int
    {
        return $this->compras_id;
    }

    /**
     * @param int|mixed $compras_id
     */
    public function setComprasId(int $compras_id): void
    {
        $this->compras_id = $compras_id;
    }

    /**
     * @return int
     */
    public function getProductoId(): int
    {
        return $this->producto_id;
    }

    /**
     * @param int $producto_id
     */
    public function setProductoId(int $producto_id): void
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

    public function getTotalProducto() : float
    {
        return $this->getPrecioCompra() * $this->getCantidad();
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    /**
     * @param Carbon $created_at
     */
    public function setCreatedAt(Carbon $created_at): void
    {
        $this->created_at = $created_at;
    }

    /* Relaciones */
    /**
     * Retorna el objeto compra correspondiente al detalle compra
     * @return Compras|null
     */
    public function getCompra(): ?Compras
    {
        if(!empty($this->compras_id)){
            $this->compra = Compras::searchForId($this->compras_id) ?? new Compras();
            return $this->compra;
        }
        return NULL;
    }

    /**
     * Retorna el objeto producto correspondiente al detalle compra
     * @return Productos|null
     */
    public function getProducto(): ?Productos
    {
        if(!empty($this->producto_id)){
            $this->producto = Productos::searchForId($this->producto_id) ?? new Productos();
            return $this->producto;
        }
        return NULL;
    }

    protected function save(string $query, string $type = 'insert'): ?bool
    {
        if($type == 'deleted'){
            $arrData = [ ':id' =>   $this->getId() ];
        }else{
            $arrData = [
                ':id' =>   $this->getId(),
                ':compra_id' =>   $this->getComprasId(),
                ':producto_id' =>  $this->getProductoId(),
                ':cantidad' =>   $this->getCantidad(),
                ':precio_compra' =>   $this->getPrecioCompra(),
                ':created_at' =>  $this->getCreatedAt()->toDateTimeString(), //YYYY-MM-DD HH:MM:SS
            ];
        }

        $this->Connect();
        var_dump($arrData);
        $result = $this->insertRow($query, $arrData);
        $this->Disconnect();
        return $result;
    }

    function insert()
    {
        $query = "INSERT INTO plotter.detalle_compras VALUES (:id,:producto_id, :compra_id,:cantidad,:precio_compra,:created_at)";
        if($this->save($query)){
            return $this->getProducto()->substractStock($this->getCantidad());
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function update() : bool
    {
        $query = "UPDATE plotter.detalle_compras SET 
            compra_id = :compra_id, producto_id = :producto_id, cantidad = :cantidad, 
            precio_compra = :precio_compra, created_at = :created_at WHERE id = :id";
        return $this->save($query);
    }

    /**
     * @return mixed
     */
    public function deleted() : bool
    {
        $query = "DELETE FROM detalle_compras WHERE id = :id";
        return $this->save($query, 'deleted');
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function search($query) : ?array
    {
        try {
            $arrDetalleCompra = array();
            $tmp = new DetalleCompras();
            $tmp->Connect();
            $getrows = $tmp->getRows($query);
            $tmp->Disconnect();

            foreach ($getrows as $valor) {
                $DetalleCompra = new DetalleCompras($valor);
                array_push($arrDetalleCompra, $DetalleCompra);
                unset($DetalleCompra);
            }
            return $arrDetalleCompra;
        } catch (Exception $e) {
            GeneralFunctions::logFile('Exception',$e, 'error');
        }
        return NULL;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function searchForId($id) : ?DetalleCompras
    {
        try {
            if ($id > 0) {
                $DetalleCompra = new DetalleCompras();
                $DetalleCompra->Connect();
                $getrow = $DetalleCompra->getRow("SELECT * FROM plotter.detalle_compras WHERE id = ?", array($id));
                $DetalleCompra->Disconnect();
                return ($getrow) ? new DetalleCompras($getrow) : null;
            }else{
                throw new Exception('Id de detalle compra Invalido');
            }
        } catch (Exception $e) {
            GeneralFunctions::logFile('Exception',$e, 'error');
        }
        return NULL;
    }

    /**
     * @return mixed
     */
    public static function getAll() : array
    {
        return DetalleCompras::search("SELECT * FROM plotter.detalle_compras");
    }

    /**
     * @param $compra_id
     * @param $producto_id
     * @return bool
     */
    public static function productoEnFactura($compra_id,$producto_id): bool
    {
        $result = DetalleCompras::search("SELECT id FROM plotter.detalle_compras where compra_id = '" . $compra_id. "' and producto_id = '" . $producto_id. "'");
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
        return "Compra: ".$this->compra->getNumeroSerie().", Producto: ".$this->producto->getNombre().", Cantidad: $this->cantidad, Precio Compra: $this->precio_compra";
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize()
    {
        return [
            'compra_id' => $this->getCompra()->jsonSerialize(),
            'producto_id' => $this->getProducto()->jsonSerialize(),
            'cantidad' => $this->getCantidad(),
            'precio_compra' => $this->getPrecioCompra(),
            'created_at' => $this->getCreatedAt()->toDateTimeString(),
        ];
    }
}