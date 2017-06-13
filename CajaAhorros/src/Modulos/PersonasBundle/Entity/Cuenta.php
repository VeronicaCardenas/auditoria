<?php
namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estado
 * @ORM\Table(name="cuenta")
 * @ORM\Entity
 */
class Cuenta
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;
    
    
     /**
     * @var string
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    
     /**
     * @var string
     * @ORM\Column(name="siglas", type="string", length=255)
     */
    private $siglas;
    
    
    /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoDeCuenta")
     * @ORM\JoinColumn(name="tipo_cuenta_id", referencedColumnName="id")
     */
    private $tipocuentaid;
    
    
    /**     
    * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\NivelDeCuenta")
    * @ORM\JoinColumn(name="nivel_id", referencedColumnName="id")
    */
    private $nivel;
    
    public function getId() {
        return $this->id;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getSiglas() {
        return $this->siglas;
    }

    public function getTipocuentaid() {
        return $this->tipocuentaid;
    }

    public function getNivel() {
        return $this->nivel;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setSiglas($siglas) {
        $this->siglas = $siglas;
    }

    public function setTipocuentaid($tipocuentaid) {
        $this->tipocuentaid = $tipocuentaid;
    }

    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }


    public function __toString()
    {   
         return (($this->getCodigo()== null ? " " : $this->getCodigo())." ".($this->getNombre()== null ? " " : $this->getNombre()));

    }
   
}
