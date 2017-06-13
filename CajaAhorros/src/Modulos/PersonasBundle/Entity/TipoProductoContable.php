<?php
namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="tipo_producto_contable")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\TipoProductoContableRepository")
 */
class TipoProductoContable
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @var string $cuentaDebe
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_debe", referencedColumnName="id")
     */

    private $cuentaDebe;

    /**
     * @var string $cuentaHaber
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_haber", referencedColumnName="id")
     */
    private $cuentaHaber;

    /**
     * @var string $tipotransaccion
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoTransaccion")
     * @ORM\JoinColumn(name="tipotransaccion", referencedColumnName="id")
     */
    private $tipotransaccion;


    /**
     * @param string $cuentaDebe
     */
    public function setCuentaDebe($cuentaDebe)
    {
        $this->cuentaDebe = $cuentaDebe;
    }

    /**
     * @return string
     */
    public function getCuentaDebe()
    {
        return $this->cuentaDebe;
    }

    /**
     * @param string $cuentaHaber
     */
    public function setCuentaHaber($cuentaHaber)
    {
        $this->cuentaHaber = $cuentaHaber;
    }

    /**
     * @return string
     */
    public function getCuentaHaber()
    {
        return $this->cuentaHaber;
    }

     /**
     * @param string $tipotransaccion
     */
    public function setTipoTransaccion($tipotransaccion)
    {
        $this->tipotransaccion = $tipotransaccion;
    }

    /**
     * @return string
     */
    public function getTipoTransaccion()
    {
        return $this->tipotransaccion;
    }


    public function getId() {
        return $this->id;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function __toString()
    {
        return $this->tipo;
    }
}
