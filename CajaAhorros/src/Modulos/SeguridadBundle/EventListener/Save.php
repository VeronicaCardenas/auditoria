<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 23/06/15
 * Time: 14:32
 */

namespace Modulos\SeguridadBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Modulos\SeguridadBundle\Entity\trazaUser;
use Modulos\SeguridadBundle\Entity\Usuario;



class Save {

    private $container;
    private $valor;

    function __construct($container) {
        $this->container = $container;
    }
    public function getUser()
    {
      return $this->container->get('security.context')->getToken()->getUser();
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        if($entity instanceof Usuario){
            $traza = new trazaUser();
            $traza->setAccion(" El usuario: <a href=#>".$this->getUser()->getUsuario()."</a> ha ".$entity->getAccion()." el usuario: <a href=#>".$entity->getUsuario()."</a>");
            $traza->setFechaCreacion(new \DateTime('now'));
            $entityManager->persist($traza);
            $entityManager->flush();
        }


    }


    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        if ($entity instanceof Usuario){
            $traza = new trazaUser();
            if($entity->getAccion()=="Cambiado"){
                $traza->setAccion(" El usuario: <a href=#>".$this->getUser()->getUsuario()."</a> ha Cambiado la contraseña");
            }
            else if($entity->getAccion()=="Creado"){
                 $traza->setAccion(" El usuario: <a href=#>".$this->getUser()->getUsuario()."</a> ha Actualizado el usuario: <a href=#>".$entity->getUsuario()."</a>");
               // $traza->setAccion("<a href=#>HH</a>");
            }
            else{
                $traza->setAccion(" El usuario: <a href=#>".$this->getUser()->getUsuario()."</a> ha ".$entity->getAccion()." el usuario: <a href=#>".$entity->getUsuario()."</a>");
            }
            $traza->setFechaCreacion(new \DateTime('now'));
            $entityManager->persist($traza);
            $entityManager->flush();
        }
    }

} 