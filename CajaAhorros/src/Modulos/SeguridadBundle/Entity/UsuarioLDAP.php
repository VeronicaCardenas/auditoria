<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 30/06/14
 * Time: 14:01
 */

namespace Modulos\SeguridadBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioLDAP
 * @ORM\Table(name="usuarioldap")
 * @ORM\Entity
 */
class UsuarioLDAP {
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
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=100)
     */
    private $usuario;



    public function getId()
    {
        return $this->id;
    }


    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }


    public function getNombre()
    {
        return $this->nombre;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }


    public function getUsuario()
    {
        return $this->usuario;
    }


    public function __toString()
    {
        return $this->usuario;
    }




} 