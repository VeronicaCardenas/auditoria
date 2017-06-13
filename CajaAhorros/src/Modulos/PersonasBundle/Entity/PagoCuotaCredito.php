<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 11/19/2015
 * Time: 2:32 PM
 */

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="pagos_cuotas_creditos")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\PagoCuotaCreditoRepository")
 */
class PagoCuotaCredito
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
     * @var integer $credito_id
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Creditos")
     * @ORM\JoinColumn(name="credito_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $creditoId;


    /**
     * @var string $valorIngresado
     * @ORM\Column(name="cuota", type="string", nullable=true)
     */
    private $valorIngresado;


    /**
     * @var datetime
     * @ORM\Column(name="fechaDePago", type="datetime",nullable=true)
     */
    private $fechaDePago;

    /**
     * @var integer $cuotas
     * @ORM\Column(name="coutas", type="integer",nullable=true)
     */
    public $cuotas;


    /**
     * @var boolean $sininteres
     * @ORM\Column(name="sininteres", type="boolean",nullable=true)
     */
    private $sininteres;

    /**
     * @return boolean
     */
    public function getSininteres()
    {
        return $this->sininteres;
    }

    /**
     * @param boolean $sininteres
     */
    public function setSininteres($sininteres)
    {
        $this->sininteres = $sininteres;
    }

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
    public function getCreditoId()
    {
        return $this->creditoId;
    }

    /**
     * @param string $credito_id
     */
    public function setCreditoId($creditoId)
    {
        $this->creditoId = $creditoId;
    }

    /**
     * @return string
     */
    public function getValorIngresado()
    {
        return $this->valorIngresado;
    }

    /**
     * @param string $valorIngresado
     */
    public function setValorIngresado($valorIngresado)
    {
        $this->valorIngresado = $valorIngresado;
    }

    /**
     * @return datetime
     */
    public function getFechaDePago()
    {
        return $this->fechaDePago;
    }

    /**
     * @param datetime $fechaDePago
     */
    public function setFechaDePago($fechaDePago)
    {
        $this->fechaDePago = $fechaDePago;
    }

    //liquidacion parcial credito


    /**
     * @param integer $cuotas
     */

    public function setCuotas($cuotas)
    {
        $this->cuotas = $cuotas;
    }

    /**
     * @return integer
     */
    public function getCoutas()
    {
        return $this->cuotas;
    }


    /////////////////////////////
}