<?php
namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoDocumento
 * @ORM\Table(name="estado_documento")
 * @ORM\Entity
 */
class EstadoDocumento
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
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setDescripcion($des)
    {
        $this->descripcion = $des;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function __toString()
    {
        return $this->descripcion;
    }
}
