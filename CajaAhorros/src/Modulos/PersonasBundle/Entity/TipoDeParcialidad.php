<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="tipo_de_parcialidad")
 * @ORM\Entity()
 */
class TipoDeParcialidad 
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
     * @ORM\Column(name="parcialidad", type="string", length=100,nullable=true)
     */
        private $parcialidad;

    public function getId() {
        return $this->id;
    }

    public function getParcialidad() {
        return $this->parcialidad;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setParcialidad($parcialidad) {
        $this->parcialidad = $parcialidad;
    }

        
    function __toString() {
       return $this->parcialidad ;
    }

    
}
