<?php
namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estado
 * @ORM\Table(name="grupos_personas")
 * @ORM\Entity
 */
class GruposPersonas
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
     * @var string $grupo
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Grupo")
     * @ORM\JoinColumn(name="grupo_id", referencedColumnName="id")
     */
    private $grupo;   
    
    
    /**
    * @var string $persona
    *
    * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Persona")
    * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
    */
    private $persona;   
    
    
    /**
    * @var string $cargo
    *
    * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cargo")
    * @ORM\JoinColumn(name="cargo_id", referencedColumnName="id")
    */
    private $cargo;     

  

    public function __toString()
    {
        return $this->persona;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set grupo
     *
     * @param \Modulos\PersonasBundle\Entity\Grupo $grupo
     * @return GruposPersonas
     */
    public function setGrupo(\Modulos\PersonasBundle\Entity\Grupo $grupo = null)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return \Modulos\PersonasBundle\Entity\Grupo 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set persona
     *
     * @param \Modulos\PersonasBundle\Entity\Persona $persona
     * @return GruposPersonas
     */
    public function setPersona(\Modulos\PersonasBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \Modulos\PersonasBundle\Entity\Persona 
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set cargo
     *
     * @param \Modulos\PersonasBundle\Entity\Cargos $cargo
     * @return GruposPersonas
     */
    public function setCargo(\Modulos\PersonasBundle\Entity\Cargo $cargo = null)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargos
     *
     * @return \Modulos\PersonasBundle\Entity\Cargos 
     */
    public function getCargo()
    {
        return $this->cargo;
    }
}
