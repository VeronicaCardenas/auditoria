<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\ReglasDeCreditos;
use Modulos\PersonasBundle\Form\ReglasDeCreditosType;

/**
 * ReglasDeCreditos controller.
 *
 */
class ReglasDeCreditosController extends Controller
{

    /**
     * Lists all ReglasDeCreditos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:ReglasDeCreditos')->findAll();

        return $this->render(
            'ModulosPersonasBundle:ReglasDeCreditos:index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * Creates a new ReglasDeCreditos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ReglasDeCreditos();
        $form = $this->createForm(new ReglasDeCreditosType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la regla de credito'
            );

//            return $this->redirect($this->generateUrl('reglasdecreditos_show', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('reglasdecreditos'));
        }

        return $this->render(
            'ModulosPersonasBundle:ReglasDeCreditos:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a ReglasDeCreditos entity.
     *
     * @param ReglasDeCreditos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /* private function createCreateForm(ReglasDeCreditos $entity)
     {
         $form = $this->createForm(new ReglasDeCreditosType(), $entity, array(
             'action' => $this->generateUrl('reglasdecreditos_create'),
             'method' => 'POST',
         ));

         $form->add('submit', 'submit', array('label' => 'Create'));

         return $form;
     }*/

    /**
     * Displays a form to create a new ReglasDeCreditos entity.
     *
     */
    /*public function newAction()
    {
        $entity = new ReglasDeCreditos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:ReglasDeCreditos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a ReglasDeCreditos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ReglasDeCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReglasDeCreditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'ModulosPersonasBundle:ReglasDeCreditos:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ReglasDeCreditos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ReglasDeCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReglasDeCreditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'ModulosPersonasBundle:ReglasDeCreditos:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a ReglasDeCreditos entity.
     *
     * @param ReglasDeCreditos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ReglasDeCreditos $entity)
    {
        $form = $this->createForm(
            new ReglasDeCreditosType(),
            $entity,
            array(
                'action' => $this->generateUrl('reglasdecreditos_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ReglasDeCreditos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ReglasDeCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReglasDeCreditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la regla de credito'
            );

            return $this->redirect($this->generateUrl('reglasdecreditos', array('id' => $id)));
        }

        return $this->render(
            'ModulosPersonasBundle:ReglasDeCreditos:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ReglasDeCreditos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:ReglasDeCreditos')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la regla de credito'
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede eliminar porque tiene registros relacionados."
                    );

                    return $this->redirect($this->generateUrl('reglasdecreditos'));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        return $this->redirect($this->generateUrl('reglasdecreditos'));
    }


    /**
     * Creates a form to delete a ReglasDeCreditos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reglasdecreditos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
