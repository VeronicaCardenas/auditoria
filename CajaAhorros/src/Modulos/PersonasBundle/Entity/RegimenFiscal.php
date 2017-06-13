<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="regimen_fiscal")
 * @ORM\Entity()
 */
class RegimenFiscal 
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
    private $regimen;

    function getId() {
        return $this->id;
    }

    function getRegimen() {
        return $this->regimen;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRegimen($regimen) {
        $this->regimen = $regimen;
    }

    function __construct() {
        
    }
    
    function __toString() {
       return $this->regimen ;
    }

    
}
