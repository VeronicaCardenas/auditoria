<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="ciudad")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\CiudadRepository")
 */
class Ciudad 
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
     * @var string $provincia
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Provincia")
     * @ORM\JoinColumn(name="ciudad_id", referencedColumnName="id")
     */
    private $provincia;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=100,nullable=true)
     */
    private $codigo;
    
    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getProvincia() {
        return $this->provincia;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }  
    
    public function __toString()
    {
        return $this->nombre;
    }
    
}
