<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="provincia")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\ProvinciaRepository")
 */
class Provincia 
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
     * @ORM\Column(name="nombre", type="string", length=100,nullable=true)
     */
    private $nombre;
    
    
        
     /**
     * @var string $region
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=100,nullable=true)
     */
    private $codigo;
    
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getRegion() {
        return $this->region;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

            
    public function  __toString()
    {
        return $this->nombre;
    }
  
}
