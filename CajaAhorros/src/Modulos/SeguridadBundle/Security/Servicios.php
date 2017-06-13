<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 14/08/14
 * Time: 13:31
 */

namespace Modulos\SeguridadBundle\Security;


class Servicios {

    public function getUserLogueado()
    {
       return $this->get('security.context')->getToken()->getUser();
    }


} 