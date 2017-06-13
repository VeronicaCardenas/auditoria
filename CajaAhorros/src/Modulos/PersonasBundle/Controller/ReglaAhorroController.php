<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\ReglaAhorro;
use Modulos\PersonasBundle\Form\ReglaAhorroType;

/**
 * ReglaAhorro controller.
 *
 */
class ReglaAhorroController extends Controller
{

    /**
     * Lists all ReglaAhorro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:ReglaAhorro')->findAll();

        return $this->render('ModulosPersonasBundle:ReglaAhorro:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ReglaAhorro entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ReglaAhorro();
        $form = $this->createForm(new ReglaAhorroType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la regla de ahorro'
            ); 
            return $this->redirect($this->generateUrl('reglaahorro'));
        }

        return $this->render('ModulosPersonasBundle:ReglaAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ReglaAhorro entity.
     *
     * @param ReglaAhorro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
  /*  private function createCreateForm(ReglaAhorro $entity)
    {
        $form = $this->createForm(new ReglaAhorroType(), $entity, array(
            'action' => $this->generateUrl('reglaahorro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new ReglaAhorro entity.
     *
     */
 /*   public function newAction()
    {
        $entity = new ReglaAhorro();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:ReglaAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a ReglaAhorro entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ReglaAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReglaAhorro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ReglaAhorro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ReglaAhorro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ReglaAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReglaAhorro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ReglaAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ReglaAhorro entity.
    *
    * @param ReglaAhorro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ReglaAhorro $entity)
    {
        $form = $this->createForm(new ReglaAhorroType(), $entity, array(
            'action' => $this->generateUrl('reglaahorro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ReglaAhorro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ReglaAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReglaAhorro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la regla'
            );
            return $this->redirect($this->generateUrl('reglaahorro', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:ReglaAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ReglaAhorro entity.
     *
     */

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:ReglaAhorro')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el documento contable'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('reglaahorro'));
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
        return $this->redirect($this->generateUrl('reglaahorro'));
    }
   

    /**
     * Creates a form to delete a ReglaAhorro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reglaahorro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
