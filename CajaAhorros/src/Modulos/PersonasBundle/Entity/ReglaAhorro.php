<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @ORM\Table(name="regla_ahorro")
 * @ORM\Entity()
 */
class ReglaAhorro
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
     * @ORM\Column(name="descripcion", type="string", length=100,nullable=true)
     */
    private $descripcion;

    /**
     * @var string $variableRegla
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\VariablesReglas")
     */
    private $variableRegla;

    /**
     * @var string
     *
     * @ORM\Column(name="operador", type="string", length=10,nullable=false)
     */
    private $operador;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=100,nullable=true)
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="operador1", type="string", length=10,nullable=false)
     */
    private $operador1;

    /**
     * @var string $variableRegla1
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\VariablesReglas")
     */
    private $variableRegla1;


    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $operador
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;
    }

    /**
     * @return string
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * @param string $operador1
     */
    public function setOperador1($operador1)
    {
        $this->operador1 = $operador1;
    }

    /**
     * @return string
     */
    public function getOperador1()
    {
        return $this->operador1;
    }

    /**
     * @param string $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param string $variableRegla
     */
    public function setVariableRegla($variableRegla)
    {
        $this->variableRegla = $variableRegla;
    }

    /**
     * @return string
     */
    public function getVariableRegla()
    {
        return $this->variableRegla;
    }

    /**
     * @param string $variableRegla1
     */
    public function setVariableRegla1($variableRegla1)
    {
        $this->variableRegla1 = $variableRegla1;
    }

    /**
     * @return string
     */
    public function getVariableRegla1()
    {
        return $this->variableRegla1;
    }

   public function __toString(){
        return $this->descripcion;
    }
    
    }
