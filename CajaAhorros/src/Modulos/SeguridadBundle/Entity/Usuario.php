<?php

namespace Modulos\SeguridadBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Usuario
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="Modulos\SeguridadBundle\Entity\Repository\UsuarioRepository")
 * @UniqueEntity(fields={"ci"},message="El ci ya existe.")
 * @UniqueEntity(fields={"usuario"},message="El usuario ya existe.")
 * @Assert\Callback(methods={"isPasswordValid"})
 * @Assert\Callback(methods={"isRoleValid"})
 */
class Usuario implements AdvancedUserInterface, EquatableInterface, \Serializable
{
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
                $this->id,
                $this->usuario,
                $this->password,
                $this->salt,
        ));
    }
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->usuario,
            $this->password,
            $this->salt
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
       if($this->getFechaExpiracion()== null){
            return true;
       }
        else{
            $ahora = new \DateTime('now');
            if($this->fechaExpiracion < $ahora ){
                return false;

            }
            else{
                return true;
            }
        }
    }
    public function isEnabled()
    {
        if($this->estado == 1)
        {
            throw new BadCredentialsException("El usuario ha sido eliminado. Por favor contacte con el administrador del sistema.");
        }
        else{
            return true;
        }


    }
    public function isEqualTo(UserInterface $user)
    {
          /* if (!$user instanceof Usuario) {
               return false;
            }

            if ($this->password !== $user->getPassword()) {
               return false;
            }

            if ($this->getSalt() !== $user->getSalt()) {
                return false;
            }

            if ($this->usuario !== $user->getUsuario()) {
               return false;
            }
            if($this->estado == 1)
            {
                throw new BadCredentialsException("El usuario ha sido eliminado. Por favor contacte con el administrador del sistema.");
            }*/
            return true;
    }
    function eraseCredentials()
    {
       
    }
    public function getSalt()
    {
        return $this->salt;
    }
    public function getPassword()
    {
        return $this->password;
    }
    function getRoles()
    {
      return $this->getRole()->toArray();
    }
    function getUsername()
    {
       return $this->getUsuario();

    }

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
     * @ORM\Column(name="ci", type="string", length=11,nullable=true)
     */
    private $ci;

    /**
     * @var string $sexo
     *
     * @ORM\ManyToOne(targetEntity="Modulos\SeguridadBundle\Entity\Sexo")
     */
    private $sexo;
    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text",nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=20,nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=100)
     * @Assert\NotBlank(message="Campo obligatorio.")
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank(message="Campo obligatorio.")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255,nullable=true)
     */
    private $salt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isldap", type="boolean",nullable=true)
     */
    private $isldap;
    
     /**
     * 
     * @var ArrayCollection $role
     * @ORM\ManyToMany(targetEntity="Modulos\SeguridadBundle\Entity\Role")
     *
     */
    private $role;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", length=10,nullable=true)
     */
    private $estado;

     /**
     * @var datetime
     *
     * @ORM\Column(name="fechaExpiracion", type="datetime",nullable=true)
     */
    private $fechaExpiracion;

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
     * @ORM\Column(name="accion", type="string", length=255,nullable=true)
     */
     private $accion;


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
    public function getAccion(){
        return $this->accion;
    }
    public function setAccion($accion=null){
        $this->accion = $accion;
    }

    public function __construct()
    {
      $this->role = new ArrayCollection();
      $this->salt = md5(uniqid(null, true));
    }
    public function getFechaExpiracion()
    {
        return $this->fechaExpiracion;
    }
    public function setFechaExpiracion($fechaExp)
    {
        $this->fechaExpiracion = $fechaExp;
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
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set ci
     *
     * @param string $ci
     * @return Usuario
     */
    public function setCi($ci)
    {
        $this->ci = $ci;
    
        return $this;
    }

    /**
     * Get ci
     *
     * @return string 
     */
    public function getCi()
    {
        return $this->ci;
    }

    public function getSexo()
    {
        
        return $this->sexo;
    }
    public function setSexo(\Modulos\SeguridadBundle\Entity\Sexo $sexo=null)
    {
       
        $this->sexo= $sexo;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Usuario
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
     * Set telefono
     *
     * @param string $telefono
     * @return Usuario
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
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return Usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole(\Modulos\SeguridadBundle\Entity\Role $role)
    {
        
        $this->role[]= $role;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getRutaFoto()
    {
        return $this->rutaFoto;
    }
    public function setRutaFoto($rutafoto)
    {
        $this->rutaFoto= $rutafoto;  
    }

    public function getIsldap()
    {
        return $this->isldap;
    }
    public function setIsldap($ldap)
    {
        $this->isldap = $ldap;
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

   public function isRoleValid(ExecutionContextInterface $context)
   {
        //$nombre_propiedad = $context->getPropertyPath() . '.documentosLegales';
        $roles = $this->getRole();

        if ($roles->count()== 0) {
            //$context->setPropertyPath($nombre_propiedad);
            $context->addViolationAt('role', 'Campo obligatorio.', array(), null);
            return;
        }

   }
   /*
    Contraseñas que contengan al menos una letra mayúscula.
    Contraseñas que contengan al menos una letra minúscula.
    Contraseñas que contengan al menos un número o caracter especial.
    Contraseñas cuya longitud sea como mínimo 8 caracteres.
    Contraseñas cuya longitud máxima no debe ser arbitrariamente limitada.
    */
   public function isPasswordValid(ExecutionContextInterface $context)
   {
       if (!preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $this->password))
       {
           $context->addViolationAt('password', 'La contraseña no es segura.', array(), null);
           return;
       }

   }
    public function __toString(){
        return $this->usuario;
    }

    
}
