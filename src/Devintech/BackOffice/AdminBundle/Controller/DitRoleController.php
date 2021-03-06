<?php

namespace App\Devintech\BackOffice\AdminBundle\Controller;

use App\Devintech\Service\MetierManagerBundle\Form\DitRoleType;
use App\Devintech\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Devintech\Service\MetierManagerBundle\Entity\DitRole;

/**
 * Class DitRoleController
 */
class DitRoleController extends Controller
{
    /**
     * Afficher tout les rôles
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_USER_ROLE);

        // Récupérer tout les role
        $_roles = $_role_manager->getAllDitRole();

        return $this->render('AdminBundle:DitRole:index.html.twig', array(
            'roles' => $_roles
        ));
    }

    /**
     * Affichage page modification rôle
     * @param DitRole $_role
     * @return Render page
     */
    public function editAction(DitRole $_role)
    {
        if (!$_role) {
            throw $this->createNotFoundException('Unable to find DitRole entity.');
        }

        $_edit_form = $this->createEditForm($_role);

        return $this->render('AdminBundle:DitRole:edit.html.twig', array(
            'role'      => $_role,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création rôle
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_USER_ROLE);

        $_role = new DitRole();
        $_form = $this->createCreateForm($_role);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement rôle
            $_role_manager->saveDitRole($_role, 'new');

            $_role_manager->setFlash('success', "Rôle ajouté");

            return $this->redirect($this->generateUrl('role_index'));
        }

        return $this->render('AdminBundle:DitRole:add.html.twig', array(
            'role' => $_role,
            'form' => $_form->createView()
        ));
    }

    /**
     * Modification rôle
     * @param Request $_request requête
     * @param DitRole $_role
     * @return Render page
     */
    public function updateAction(Request $_request, DitRole $_role)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_USER_ROLE);

        if (!$_role) {
            throw $this->createNotFoundException('Unable to find DitRole entity.');
        }

        $_edit_form = $this->createEditForm($_role);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_role_manager->saveDitRole($_role, 'update');

            $_role_manager->setFlash('success', "Rôle modifié");

            return $this->redirect($this->generateUrl('role_index'));
        }

        return $this->render('AdminBundle:DitRole:edit.html.twig', array(
            'role'      => $_role,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition rôle
     * @param DitRole $_role The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DitRole $_role)
    {
        $_form = $this->createForm(DitRoleType::class, $_role, array(
            'action' => $this->generateUrl('role_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création rôle
     * @param DitRole $_role The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DitRole $_role)
    {
        $_form = $this->createForm(DitRoleType::class, $_role, array(
            'action' => $this->generateUrl('role_update', array('id' => $_role->getId())),
            'method' => 'PUT'
        ));

        return $_form;
    }

    /**
     * Suppression rôle
     * @param Request $_request requête
     * @param DitRole $_role
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, DitRole $_role)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_USER_ROLE);

        $_form = $this->createDeleteForm($_role);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression rôle
            $_role_manager->deleteDitRole($_role);

            $_role_manager->setFlash('success', 'Rôle supprimé');
        }

        return $this->redirectToRoute('role_index');
    }

    /**
     * Création formulaire de suppression rôle
     * @param DitRole $_role The DitRole entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DitRole $_role)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('role_delete', array('id' => $_role->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste rôle
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_USER_ROLE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            if ($_ids == null) {
                $_role_manager->setFlash('error', 'Veuillez sélectionner un élément à supprimer');

                return $this->redirect($this->generateUrl('role_index'));
            }
            $_role_manager->deleteGroupDitRole($_ids);
        }

        $_role_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('role_index'));
    }
}