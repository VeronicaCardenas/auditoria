<?php
namespace Modulos\SeguridadBundle\Entity\Repository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UsuarioRepository extends EntityRepository implements UserProviderInterface{
    
    public function findUsuariosEstado($estado)
    {
         $em = $this->getEntityManager();
         $consulta = $em->createQuery('SELECT u FROM SeguridadBundle:Usuario u
                                       WHERE u.estado= :estado');
         $consulta->setParameter('estado', $estado);
         return $consulta->getResult();
        
    }

    public function loadUserByUsername($username)
    {
        $q = $this->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.role', 'r')
            ->where('u.usuario = :usuario OR u.email = :email')
            ->setParameter('usuario', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();

        } catch (NoResultException $e) {

            $message = sprintf('Unable to find an active admin AcmeUserBundle:User object identified by"%s".',$username);
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }


    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.',$class));
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
   
}

?>
