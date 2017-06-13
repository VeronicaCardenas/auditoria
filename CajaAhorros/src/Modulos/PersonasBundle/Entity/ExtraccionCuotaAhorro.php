<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="extraccionCuotaAhorro")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\ExtraccionCuotaAhorroRepository")
 */
class ExtraccionCuotaAhorro
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
     * @var integer $idAhorro
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Ahorro")
     * @ORM\JoinColumn(name="idAhorro", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $idAhorro;


    /**
     * @var string 
     * @ORM\Column(name="cuota", type="string", nullable=true)
     */
    private $cuota;


    /**
     * @var datetime
     * @ORM\Column(name="fechaDeEntrada", type="datetime",nullable=true)
     */
    private $fechaDeEntrada;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIdAhorro()
    {
        return $this->idAhorro;
    }

    /**
     * @param string
     */
    public function setIdAhorro($IdAhorro)
    {
        $this->idAhorro = $IdAhorro;
    }

    /**
     * @return string
     */
    public function getCuota()
    {
        return $this->cuota;
    }

    /**
     * @param string 
     */
    public function setCuota($cuota)
    {
        $this->cuota = $cuota;
    }

    /**
     * @return datetime
     */
    public function getFechaDeEntrada()
    {
        return $this->fechaDeEntrada;
    }

    /**
     * @param datetime 
     */
    public function setFechaDeEntrada($fechaDeEntrada)
    {
        $this->fechaDeEntrada = $fechaDeEntrada;
    }
}