<?php
namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estado
 * @ORM\Table(name="grupo")
 * @ORM\Entity
 */
class Grupo
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
     * @ORM\Column(name="grupos", type="string", length=255)
     */
    private $grupo;
    

    public function getId() {
        return $this->id;
    }

    public function getGrupo() {
        return $this->grupo;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }   

    public function __toString()
    {
        return $this->cargo;
    }
}
