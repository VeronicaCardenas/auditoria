<?php
namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estado
 * @ORM\Table(name="caja_persona")
 * @ORM\Entity
 */
class CajaPersona
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
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Persona")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $personaid;
    
        
    /**
     * @var decimal
     * @ORM\Column(name="valor",type="decimal",scale=2,nullable=true)
    */
    private $valor;
    
    
    public function getId() {
        return $this->id;
    }

    public function getCuentaid() {
        return $this->cuentaid;
    }

    public function getValor() {
        return $this->valor;
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
    
    public function getPersonaid() {
        return $this->personaid;
    }

    public function setPersonaid($personaid) {
        $this->personaid = $personaid;
    }



   
    
 
}
