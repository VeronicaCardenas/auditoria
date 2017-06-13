<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * @ORM\Table(name="vchr")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\VCHRRepository")
 */
class VCHR
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
     * @var string $libro
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Libro")
     * @ORM\JoinColumn(name="libro_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $libroId;

    /**
     * @var datetime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime",nullable=true)
     */
    private $fecha;

    /**
     * @var integer $mes
     *
     * @ORM\Column(name="mes", type="integer", nullable=true)
     */
    private $mes;


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
     * @return datetime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param datetime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return string
     */
    public function getLibroId()
    {
        return $this->libroId;
    }

    /**
     * @param string $libroId
     */
    public function setLibroId(\Modulos\PersonasBundle\Entity\Libro $libroId = null)
    {
        $this->libroId = $libroId;
    }

    /**
     * @return int
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * @param int $mes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }


}
