<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="pagoCuotaAhorro")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\PagoCuotaAhorroRepository")
 */
class PagoCuotaAhorro
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
     * @ORM\Column(name="cuota", type="decimal", precision=20, scale=10, nullable=true)
     */
    private $cuota;

    /**
     * @var string
     * @ORM\Column(name="cuotaAcumulada", type="decimal", precision=20, scale=10, nullable=true)
     */
    private $cuotaAcumulada;

    /**
     * @var string
     * @ORM\Column(name="interes", type="decimal", precision=20, scale=10, nullable=true)
     */
    private $interes;


    /**
     * @var datetime
     * @ORM\Column(name="fechaDeEntrada", type="datetime",nullable=true)
     */
    private $fechaDeEntrada;

    /**
     * @var integer
     * @ORM\Column(name="tipo", type="integer",nullable=true)
     */
    private $tipo;

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
     * @return string
     */
    public function getCuotaAcumulada()
    {
        return $this->cuotaAcumulada;
    }

    /**
     * @param string $cuotaAcumulada
     */
    public function setCuotaAcumulada($cuotaAcumulada)
    {
        $this->cuotaAcumulada = $cuotaAcumulada;
    }

    /**
     * @return string
     */
    public function getInteres()
    {
        return $this->interes;
    }

    /**
     * @param string $interes
     */
    public function setInteres($interes)
    {
        $this->interes = $interes;
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

    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }


}