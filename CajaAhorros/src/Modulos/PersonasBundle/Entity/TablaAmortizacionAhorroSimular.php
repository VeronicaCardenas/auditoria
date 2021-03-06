<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="tablaamortizacionahorro_simular")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\TablaAmortizacionAhorroSimularRepository")
 */
class TablaAmortizacionAhorroSimular
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
     * @var string $ahorro_id
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\AhorroSimular")
     * @ORM\JoinColumn(name="ahorro_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $ahorro_id;
    
    
     /**
     * @var string $cuota
     * @ORM\Column(name="cuota", type="string", nullable=true)
     */
    private $cuota;
    

    /**
     * @var datetime
     * @ORM\Column(name="fechaDePago", type="datetime",nullable=true)
     */
    private $fechaDePago;

    /**
     * @var string
     *
     * @ORM\Column(name="capital", type="string", length=100,nullable=true)
     */
    private $capital;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="interes", type="string", length=100,nullable=true)
     */
    private $interes;
    
    
    
    
     /**
     * @var decimal $valorcuota
      *
      * @ORM\Column(name="valorcuota", type="decimal", precision=20, scale=10, nullable=true)
      */
    private $valorcuota;



    /**
     * @var decimal $saldo
     *
     * @ORM\Column(name="saldo", type="decimal", precision=20, scale=10, nullable=true)
     */
    private $saldo;

    /**
     * @return decimal
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * @param decimal $saldo
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
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
    public function getAhorroId()
    {
        return $this->ahorro_id;
    }

    /**
     * @param string $credito_id
     */
    public function setAhorroId($ahorro_id)
    {
        $this->ahorro_id = $ahorro_id;
    }

    /**
     * @return string
     */
    public function getCuota()
    {
        return $this->cuota;
    }

    /**
     * @param string $cuota
     */
    public function setCuota($cuota)
    {
        $this->cuota = $cuota;
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

    /**
     * @return string
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * @param string $capital
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;
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
     * @return decimal
     */
    public function getValorcuota()
    {
        return $this->valorcuota;
    }

    /**
     * @param decimal $valorcuota
     */
    public function setValorcuota($valorcuota)
    {
        $this->valorcuota = $valorcuota;
    }
    
    

}
