<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="nivelescuenta")
 * @ORM\Entity()
 */
class NivelesCuenta
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
     * @ORM\Column(name="nivel", type="string", length=100,nullable=true)
     */
    private $nivel;


    /**
     * @var string
     *
     * @ORM\Column(name="cantidadDigitos", type="string", length=100,nullable=true)
     */
    private $cantidadDigitos;

    public function getId() {
        return $this->id;
    }

    public function getNivel() {
        return $this->nivel;
    }

    public function getCantidadDigitos() {
        return $this->cantidadDigitos;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }
    public function setCantidadDigitos($cantidadDigitos) {
        $this->cantidadDigitos = $cantidadDigitos;
    }



}
