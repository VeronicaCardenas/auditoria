<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 23/06/15
 * Time: 9:57
 */

namespace Modulos\SeguridadBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * trazaUser
 * @ORM\Table(name="trazauser")
 * @ORM\Entity(repositoryClass="Modulos\SeguridadBundle\Entity\Repository\trazaUserRepository")
 */

class trazaUser {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var datetime
     *
     * @ORM\Column(name="fechaCreacion", type="datetime",nullable=true)
     */
    private $fechaCreacion;

    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="string", length=500)
     */
     private $accion;


    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id = $id;
    }

   public function getFechaCreacion(){
        return $this->fechaCreacion;
    }
    public function setFechaCreacion($fecha){
        $this->fechaCreacion = $fecha;
    }
    public function getAccion(){
        return $this->accion;
    }
    public function setAccion($accion){
        $this->accion = $accion;
    }
    public function __toString(){
        return $this->accion;
    }

} 