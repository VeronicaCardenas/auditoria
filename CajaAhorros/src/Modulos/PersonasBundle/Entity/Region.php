<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="region")
 * @ORM\Entity()
 */
class Region 
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
     * @var string $pais
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Pais")
     * @ORM\JoinColumn(name="pais_id", referencedColumnName="id")
     */
    private $pais;
    
    
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

    public function getPais() {
        return $this->pais;
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

    public function setPais($pais) {
        $this->pais = $pais;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

        
    
    public function __toString()
    {
        return $this->nombre;
    }
}
