<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\EstadoAhorro;
use Modulos\PersonasBundle\Form\EstadoAhorroType;

/**
 * EstadoAhorro controller.
 *
 */
class EstadoAhorroController extends Controller
{

    /**
     * Lists all EstadoAhorro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->findAll();

        return $this->render('ModulosPersonasBundle:EstadoAhorro:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EstadoAhorro entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoAhorro();
        $form = $this->createForm(new EstadoAhorroType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el estado de ahorro'
            );

            return $this->redirect($this->generateUrl('estadoahorro', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:EstadoAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EstadoAhorro entity.
     *
     * @param EstadoAhorro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoAhorro $entity)
    {
        $form = $this->createForm(new EstadoAhorroType(), $entity, array(
            'action' => $this->generateUrl('estadoahorro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoAhorro entity.
     *
     */
    public function newAction()
    {
        $entity = new EstadoAhorro();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:EstadoAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EstadoAhorro entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoAhorro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:EstadoAhorro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EstadoAhorro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoAhorro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:EstadoAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EstadoAhorro entity.
    *
    * @param EstadoAhorro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoAhorro $entity)
    {
        $form = $this->createForm(new EstadoAhorroType(), $entity, array(
            'action' => $this->generateUrl('estadoahorro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EstadoAhorro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoAhorro entity.');
        }
        
        $editForm = $this->createForm(new EstadoAhorroType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el estado de ahorro'
            );

            return $this->redirect($this->generateUrl('estadoahorro', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:EstadoAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a EstadoAhorro entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
       
          $em = $this->getDoctrine()->getManager();
          $entity = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find($id);

          try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el estado de ahorro'
            );
          }
       
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('estadocreditos'));
                }
                else
                {
                    throw $e;
                }
            }
            else
            {
                throw $e;
            }
        }

        return $this->redirect($this->generateUrl('estadoahorro'));
    }

    /**
     * Creates a form to delete a EstadoAhorro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estadoahorro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
