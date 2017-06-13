<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @ORM\Table(name="producto_contable")
 * @ORM\Entity()
 */
class ProductoContable
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
    
    
    public function getId() {
        return $this->id;
    }

    public function getProductoContable() {
        return $this->productoContable;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setProductoContable($productoContable) {
        $this->productoContable = $productoContable;
    }

    public function __toString()
    {
        return $this->productoContable;
    }  
   
}
