<?php
namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estado
 * @ORM\Table(name="documento")
 * @ORM\Entity
 */
class Documento
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;   
   
   
    /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_id", referencedColumnName="id")
     */
    private $cuentaid;
    
    
    /**
     * @var decimal
     * @ORM\Column(name="valor",type="decimal",scale=2,nullable=true)
    */
    private $valor;
    
    
     /**
     * @var datetime
     * @ORM\Column(name="fechaEmision", type="datetime",nullable=true)
     */
    private $fechaEmision;
    
    
     /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\ProductoContable")
     * @ORM\JoinColumn(name="producto_contable_id", referencedColumnName="id")
     */
    private $producto_contable_id;
    
    
     /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\EstadoDocumento")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     */
    private $estado_id;
    
    
    
    /**     
    * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\NivelDeCuenta")
    * @ORM\JoinColumn(name="nivel_id", referencedColumnName="id")
    */
    private $nivel;
    
    
    
    public function getId() {
        return $this->id;
    }

    public function getCuentaid() {
        return $this->cuentaid;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getFechaEmision() {
        return $this->fechaEmision;
    }

    public function getProducto_contable_id() {
        return $this->producto_contable_id;
    }

    public function getEstado_id() {
        return $this->estado_id;
    }

    public function getNivel() {
        return $this->nivel;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCuentaid($cuentaid) {
        $this->cuentaid = $cuentaid;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function setFechaEmision($fechaEmision) {
        $this->fechaEmision = $fechaEmision;
    }

    public function setProducto_contable_id($producto_contable_id) {
        $this->producto_contable_id = $producto_contable_id;
    }

    public function setEstado_id($estado_id) {
        $this->estado_id = $estado_id;
    }

    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }



    /**
     * Set producto_contable_id
     *
     * @param \Modulos\PersonasBundle\Entity\ProductoContable $productoContableId
     * @return Documento
     */
    public function setProductoContableId(\Modulos\PersonasBundle\Entity\ProductoContable $productoContableId = null)
    {
        $this->producto_contable_id = $productoContableId;

        return $this;
    }

    /**
     * Get producto_contable_id
     *
     * @return \Modulos\PersonasBundle\Entity\ProductoContable 
     */
    public function getProductoContableId()
    {
        return $this->producto_contable_id;
    }

    /**
     * Set estado_id
     *
     * @param \Modulos\PersonasBundle\Entity\EstadoDocumento $estadoId
     * @return Documento
     */
    public function setEstadoId(\Modulos\PersonasBundle\Entity\EstadoDocumento $estadoId = null)
    {
        $this->estado_id = $estadoId;

        return $this;
    }

    /**
     * Get estado_id
     *
     * @return \Modulos\PersonasBundle\Entity\EstadoDocumento 
     */
    public function getEstadoId()
    {
        return $this->estado_id;
    }
}
