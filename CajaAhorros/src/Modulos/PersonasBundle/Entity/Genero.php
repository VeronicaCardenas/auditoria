<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="genero")
 * @ORM\Entity()
 */
class Genero 
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
     * @ORM\Column(name="tipo", type="string", length=100,nullable=true)
     */
    private $genero;

    function getId() {
        return $this->id;
    }

    function getGenero() {
        return $this->genero;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setGenero($genero) {
        $this->genero = $genero;
    }

    
    function __construct() {
        
    }
    
    function __toString() {
       return $this->genero ;
    }

    
}
