<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @ORM\Table(name="producto_contable_cuenta")
 * @ORM\Entity()
 */
class ProductoContableCuenta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="producto_contable", type="string", length=100,nullable=true)
    */
    private $productoContable;
    
    
    /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_id", referencedColumnName="id")
     */
    private $cuentaid;
    
    
     /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Operador")
     * @ORM\JoinColumn(name="operacion_id", referencedColumnName="id")
     */
    private $operacionid;
    
    
    /**     
    * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\NivelDeCuenta")
    * @ORM\JoinColumn(name="nivel_id", referencedColumnName="id")
    */
    private $nivel;
    
    
    
    
        
    /**
     * @var decimal
     * @ORM\Column(name="porciento",type="decimal",scale=2,nullable=true)
    */
    private $porciento;
    
    
    public function getId() {
        return $this->id;
    }

    public function getProductoContable() {
        return $this->productoContable;
    }

    public function getCuentaid() {
        return $this->cuentaid;
    }

    public function getOperacionid() {
        return $this->operacionid;
    }

    public function getNivel() {
        return $this->nivel;
    }

    public function getPorciento() {
        return $this->porciento;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setProductoContable($productoContable) {
        $this->productoContable = $productoContable;
    }

    public function setCuentaid($cuentaid) {
        $this->cuentaid = $cuentaid;
    }

    public function setOperacionid($operacionid) {
        $this->operacionid = $operacionid;
    }

    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }

    public function setPorciento($porciento) {
        $this->porciento = $porciento;
    }

 
    
  
   
}
