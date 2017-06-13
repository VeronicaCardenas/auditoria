<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 1/31/2016
 * Time: 12:35 AM
 */

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * PersonaDocuemento
 *
 * @ORM\Table(name="creditodocumento")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\CreditoDocumentoRepository")
 * @UniqueEntity(fields={"nombre"},message="El documento ya existe.")
 */
class CreditosDocumento
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
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Assert\NotBlank(message="Campo obligatorio.")
     */
    private $nombre;

    /**
     * @ORM\Column(name="rutaDocumento",type="string",nullable=true)
     */
    private $rutaDocumento;

    /**
     * @Assert\File
     */
    private $documento;

    /**
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Creditos")
     * @ORM\JoinColumn(name="credito_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $credito;


    /**
     * @param UploadedFile $documento
     */
    public function setDocumento(UploadedFile $doc = null)
    {
        $this->documento = $doc;
    }
    /**
     * @return UploadedFile
     */
    public function getDocumento()
    {
        return $this->documento;
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
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $credito
     */
    public function setCredito(\Modulos\PersonasBundle\Entity\Creditos $credito=null)
    {
        $this->credito = $credito;
    }

    /**
     * @return mixed
     */
    public function getCredito()
    {
        return $this->credito;
    }

    /**
     * @param mixed $rutaFoto
     */
    public function setRutaDocumento($rutaDocuemento)
    {
        $this->rutaDocumento = $rutaDocuemento;
    }

    /**
     * @return mixed
     */
    public function getRutaDocumento()
    {
        return $this->rutaDocumento;
    }

    public function subirDocumento($directorioDestino)
    {
        if(null===$this->documento)
        {
            return;
        }
        $nombreDocuemento = $this->documento->getClientOriginalName();

        $this->documento->move($directorioDestino, $nombreDocuemento);

        $this->setRutaDocumento($nombreDocuemento);

    }



}