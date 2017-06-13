<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\EstadoCreditos;
use Modulos\PersonasBundle\Form\EstadoCreditosType;

/**
 * EstadoCreditos controller.
 *
 */
class EstadoCreditosController extends Controller
{

    /**
     * Lists all EstadoCreditos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->findAll();

        return $this->render('ModulosPersonasBundle:EstadoCreditos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EstadoCreditos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoCreditos();
        $form = $this->createForm(new EstadoCreditosType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el estado de crédito'
            );
            return $this->redirect($this->generateUrl('estadocreditos', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:EstadoCreditos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EstadoCreditos entity.
     *
     * @param EstadoCreditos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
   /* private function createCreateForm(EstadoCreditos $entity)
    {
        $form = $this->createForm(new EstadoCreditosType(), $entity, array(
            'action' => $this->generateUrl('estadocreditos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new EstadoCreditos entity.
     *
     */
   /* public function newAction()
    {
        $entity = new EstadoCreditos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:EstadoCreditos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a EstadoCreditos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoCreditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:EstadoCreditos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EstadoCreditos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoCreditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:EstadoCreditos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EstadoCreditos entity.
    *
    * @param EstadoCreditos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoCreditos $entity)
    {
        $form = $this->createForm(new EstadoCreditosType(), $entity, array(
            'action' => $this->generateUrl('estadocreditos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EstadoCreditos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoCreditos entity.');
        }

        $editForm = $this->createForm(new EstadoCreditosType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el estado de crédito'
            );

            return $this->redirect($this->generateUrl('estadocreditos', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:EstadoCreditos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a EstadoCreditos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el estado de crédito'
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
        return $this->redirect($this->generateUrl('estadocreditos'));
    }

    /**
     * Creates a form to delete a EstadoCreditos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estadocreditos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
