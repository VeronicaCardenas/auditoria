<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\CajaPersona;
use Modulos\PersonasBundle\Form\CajaPersonaType;

/**
 * CajaPersona controller.
 *
 */
class CajaPersonaController extends Controller
{

    /**
     * Lists all CajaPersona entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:CajaPersona')->findAll();

        return $this->render('ModulosPersonasBundle:CajaPersona:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CajaPersona entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CajaPersona();
        $form = $this->createForm(new CajaPersonaType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la caja persona'
            ); 

            return $this->redirect($this->generateUrl('cajapersona', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:CajaPersona:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CajaPersona entity.
     *
     * @param CajaPersona $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(CajaPersona $entity)
    {
        $form = $this->createForm(new CajaPersonaType(), $entity, array(
            'action' => $this->generateUrl('cajapersona_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new CajaPersona entity.
     *
     */
    /*public function newAction()
    {
        $entity = new CajaPersona();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:CajaPersona:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a CajaPersona entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:CajaPersona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CajaPersona entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:CajaPersona:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CajaPersona entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:CajaPersona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CajaPersona entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:CajaPersona:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CajaPersona entity.
    *
    * @param CajaPersona $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CajaPersona $entity)
    {
        $form = $this->createForm(new CajaPersonaType(), $entity, array(
            'action' => $this->generateUrl('cajapersona_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CajaPersona entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:CajaPersona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CajaPersona entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la caja persona'
            );
            return $this->redirect($this->generateUrl('cajapersona', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:CajaPersona:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CajaPersona entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:CajaPersona')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la caja persona'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('cajapersona'));
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
        return $this->redirect($this->generateUrl('cajapersona'));
    }


    /**
     * Creates a form to delete a CajaPersona entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cajapersona_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
