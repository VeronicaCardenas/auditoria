<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @ORM\Table(name="frecuencia_de_pagos")
 * @ORM\Entity()
 */
class FrecuenciaDePagos 
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
     *
     * @ORM\Column(name="frecuencia", type="string", length=100,nullable=true)
     */
    private $frecuencia;

    public function getId() {
        return $this->id;
    }

    public function getFrecuencia() {
        return $this->frecuencia;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setFrecuencia($frecuencia) {
        $this->frecuencia = $frecuencia;
    }        
    
    function __toString() {
       return $this->frecuencia ;
    }
    
}
