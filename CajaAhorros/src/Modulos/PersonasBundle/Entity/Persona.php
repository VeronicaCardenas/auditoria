<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * 
 * @ORM\Table(name="persona")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\PersonaRepository")
 */
class Persona 
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
     * @ORM\Column(name="nombre", type="string", length=100,nullable=true)
     */
    private $nombre;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="primerApellido", type="string", length=100,nullable=true)
     */
    private $primerApellido;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="segundoApellido", type="string", length=100,nullable=true)
     */
    private $segundoApellido;    
    
    
     /**
     * @var string $tipo_persona
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoPersona")
     * @ORM\JoinColumn(name="tipo_persona_id", referencedColumnName="id")
     */
    private $tipo_persona;    
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=100,nullable=true)
     */
    private $correo;
    
     /**
     * @var string
     *
     * @ORM\Column(name="telfonoFijo", type="string", length=15,nullable=true)
     */
    private $telefonoFijo;
    
    
     /**
     * @var string
     *
     * @ORM\Column(name="telfonoMovil", type="string", length=15,nullable=true)
     */
    private $telefonoMovil;
    
     /**
     * @var string
     *
     * @ORM\Column(name="profecion", type="string", length=15,nullable=true)
     */
    private $profecion;    
    
    /**
    * @var datetime
    * @ORM\Column(name="fechaNacimiento", type="datetime",nullable=true)
    */
    private $fechaNacimiento;   
    
     /**
     * @var string $tipo_identificacion
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoDocIdentificacion")
     * @ORM\JoinColumn(name="tipo_identificacion_id", referencedColumnName="id")
     */
    private $tipo_identificacion;
    
     /**
     * @var string
     *
     * @ORM\Column(name="ci", type="string", length=15,nullable=true)
     */
    private $ci; 
    
    
    /**
     * @var datetime
     * @ORM\Column(name="fechaCreacion", type="datetime",nullable=true)
     */
    private $fechaCreacion;

    /**
     * @var datetime
     * @ORM\Column(name="fechaActualizacion", type="datetime",nullable=true)
     */
    private $fechaActualizacion;    
   
     /**
     * @var string $genero
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Genero")
     * @ORM\JoinColumn(name="genero_id", referencedColumnName="id")
     */
    private $genero;
    
    
    /**
     * @var string $regimen_fiscal
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\RegimenFiscal")
     * @ORM\JoinColumn(name="regimen_fiscal_id", referencedColumnName="id")
     */
    private $regimen_fiscal;    
    
     /**
     * @var string $regimen_matrimonial
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\RegimenMatrimonial")
     * @ORM\JoinColumn(name="regimen_matrimonial_id", referencedColumnName="id")
     */
    private $regimen_matrimonial;    
    
     /**
     * @var string $empresa
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Empresa")
     * @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
     */
    private $empresa;    
    
     /**
     * @var string $entidad
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Entidad")
     * @ORM\JoinColumn(name="entidad_id", referencedColumnName="id")
     */
    private $entidad;


     /**
     * @var string $region
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Region")
     * @ORM\JoinColumn(name="region", referencedColumnName="id")
     */
    private $region;
    

     /**
     * @var string $provincia
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Provincia")
     * @ORM\JoinColumn(name="provincia", referencedColumnName="id")
     */
    private $provincia;


     /**
     * @var string $ciudad
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Ciudad")
     * @ORM\JoinColumn(name="ciudad", referencedColumnName="id")
     */
    private $ciudad;


     /**
     * @var string $parroquia
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Parroquia")
     * @ORM\JoinColumn(name="parroquia", referencedColumnName="id")
     */
    private $parroquia;



    
     /**
     * @var string
     *
     * @ORM\Column(name="lugar_nacimiento_e", type="string", length=15,nullable=true)
     */
    private $lugar_nacimiento_e; 
    
    
     /**
     * @var integer
     * @ORM\Column(name="convive", type="integer",nullable=true)     * 
     */
    private $convive;


    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255,nullable=true)
     */
    private $lugardeRecidencia;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $rutaFoto;

    /**
     * @Assert\Image(maxSize = "3M",mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png"},maxSizeMessage="Peso máximo de 3 MB.",mimeTypesMessage = "Solo formatos jpg, jpeg, gif o png.")
     */
    private $foto;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcionConvive", type="string", length=500,nullable=true)
     */

    private $descripcionConvive;

    /**
     * @param string $cargo
     */
    public function setCargo(\Modulos\PersonasBundle\Entity\Cargo $cargo=null)
    {
        $this->cargo = $cargo;
    }

    /**
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * @param string $descripcionConvive
     */
    public function setDescripcionConvive($descripcionConvive)
    {
        $this->descripcionConvive = $descripcionConvive;
    }

    /**
     * @return string
     */
    public function getDescripcionConvive()
    {
        return $this->descripcionConvive;
    }

    /**
     * @param \Modulos\PersonasBundle\Entity\decimal $salarioMensual
     */
    public function setSalarioMensual($salarioMensual)
    {
        $this->salarioMensual = $salarioMensual;
    }

    /**
     * @return \Modulos\PersonasBundle\Entity\decimal
     */
    public function getSalarioMensual()
    {
        return $this->salarioMensual;
    }

    /**
     * @var string $cargo*
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cargo")
     */
    private $cargo;

    /**
     * @var decimal
     *
     * @ORM\Column(name="salarioMensual", type="decimal", precision=20, scale=10, nullable=false)
     */

    private $salarioMensual;


    /**
     * @var boolean $estadoPersona
     * @ORM\Column(name="estado_persona", type="boolean",nullable=false)
     */
    private $estadoPersona;



    /**
     * @param UploadedFile $foto
     */
    public function setFoto(UploadedFile $foto = null)
    {
        $this->foto = $foto;
    }
    /**
     * @return UploadedFile
     */
    public function getFoto()
    {
        return $this->foto;
    }

    public function getRutaFoto()
    {
        return $this->rutaFoto;
    }
    public function setRutaFoto($rutafoto)
    {
        $this->rutaFoto= $rutafoto;
    }
    
    
    
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getPrimerApellido() {
        return $this->primerApellido;
    }

    public function getSegundoApellido() {
        return $this->segundoApellido;
    }

    public function getTipo_persona() {
        return $this->tipo_persona;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function getTelefonoFijo() {
        return $this->telefonoFijo;
    }

    public function getTelefonoMovil() {
        return $this->telefonoMovil;
    }

    public function getProfecion() {
        return $this->profecion;
    }

    public function getFechaNacimiento() {
        return $this->fechaNacimiento;
    }

    public function getTipo_identificacion() {
        return $this->tipo_identificacion;
    }

    public function getCi() {
        return $this->ci;
    }

    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }

    public function getFechaActualizacion() {
        return $this->fechaActualizacion;
    }

    public function getGenero() {
        return $this->genero;
    }

    public function getRegimen_fiscal() {
        return $this->regimen_fiscal;
    }

    public function getRegimen_matrimonial() {
        return $this->regimen_matrimonial;
    }

    public function getEmpresa() {
        return $this->empresa;
    }

    public function getEntidad() {
        return $this->entidad;
    }



    public function getLugar_nacimiento_e() {
        return $this->lugar_nacimiento_e;
    }

    public function getConvive() {
        return $this->convive;
    }

    public function getLugardeRecidencia() {
        return $this->lugardeRecidencia;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setPrimerApellido($primerApellido) {
        $this->primerApellido = $primerApellido;
    }

    public function setSegundoApellido($segundoApellido) {
        $this->segundoApellido = $segundoApellido;
    }

    public function setTipo_persona($tipo_persona) {
        $this->tipo_persona = $tipo_persona;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    public function setTelefonoFijo($telefonoFijo) {
        $this->telefonoFijo = $telefonoFijo;
    }

    public function setTelefonoMovil($telefonoMovil) {
        $this->telefonoMovil = $telefonoMovil;
    }

    public function setProfecion($profecion) {
        $this->profecion = $profecion;
    }

    public function setFechaNacimiento($fechaNacimiento) {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function setTipo_identificacion($tipo_identificacion) {
        $this->tipo_identificacion = $tipo_identificacion;
    }

    public function setCi($ci) {
        $this->ci = $ci;
    }

    public function setFechaCreacion($fechaCreacion) {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function setFechaActualizacion($fechaActualizacion) {
        $this->fechaActualizacion = $fechaActualizacion;
    }

    public function setGenero($genero) {
        $this->genero = $genero;
    }

    public function setRegimen_fiscal($regimen_fiscal) {
        $this->regimen_fiscal = $regimen_fiscal;
    }

    public function setRegimen_matrimonial($regimen_matrimonial) {
        $this->regimen_matrimonial = $regimen_matrimonial;
    }

    public function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }

    public function setEntidad($entidad) {
        $this->entidad = $entidad;
    }



    public function setLugar_nacimiento_e($lugar_nacimiento_e) {
        $this->lugar_nacimiento_e = $lugar_nacimiento_e;
    }

    public function setConvive($convive) {
        $this->convive = $convive;
    }

    public function setLugardeRecidencia($lugardeRecidencia) {
        $this->lugardeRecidencia = $lugardeRecidencia;
    }

   

    /**
     * Set lugar_nacimiento_e
     *
     * @param string $lugarNacimientoE
     * @return Persona
     */
    public function setLugarNacimientoE($lugarNacimientoE)
    {
        $this->lugar_nacimiento_e = $lugarNacimientoE;

        return $this;
    }

    /**
     * Get lugar_nacimiento_e
     *
     * @return string 
     */
    public function getLugarNacimientoE()
    {
        return $this->lugar_nacimiento_e;
    }

    /**
     * Set tipo_persona
     *
     * @param \Modulos\PersonasBundle\Entity\TipoPersona $tipoPersona
     * @return Persona
     */
    public function setTipoPersona(\Modulos\PersonasBundle\Entity\TipoPersona $tipoPersona = null)
    {
        $this->tipo_persona = $tipoPersona;

        return $this;
    }

    /**
     * Get tipo_persona
     *
     * @return \Modulos\PersonasBundle\Entity\TipoPersona 
     */
    public function getTipoPersona()
    {
        return $this->tipo_persona;
    }

    /**
     * Set tipo_identificacion
     *
     * @param \Modulos\PersonasBundle\Entity\TipoDocIdentificacion $tipoIdentificacion
     * @return Persona
     */
    public function setTipoIdentificacion(\Modulos\PersonasBundle\Entity\TipoDocIdentificacion $tipoIdentificacion = null)
    {
        $this->tipo_identificacion = $tipoIdentificacion;

        return $this;
    }

    /**
     * Get tipo_identificacion
     *
     * @return \Modulos\PersonasBundle\Entity\TipoDocIdentificacion 
     */
    public function getTipoIdentificacion()
    {
        return $this->tipo_identificacion;
    }

    /**
     * Set regimen_fiscal
     *
     * @param \Modulos\PersonasBundle\Entity\RegimenFiscal $regimenFiscal
     * @return Persona
     */
    public function setRegimenFiscal(\Modulos\PersonasBundle\Entity\RegimenFiscal $regimenFiscal = null)
    {
        $this->regimen_fiscal = $regimenFiscal;

        return $this;
    }

    /**
     * Get regimen_fiscal
     *
     * @return \Modulos\PersonasBundle\Entity\RegimenFiscal 
     */
    public function getRegimenFiscal()
    {
        return $this->regimen_fiscal;
    }

    /**
     * Set regimen_matrimonial
     *
     * @param \Modulos\PersonasBundle\Entity\RegimenMatrimonial $regimenMatrimonial
     * @return Persona
     */
    public function setRegimenMatrimonial(\Modulos\PersonasBundle\Entity\RegimenMatrimonial $regimenMatrimonial = null)
    {
        $this->regimen_matrimonial = $regimenMatrimonial;

        return $this;
    }

    /**
     * Get regimen_matrimonial
     *
     * @return \Modulos\PersonasBundle\Entity\RegimenMatrimonial 
     */
    public function getRegimenMatrimonial()
    {
        return $this->regimen_matrimonial;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */

    //$region = "0";
    public function setRegion(\Modulos\PersonasBundle\Entity\Region $region=null)
    //public function setRegion($region="1")
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param string $provincia
     */
    public function setProvincia(\Modulos\PersonasBundle\Entity\Provincia $provincia=null)
    {
        $this->provincia = $provincia;
    }

    /**
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * @param string $ciudad
     */
    public function setCiudad(\Modulos\PersonasBundle\Entity\Ciudad $ciudad=null)
    {
        $this->ciudad = $ciudad;
    }



    /**
     * @return string
     */
    public function getParroquia()
    {
        return $this->parroquia;
    }

    /**
     * @param string $parroquia
     */
    public function setParroquia(\Modulos\PersonasBundle\Entity\Parroquia $parroquia = null)
    {
        $this->parroquia = $parroquia;
    }

    /**
     * @return boolean
     */
    public function getEstadoPersona()
    {
        return $this->estadoPersona;
    }

    /**
     * @param boolean $estadoPersona
     */
    public function setEstadoPersona($estadoPersona)
    {
        $this->estadoPersona = $estadoPersona;
    }



    public function subirFoto($directorioDestino)
    {
        if(null===$this->foto)
        {
            return;
        }
        $nombrefoto = $this->foto->getClientOriginalName();

        $this->foto->move($directorioDestino, $nombrefoto);

        $this->setRutaFoto($nombrefoto);

    }
    
     public function __toString()
    {
        //return (($this->getNombre()== null ? " " : $this->getNombre())." ".($this->getPrimerApellido()== null ? " " : $this->getPrimerApellido())." ".($this->getSegundoApellido()== null ? " " : $this->getSegundoApellido()));
        return (($this->getPrimerApellido()== null ? " " : $this->getPrimerApellido())." ".($this->getSegundoApellido()== null ? " " : $this->getSegundoApellido())." ".($this->getNombre()== null ? " " : $this->getNombre()));
//        return $this->nombre."_".$this->primerApellido."_".$this->segundoApellido;
    }   
}
