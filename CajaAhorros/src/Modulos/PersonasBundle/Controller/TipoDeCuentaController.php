<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoDeCuenta;
use Modulos\PersonasBundle\Form\TipoDeCuentaType;

/**
 * TipoDeCuenta controller.
 *
 */
class TipoDeCuentaController extends Controller
{

    /**
     * Lists all TipoDeCuenta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoDeCuenta')->findAll();

        return $this->render('ModulosPersonasBundle:TipoDeCuenta:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoDeCuenta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoDeCuenta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de cuenta'
            );
            return $this->redirect($this->generateUrl('tipodecuenta', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:TipoDeCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoDeCuenta entity.
     *
     * @param TipoDeCuenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoDeCuenta $entity)
    {
        $form = $this->createForm(new TipoDeCuentaType(), $entity, array(
            'action' => $this->generateUrl('tipodecuenta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoDeCuenta entity.
     *
     */
    public function newAction()
    {
        $entity = new TipoDeCuenta();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoDeCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TipoDeCuenta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDeCuenta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoDeCuenta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeCuenta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDeCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoDeCuenta entity.
    *
    * @param TipoDeCuenta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoDeCuenta $entity)
    {
        $form = $this->createForm(new TipoDeCuentaType(), $entity, array(
            'action' => $this->generateUrl('tipodecuenta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoDeCuenta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de cuenta'
            );
            return $this->redirect($this->generateUrl('tipodecuenta', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:TipoDeCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TipoDeCuenta entity.
     *
     */
   public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCuenta')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('tipodecuenta'));
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
        return $this->redirect($this->generateUrl('tipodecuenta'));
    }

    /**
     * Creates a form to delete a TipoDeCuenta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipodecuenta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
