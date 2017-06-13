<?php
namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estadoslibro
 * @ORM\Table(name="estadoslibro")
 * @ORM\Entity
 */
class EstadosLibro
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
     * @ORM\Column(name="estado", type="string", length=255)
     */
    private $estado;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setEstado($des)
    {
        $this->estado = $des;
        return $this;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function __toString()
    {
        return $this->estado;
    }
}
