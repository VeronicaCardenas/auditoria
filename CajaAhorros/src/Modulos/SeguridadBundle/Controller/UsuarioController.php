<?php
namespace Modulos\SeguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Modulos\SeguridadBundle\Entity\Usuario;
use Modulos\SeguridadBundle\Entity\ldap;
use Modulos\SeguridadBundle\Entity\UsuarioLDAP;
use Modulos\SeguridadBundle\Form\Type\UsuarioType;
use Modulos\SeguridadBundle\Form\Type\ldapType;
use Modulos\SeguridadBundle\Form\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller {

    public function pre_autenticacionAction()
    {
        return $this->redirect($this->generateUrl('autenticacion'));

    }
    public function autenticacionAction(Request $request)
    {
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        }elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
        return $this->render('SeguridadBundle:Vistas_Usuarios:autenticacion.html.twig',array('last_username' => $lastUsername,
                'error'=> $error,'active'=>0));

    }

    public function inicioAction()
    {
        $em=  $this->getDoctrine()->getManager();
        $usuario = $this->get('security.context')->getToken()->getUser();
        $usuarios= $em->getRepository('SeguridadBundle:Usuario')->findAll();
        $total = count($usuarios);
        $countRemove = 0;
        $countldap = 0;
        $countActive = 0;
        foreach($usuarios as $user){
            if($user->getEstado()==1){
             $countRemove++;
            }
            if($user->getEstado()==0){
              if($user->getIsldap()==false){
                  $countActive++;
              }
              else{
                  $countldap++;
              }
            }
        }
        $data_points = array();
        $data_points [] = $total;
        $data_points [] = $countActive;
        $data_points [] =$countRemove;
        $data_points [] = $countldap;

        return $this->render('SeguridadBundle:Vistas_Usuarios:inicio.html.twig',array('usuario'=>$usuario,'data'=>$data_points));
    }

    public function listadousuariosAction(){
        $em=  $this->getDoctrine()->getManager();
        $usuarios= $em->getRepository('SeguridadBundle:Usuario')->findUsuariosEstado(0);
        return $this->render('SeguridadBundle:Vistas_Usuarios:lista_usuarios.html.twig', array('usuarios'=>$usuarios));
    }
    public function visualizarusuarioAction($id)
    {
        $em=  $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('SeguridadBundle:Usuario')->findOneById($id);
        return $this->render('SeguridadBundle:Vistas_Usuarios:visualizarusuario.html.twig',array('usuario'=>$usuario));
    }
    public function editarusuarioAction($id)
    {
        $request = $this->getRequest();
        $em=  $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('SeguridadBundle:Usuario')->findOneById($id);
        $form = $this->createForm(new UsuarioType(),$usuario);
        $passwordOriginal = $form->getData()->getPassword();
        $rutaFotoOriginal = $form->getData()->getRutaFoto();
        $form->handleRequest($request);
        if ($form->isValid())
         {
                if("çøʯʊʚAa7" == $usuario->getPassword())
                {
                    $usuario->setPassword($passwordOriginal);
                }
                else
                {
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($usuario);
                    $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
                    $usuario->setPassword($password);
                    $aux = false;
                    foreach($usuario->getRole() as $rol){
                        if($rol->getRol() == "ROLE_ADMIN"){
                            $aux = true;
                            break;
                        }
                    }
                    if($aux){
                        $usuario->setFechaExpiracion(new \DateTime('now + 100 Years'));
                    }
                    else{
                        if($usuario->getIsldap() == false){
                            $usuario->setFechaExpiracion(new \DateTime('now + 2 Month'));
                        }

                    }
                }
                if (null == $usuario->getFoto())
                {
                    $usuario->setRutaFoto($rutaFotoOriginal);
                }
                else
                {
                    if($rutaFotoOriginal != null)
                    {
                        $usuario->subirFoto($this->container->getParameter('modulos.directorio.fotos'));
                        // Borrar el documento anterior
                        unlink($this->container->getParameter('modulos.directorio.fotos').$rutaFotoOriginal);
                    }
                    else
                    {
                        $usuario->subirFoto($this->container->getParameter('modulos.directorio.fotos'));
                    }

                }
                $usuario->setAccion("Creado");
                $em->persist($usuario);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                        'notice',
                        'Se ha editado el usuario: '.$usuario->getNombre().''
                );
                return $this->redirect($this->generateUrl('ver_usuarios',array('id'=>$usuario->getId())));
             }
            return $this->render('SeguridadBundle:Vistas_Usuarios:editar_usuario.html.twig',array('form'=>$form->createView(),'usuario'=>$usuario
        ));

    }
    public function insertarUsuarioAction()
    {
        $request = $this->getRequest();
        $em=  $this->getDoctrine()->getManager();
        $usuario = new Usuario();
        $form = $this->createForm(new UsuarioType(),$usuario);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
            $usuario->setPassword($password);
            $usuario->setEstado(0);
            $usuario->setIsldap(0);
            $usuario->setAccion("Creado");
            $aux = false;
            foreach($usuario->getRole() as $rol){
                if($rol->getRol() == "ROLE_ADMIN"){
                    $aux = true;
                    break;
                }
            }
            if($aux){
                $usuario->setFechaExpiracion(new \DateTime('now + 100 Years'));
            }
            else{
                $usuario->setFechaExpiracion(new \DateTime('now + 2 Month'));
            }
            $usuario->subirFoto($this->container->getParameter('modulos.directorio.fotos'));
            $em->persist($usuario);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se ha creado el usuario: '.$usuario->getNombre().''
             );
             return $this->redirect($this->generateUrl('ver_usuarios',array('id'=>$usuario->getId())));
        }
        return $this->render('SeguridadBundle:Vistas_Usuarios:nuevo_usuario.html.twig',array('form'=>$form->createView()
       ));
    }

    public function eliminarusuarioAction($id)
    {
        $em=  $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('SeguridadBundle:Usuario')->findOneById($id);
        $usuariologueado = $this->get('security.context')->getToken()->getUser();
        $usuario->setEstado(1);
        $usuario->setAccion("Eliminado");
        $em->persist($usuario);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Se ha eliminado el Usuario: '.$usuario->getNombre().'.'
        );
        if($usuario === $usuariologueado)
        {

            return $this->redirect($this->generateUrl('logout'));
        }
        else
        {
            return $this->redirect($this->generateUrl('listadousuarios'));
        }

    }
    public function eliminar_UsuariosMarcadosAction($elementos)
    {
        $datosUsuarios = explode(",§,", $elementos);
        $em=  $this->getDoctrine()->getManager();
        for($i=0;$i<count($datosUsuarios);$i++)
        {
            $usuario = $em->getRepository('SeguridadBundle:Usuario')->findOneById($datosUsuarios[$i]);
            $usuario->setEstado(1);
            $usuario->setAccion("Eliminado");
            $em->persist($usuario);
            $em->flush();

        }
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Se han eliminado los usuarios marcados.'
        );
        return $this->redirect($this->generateUrl('listadousuarios'));

    }
    public function listadousuarioseliminadosAction()
    {
        $em=  $this->getDoctrine()->getManager();
        $usuarios= $em->getRepository('SeguridadBundle:Usuario')->findUsuariosEstado(1);
        return $this->render('SeguridadBundle:Vistas_Usuarios:lista_usuarioseliminados.html.twig', array('usuarios'=>$usuarios));
    }
    public function activarusuarioAction($id)
    {
        $em=  $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('SeguridadBundle:Usuario')->findOneById($id);
        $usuario->setEstado(0);
        $usuario->setAccion("Activado");
        $em->persist($usuario);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Se activado el usuario: '.$usuario->getNombre().''
        );
        return $this->redirect($this->generateUrl('listado_eliminados'));
    }
    public function activar_UsuariosMarcadosAction($elementos)
    {
        $datosUsuarios = explode(",§,", $elementos);
        $em=  $this->getDoctrine()->getManager();
        for($i=0;$i<count($datosUsuarios);$i++)
        {
            $usuario = $em->getRepository('SeguridadBundle:Usuario')->findOneById($datosUsuarios[$i]);
            $usuario->setEstado(0);
            $usuario->setAccion("Activado");
            $em->persist($usuario);
            $em->flush();

        }
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Se han activado los usuarios marcados.'
        );
        return $this->redirect($this->generateUrl('listado_eliminados'));

    }
   public function insertarldapAction(){
        $request = $this->getRequest();
        $em=  $this->getDoctrine()->getManager();
        $ldap = $em->getRepository('SeguridadBundle:ldap')->findAll();
        if(count($ldap) > 0){
            return $this->redirect($this->generateUrl('verldap',array('id'=>$ldap[0]->getId())));
        }
        else{
            $ldap = new ldap();
            $form = $this->createForm(new ldapType(),$ldap);
            $form->handleRequest($request);

            if($form->isValid()){
                $em->persist($ldap);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se ha configurado el servidor ldap: '.$ldap->getNombre().''
                );
                return $this->redirect($this->generateUrl('verldap',array('id'=>$ldap->getId())));

            }
            return $this->render('SeguridadBundle:Vistas_Usuarios:ldap.html.twig',array('form'=>$form->createView(),'active'=>2));
        }
    }

    public function verldapAction($id){
        $em=  $this->getDoctrine()->getManager();
        $ldap = $em->getRepository('SeguridadBundle:ldap')->findOneById($id);
        return $this->render('SeguridadBundle:Vistas_Usuarios:verldap.html.twig',array('ldap'=>$ldap,'active'=>2));
    }
    public function editarldapAction($id){
        $request = $this->getRequest();
        $em=  $this->getDoctrine()->getManager();
        $ldap = $em->getRepository('SeguridadBundle:ldap')->findOneById($id);
        $form = $this->createForm(new ldapType(),$ldap);
        $passOriginal = $form->getData()->getPass();
        $form->handleRequest($request);
        if($form->isValid()){
            if($ldap->getPass()==null){
                $ldap->setPass($passOriginal);
            }
            $em->persist($ldap);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el servidor ldap: '.$ldap->getNombre().''
            );
            return $this->redirect($this->generateUrl('verldap',array('id'=>$ldap->getId())));

        }
        return $this->render('SeguridadBundle:Vistas_Usuarios:editarldap.html.twig',array('form'=>$form->createView(),'ldap'=>$ldap,'active'=>2));
    }

    public function UsuariosldapAction()
    {
        $em=  $this->getDoctrine()->getManager();
        $users =  $em->getRepository('SeguridadBundle:UsuarioLDAP')->findAll();
        $contador = count($users);
        $Nameserver= $em->getRepository('SeguridadBundle:ldap')->findAll();
        $servidor = $Nameserver[0]->getNombre();
        $puerto = $Nameserver[0]->getPuerto();
        $base_dn = $Nameserver[0]->getBasedn();
        $user = $Nameserver[0]->getUser();
        $pass = $Nameserver[0]->getPass();
        $dominio = $Nameserver[0]->getDominio();
        $ds = @ldap_connect($servidor,$puerto);
        if($ds)
        {
            $r = @ldap_bind($ds,$dominio."\\".$user,$pass);
            if($r){
                $filtro="(objectClass=Person)";
                $sr=ldap_search($ds,$base_dn,$filtro);
                $info = ldap_get_entries($ds, $sr);
                for($i=0;$i<$info["count"];$i++)
                {
                    $flag = false;
                    for($j=0;$j<$contador;$j++)
                    {
                        $flag = false;
                        if($info[$i]['samaccountname'][0] == $users[$j]->getUsuario()){
                            $flag = true;
                            break;
                        }
                    }
                    if(!$flag){
                        $userldap = new UsuarioLDAP();
                        $userldap->setNombre(utf8_encode($info[$i]['name'][0]));
                        $userldap->setUsuario(utf8_encode($info[$i]['samaccountname'][0]));
                        $em->persist($userldap);
                    }
                }
                $em->flush();
                $users =  $em->getRepository('SeguridadBundle:UsuarioLDAP')->findAll();
                return $this->render('SeguridadBundle:Vistas_Usuarios:userldap.html.twig',array('user'=>$users));
            }
            else{
                $this->get('session')->getFlashBag()->add(
                    'noticeerror',
                    'For favor rectifique la configuración del servidor ldap, el usuario y contraseña.'
                );
                return $this->redirect($this->generateUrl('listadousuarios'));
            }

        }
        else{
            throw new AuthenticationException('Servidor Ldap no encontrado');
        }

    }

    public function cargarUsuarioLdapAction($id)
    {
        $em=  $this->getDoctrine()->getManager();
        $role = $em->getRepository('SeguridadBundle:Role')->findOneByRol("ROLE_USER");
        $usuarioldap = $em->getRepository('SeguridadBundle:UsuarioLDAP')->findOneById($id);
        $usuarios = $em->getRepository('SeguridadBundle:Usuario')->findAll();
        $contador = count($usuarios);
        for($i=0;$i<$contador;$i++)
        {
            if($usuarios[$i]->getUsuario()== $usuarioldap->getUsuario()){

                $this->get('session')->getFlashBag()->add(
                    'noticeerror',
                    'El usuario '.$usuarioldap->getUsuario(). ' ya existe.'
                );
                return $this->redirect($this->generateUrl('Usuariosldap'));

            }
        }
        $user = new Usuario();
        $user->setUsuario($usuarioldap->getUsuario());
        $user->setNombre($usuarioldap->getNombre());
        $user->setFechaExpiracion(new \DateTime('now + 100 Years'));
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
        $user->setRole($role);
        $user->setEstado(0);
        $user->setIsldap(1);
        $user->setAccion("Creado");
        $em->persist($user);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Se ha cargado el usuario de ladap '.$usuarioldap->getUsuario().'.'
        );
        return $this->redirect($this->generateUrl('Usuariosldap'));

    }

    public function cargarUserLdapAction($elementos)
    {
        $datosUsuarios = explode(",§,", $elementos);

        $em=  $this->getDoctrine()->getManager();
        $role = $em->getRepository('SeguridadBundle:Role')->findOneByRol("ROLE_USER");
        $usuarios = $em->getRepository('SeguridadBundle:Usuario')->findAll();
        $contador = count($usuarios);

        for($i=0;$i<count($datosUsuarios);$i++)
        {

            $usuario = $em->getRepository('SeguridadBundle:UsuarioLDAP')->findOneById($datosUsuarios[$i]);

            for($j=0;$j<$contador;$j++)
            {
              if($usuarios[$j]->getUsuario() == $usuario->getUsuario()){
                    $this->get('session')->getFlashBag()->add(
                        'noticeerror',
                        'El usuario '.$usuario->getUsuario(). ' ya existe.'
               );
               return $this->redirect($this->generateUrl('Usuariosldap'));
              }
            }

            $user = new Usuario();
            $user->setUsuario($usuario->getUsuario());
            $user->setNombre($usuario->getNombre());
            $user->setFechaExpiracion(new \DateTime('now + 100 Years'));
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setRole($role);
            $user->setEstado(0);
            $user->setIsldap(1);
            $user->setAccion("Creado");
            $em->persist($user);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Se han cargado los usuarios de ladap marcados.'
        );
        return $this->redirect($this->generateUrl('Usuariosldap'));
    }
    public function cambiarAction()
    {
        $request = Request::createFromGlobals();
        $em=  $this->getDoctrine()->getManager();
        $passAnterior =  $request->request->get('pass');
        $passNew = $request->request->get('passnuevo');
        $usuario = $this->get('security.context')->getToken()->getUser();

        //Encripar el passOriginal
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($usuario);
        $passwordCodificado = $encoder->encodePassword($passAnterior,$usuario->getSalt());
        $passwordOriginal = $usuario->getPassword();
        if(strcmp($passwordCodificado, $passwordOriginal) == 0){
            $nuevoPassword = $encoder->encodePassword($passNew,$usuario->getSalt());
            $usuario->setPassword($nuevoPassword);
            $aux = false;
            foreach($usuario->getRole() as $rol){
                if($rol->getRol() == "ROLE_ADMIN"){
                    $aux = true;
                    break;
                }
            }
            if($aux){
                $usuario->setFechaExpiracion(new \DateTime('now + 100 Years'));
            }
            else{
                $usuario->setFechaExpiracion(new \DateTime('now + 2 Month'));
            }
            $usuario->setAccion("Cambiado");
            $em->persist($usuario);
            $em->flush();
            return new Response("1");

        }
        else{
            return new Response("2");
        }

    }
    public function listadoTrazasUserAction(){
        return $this->render('SeguridadBundle:Vistas_Usuarios:trazasUser.html.twig');
    }
    public function trazasAction(Request $request){
        $em=  $this->getDoctrine()->getManager();
        //$request = $this->getRequest();
        $get = $request->query->all();
        $rResult = $em->getRepository('SeguridadBundle:trazaUser')->findTrazas($get,true);
        return new Response(json_encode($rResult));
    }
    public function userAjaxAction($username){
        $em=  $this->getDoctrine()->getManager();
        $user = $em->getRepository('SeguridadBundle:Usuario')->findOneByUsuario($username);
        return $this->render('SeguridadBundle:Vistas_Usuarios:userAjax.html.twig',array('user'=>$user));

    }



}