<?php

// src/SGSI/UserBundle/Security/TimeAuthenticator.php
namespace Modulos\SeguridadBundle\Security;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Modulos\ConfiguracionBundle\Entity\ldap;


class LdapAuthenticator implements SimpleFormAuthenticatorInterface
{
private $encoderFactory;
private $em;
public function __construct(EncoderFactoryInterface $encoderFactory,EntityManager $entityManager)
{
    $this->encoderFactory = $encoderFactory;
    $this->em = $entityManager;
}

    public function authenticateToken(TokenInterface $token, UserProviderInterface
        $userProvider, $providerKey)
        {   $passwordValid = false;
            try {
                $user = $userProvider->loadUserByUsername($token->getUsername());
                if($user->isCredentialsNonExpired()== FALSE)
                {
                    throw new AuthenticationException('Su contraseña ha expirado. Por favor contacte con el administrador del sistema.');
                }
                if($user->getEstado()==1)
                {
                    throw new AuthenticationException('El usuario ha sido eliminado. Por favor contacte con el administrador del sistema.');
                }

                } catch (UsernameNotFoundException $e){
                        throw new AuthenticationException('Credenciales incorrectas.');
                }

            if($user->getIsldap() == 1)
            {
             $ldap = $this->em->getRepository('SeguridadBundle:ldap')->findAll();
             $servidor = $ldap[0]->getNombre();
             $puerto = $ldap[0]->getPuerto();
             $dominio = $ldap[0]->getDominio();

             $ds = @ldap_connect($servidor,$puerto);
              if($ds)
              {
                  $B = $dominio."\\".$user->getUsername();
                  $r = @ldap_bind($ds,$B,$token->getCredentials());
                  if($r)
                  {  $passwordValid = true; }
                  else{
                      throw new AuthenticationException('For favor rectifique la configuración del servidor ldap, el usuario y contraseña.');
                  }

              }
              else
              {
                  throw new AuthenticationException('Servidor Ldap no encontrado');
              }
            }
            if(!$passwordValid){
            $encoder = $this->encoderFactory->getEncoder($user);
            $passwordValid = $encoder->isPasswordValid(
            $user->getPassword(),
            $token->getCredentials(),
            $user->getSalt()
            );
            }
            if ($passwordValid) {

            return new UsernamePasswordToken(
                    $user,
                    $user->getPassword(),
                    $providerKey,
                    $user->getRoles()
            );
           }
       throw new AuthenticationException('Usuario o contraseña incorrecta. Por favor rectifique');
    }
    public function supportsToken(TokenInterface $token, $providerKey)
    {
    return $token instanceof UsernamePasswordToken
    && $token->getProviderKey() === $providerKey;
    }
    public function createToken(Request $request, $username, $password, $providerKey)
    {
    return new UsernamePasswordToken($username, $password, $providerKey);
    }
    }