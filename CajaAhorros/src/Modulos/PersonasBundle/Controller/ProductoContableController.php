<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\ProductoContable;
use Modulos\PersonasBundle\Form\ProductoContableType;

/**
 * ProductoContable controller.
 *
 */
class ProductoContableController extends Controller
{

    /**
     * Lists all ProductoContable entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:ProductoContable')->findAll();

        return $this->render('ModulosPersonasBundle:ProductoContable:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ProductoContable entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ProductoContable();
        $form = $this->createForm(new ProductoContableType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('productocontable', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:ProductoContable:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ProductoContable entity.
     *
     * @param ProductoContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
   /* private function createCreateForm(ProductoContable $entity)
    {
        $form = $this->createForm(new ProductoContableType(), $entity, array(
            'action' => $this->generateUrl('productocontable_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new ProductoContable entity.
     *
     */
    /*public function newAction()
    {
        $entity = new ProductoContable();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:ProductoContable:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a ProductoContable entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoContable entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoContable:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductoContable entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoContable entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoContable:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ProductoContable entity.
    *
    * @param ProductoContable $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProductoContable $entity)
    {
        $form = $this->createForm(new ProductoContableType(), $entity, array(
            'action' => $this->generateUrl('productocontable_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProductoContable entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoContable entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('productocontable', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:ProductoContable:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ProductoContable entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:ProductoContable')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductoContable entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('productocontable'));
    }

    /**
     * Creates a form to delete a ProductoContable entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productocontable_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
