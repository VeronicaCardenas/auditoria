<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * 
 * @ORM\Table(name="entidad")
 * @ORM\Entity()
 */
class Entidad 
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
     * @ORM\Column(name="razonSocial", type="string", length=100,nullable=true)
     */
    private $razonSocial;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="nombreFantasia", type="string", length=100,nullable=true)
     */
    private $nombreFantasia;
    
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255,nullable=true)
     */
    private $direccion;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=100,nullable=true)
     */
    private $telefono;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=100,nullable=true)
     */
    private $correo;
    
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="ruc", type="string", length=100,nullable=true)
     */
    private $ruc;
    

    /**
     * @var datetime
     * @ORM\Column(name="fechaCreacion", type="datetime",nullable=true)
     */
    private $fechaCreacion;


    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string",length=5000, nullable=false)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $rutaLogo;

    /**
     * @Assert\Image(maxSize = "3M",mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png"},maxSizeMessage="Peso máximo de 3 MB.",mimeTypesMessage = "Solo formatos jpg, jpeg, gif o png.")
     */
    private $logo;



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
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return Entidad
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set nombreFantasia
     *
     * @param string $nombreFantasia
     * @return Entidad
     */
    public function setNombreFantasia($nombreFantasia)
    {
        $this->nombreFantasia = $nombreFantasia;

        return $this;
    }

    /**
     * Get nombreFantasia
     *
     * @return string 
     */
    public function getNombreFantasia()
    {
        return $this->nombreFantasia;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Entidad
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

     /**
     * Set direccion
     *
     * @param string $ruc
     * @return Entidad
     */
    public function setRuc($ruc)
    {
        $this->ruc = $ruc;

        return $this;
    }

    /**
     * Get ruc
     *
     * @return string 
     */
    public function getRuc()
    {
        return $this->ruc;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Entidad
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set correo
     *
     * @param string $correo
     * @return Entidad
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string 
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    
     /**
     * Set fechaCreacion
     *
     * @param string $fechaCreacion
     * @return Entidad
     */
    public function setfechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return string 
     */
    public function getfechaCreacion()
    {
        return $this->fechaCreacion;
    }
    

     /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Entidad
     */
    public function setdescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getdescripcion()
    {
        return $this->descripcion;
    }
    
    
       function __toString() {
       return $this->razonSocial;
    }

    /**
     * @param UploadedFile $logo
     */
    public function setLogo(UploadedFile $logo = null)
    {
        $this->logo = $logo;
    }
    /**
     * @return UploadedFile
     */
    public function getLogo()
    {
        return $this->logo;
    }

    public function getRutaLogo()
    {
        return $this->rutaLogo;
    }
    public function setRutaLogo($rutaLogo)
    {
        $this->rutaLogo= $rutaLogo;
    }

    public function subirLogo($directorioDestino)
    {
        if(null===$this->logo)
        {
            return;
        }
        $nombrelogo = $this->logo->getClientOriginalName();

        $this->logo->move($directorioDestino, $nombrelogo);

        $this->setRutaLogo($nombrelogo);

    }
}
