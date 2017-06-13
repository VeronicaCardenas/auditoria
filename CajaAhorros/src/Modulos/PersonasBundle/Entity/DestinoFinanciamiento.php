<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="destino_financiamiento")
 * @ORM\Entity()
 */
class DestinoFinanciamiento 
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
     * @ORM\Column(name="financiamiento", type="string", length=100,nullable=true)
     */
    private $financiamiento;

    public function getId() {
        return $this->id;
    }

    public function getFinanciamiento() {
        return $this->financiamiento;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setFinanciamiento($financiamiento) {
        $this->financiamiento = $financiamiento;
    }    
        
    function __toString() {
       return $this->financiamiento ;
    }

    
}
