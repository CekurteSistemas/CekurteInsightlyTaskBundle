<?php

namespace Cekurte\InsightlyTaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cekurte\GeneratorBundle\Controller\CekurteController;
use Cekurte\GeneratorBundle\Controller\RepositoryInterface;
use Cekurte\GeneratorBundle\Office\PHPExcel as CekurtePHPExcel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Cekurte\InsightlyTaskBundle\Entity\Task;
use Cekurte\InsightlyTaskBundle\Entity\Repository\TaskRepository;
use Cekurte\InsightlyTaskBundle\Form\Type\TaskFormType;
use Cekurte\InsightlyTaskBundle\Form\Handler\TaskFormHandler;

/**
 * Task controller.
 *
 * @Route("/task")
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class TaskController extends CekurteController implements RepositoryInterface
{
    /**
     * Get a instance of TaskRepository.
     *
     * @return TaskRepository
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function getEntityRepository()
    {
        return $this->get('doctrine')->getRepository('CekurteInsightlyTaskBundle:Task');
    }

    /**
     * Lists all Task entities.
     *
     * @Route("/", defaults={"page"=1, "sort"="ck.id", "direction"="asc"}, name="admin_task")
     * @Route("/page/{page}/sort/{sort}/direction/{direction}/", defaults={"page"=1, "sort"="ck.id", "direction"="asc"}, name="admin_task_paginator")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEINSIGHTLYTASKBUNDLE_TASK, ROLE_ADMIN")
     *
     * @param int $page
     * @param string $sort
     * @param string $direction
     * @return array
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function indexAction($page, $sort, $direction)
    {
        $form = $this->createForm(new TaskFormType(), new Task(), array(
            'search' => true,
        ));

        if ($this->get('session')->has('search_task')) {

            $form->bind($this->get('session')->get('search_task'));
        }

        $query = $this->getEntityRepository()->getQuery($form->getData(), $sort, $direction);

        $pagination = $this->getPagination($query, $page);

        $pagination->setUsedRoute('admin_task_paginator');

        return array(
            'pagination'    => $pagination,
            'delete_form'   => $this->createDeleteForm()->createView(),
            'search_form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to search a Task entity.
     *
     * @Route("/search", name="admin_task_search")
     * @Method({"GET", "POST"})
     * @Template()
     * @Secure(roles="ROLE_CEKURTEINSIGHTLYTASKBUNDLE_TASK, ROLE_ADMIN")
     *
     * @param Request $request
     * @return RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function searchAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->get('session')->set('search_task', $request);
        } else {
            $this->get('session')->remove('search_task');
        }

        return $this->redirect($this->generateUrl('admin_task'));
    }

    /**
     * Export Task entities to Excel.
     *
     * @Route("/export/sort/{sort}/direction/{direction}/", defaults={"sort"="ck.id", "direction"="asc"}, name="admin_task_export")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_EXPORT")
     *
     * @param string $sort
     * @param string $direction
     * @return StreamedResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function exportAction($sort, $direction)
    {
        $form = $this->createForm(new TaskFormType(), new Task(), array(
            'search' => true,
        ));

        if ($this->get('session')->has('search_task')) {

            $form->bind($this->get('session')->get('search_task'));
        }

        $query = $this->getEntityRepository()->getQuery($form->getData(), $sort, $direction);

        $translator = $this->get('translator');

        $office = new CekurtePHPExcel(sprintf(
            '%s %s',
            $translator->trans('Report of'),
            $translator->trans('Task')
        ));

        $office
            ->setHeader(array(
                'subject' => $translator->trans('Subject'),
                'content' => $translator->trans('Content'),
            ))
            ->setData($query->getArrayResult())
            ->build()
        ;

        return $office->createResponse();
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="admin_task_create")
     * @Method("POST")
     * @Template("CekurteInsightTaskBundle:Task:new.html.twig")
     * @Secure(roles="ROLE_CEKURTEINSIGHTLYTASKBUNDLE_TASK_CREATE, ROLE_ADMIN")
     *
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new TaskFormType(), new Task());

        $handler = new TaskFormHandler(
            $form,
            $this->getRequest(),
            $this->get('doctrine')->getManager(),
            $this->get('session')->getFlashBag()
        );

        $handler->setInsightlyService($this->get('cekurte_insightly'));

        if ($id = $handler->save()) {
            return $this->redirect($this->generateUrl('admin_task_show', array('id' => $id)));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new", name="admin_task_new")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEINSIGHTLYTASKBUNDLE_TASK_CREATE, ROLE_ADMIN")
     *
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function newAction()
    {
        $form = $this->createForm(new TaskFormType(), new Task());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/{id}", name="admin_task_show")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_CEKURTEINSIGHTLYTASKBUNDLE_TASK_RETRIEVE, ROLE_ADMIN")
     *
     * @param int $id
     * @return array|Response
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function showAction($id)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $this->createDeleteForm()->createView(),
        );
    }
}
