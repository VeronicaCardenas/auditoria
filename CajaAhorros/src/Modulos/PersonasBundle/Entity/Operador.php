<?php
namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Estado
 * @ORM\Table(name="operador")
 * @ORM\Entity
 */
class Operador
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
     * @ORM\Column(name="operador", type="string", length=255)
     */
    private $operador;

    public function __toString()
    {
        return $this->operador;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getOperador() {
        return $this->operador;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setOperador($operador) {
        $this->operador = $operador;
    }
}
