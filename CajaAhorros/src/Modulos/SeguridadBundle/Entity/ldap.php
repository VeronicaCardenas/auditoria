<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 7/07/14
 * Time: 10:30
 */

namespace Modulos\SeguridadBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ldap
 * @ORM\Table(name="ldap")
 * @ORM\Entity
 */
class ldap {
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
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Assert\NotBlank(message="Campo obligatorio.")
     * @Assert\Length(max=100, maxMessage= "Admite hasta 100 caracteres.")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="puerto", type="string", length=10)
     * @Assert\NotBlank(message="Campo obligatorio.")
     * @Assert\Regex(pattern= "/^[0-9]{1,10}$/",match=true,message= "Puerto no válido.")
     * @Assert\Length(max=10, maxMessage= "Admite hasta 10 caracteres.")
     */
    private $puerto;

    /**
     * @var string
     * @ORM\Column(name="dominio", type="string", length=100)
     * @Assert\NotBlank(message="Campo obligatorio.")
     * @Assert\Length(max=255, maxMessage= "Admite hasta 100 caracteres.")
     */
    private $dominio;

    /**
     * @var string
     * @ORM\Column(name="basedn", type="string", length=255)
     * @Assert\NotBlank(message="Campo obligatorio.")
     * @Assert\Length(max=100, maxMessage= "Admite hasta 100 caracteres.")
     */
    private $basedn;

     /**
     * @var string
     * @ORM\Column(name="user", type="string", length=255)
     * @Assert\NotBlank(message="Campo obligatorio.")
     * @Assert\Length(max=100, maxMessage= "Admite hasta 100 caracteres.")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="pass", type="string", length=255)
     */
    private $pass;

    public function getId(){
       return $this->id;
    }
    public function setNombre($nombre) {
       $this->nombre = $nombre;
       return $this;
    }
    public function getNombre(){
       return $this->nombre;
    }
    public function getPuerto(){
        return $this->puerto;
    }
    public function setPuerto($puerto){
        $this->puerto = $puerto;
    }
    public function getBasedn(){
        return $this->basedn;
    }
    public function setBasedn($basedn){
        $this->basedn = $basedn;
    }
    public function getUser(){
        return $this->user;
    }
    public function setUser($user){
        $this->user = $user;
    }
    public function getPass(){
        return $this->pass;
    }
    public function setPass($pass){
        $this->pass = $pass;
    }
    public function getDominio(){
        return $this->dominio;
    }
    public function setDominio($dominio){
        $this->dominio = $dominio;
    }


} 