<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * @ORM\Table(name="dtvc")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\DTVCRepository")
 */
class DTVC
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
     * @var string $cuenta
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_deudora_id", referencedColumnName="id")
     */
    private $cuentaDeudoraId;

    /**
     * @var string $persona
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_acreedora_id", referencedColumnName="id")
     */
    private $cuentaAcreedoraId;

    /**
     * @var decimal $valor
     *
     * @ORM\Column(name="valor", type="decimal", precision=20, scale=10, nullable=false)
     */
    private $valor;

    /**
     * @var string $vchr
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\VCHR")
     * @ORM\JoinColumn(name="vchr_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $idVchr;

    /**
     * @var boolean $valor
     *
     * @ORM\Column(name="esDebe", type="boolean", nullable=false)
     */
    private $esDebe=true;


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
    public function getCuentaDeudoraId()
    {
        return $this->cuentaDeudoraId;
    }

    /**
     * @param string $cuentaDeudoraId
     */
    public function setCuentaDeudoraId(\Modulos\PersonasBundle\Entity\Cuenta $cuentaDeudoraId)
    {
        $this->cuentaDeudoraId = $cuentaDeudoraId;
    }

    /**
     * @return string
     */
    public function getCuentaAcreedoraId()
    {
        return $this->cuentaAcreedoraId;
    }

    /**
     * @param string $cuentaAcreedoraId
     */
    public function setCuentaAcreedoraId(\Modulos\PersonasBundle\Entity\Cuenta $cuentaAcreedoraId)
    {
        $this->cuentaAcreedoraId = $cuentaAcreedoraId;
    }

    /**
     * @return decimal
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param decimal $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return int
     */
    public function getIdVchr()
    {
        return $this->idVchr;
    }

    /**
     * @param int $idVchr
     */
    public function setIdVchr(\Modulos\PersonasBundle\Entity\VCHR $idVchr)
    {
        $this->idVchr = $idVchr;
    }

    /**
     * @return boolean
     */
    public function getEsDebe()
    {
        return $this->esDebe;
    }

    /**
     * @param boolean $esDebe
     */
    public function setEsDebe($esDebe)
    {
        $this->esDebe = $esDebe;
    }



}
