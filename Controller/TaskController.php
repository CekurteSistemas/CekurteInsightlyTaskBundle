<?php

namespace Cekurte\InsightlyTaskBundle\Controller;

use Cekurte\InsightlyTaskBundle\Entity\Comment;
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
use Symfony\Component\Validator\Exception\InvalidArgumentException;

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
     * Creates a new Comment entity.
     *
     * @Route("/{id}/comment", name="admin_task_create_comment")
     * @Method("POST")
     * @Secure(roles="ROLE_CEKURTEINSIGHTLYTASKBUNDLE_TASK_CREATE, ROLE_ADMIN")
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function createCommentAction(Request $request, $id)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        try {

            $insightly = $this->get('cekurte_insightly');

            $commentStr = trim($request->get('comment', ''));

            if (empty($commentStr)) {
                throw new InvalidArgumentException('The comment cannot be empty');
            }

            $comment                    = new \stdClass();
            $comment->BODY              = $commentStr;
            $comment->OWNER_USER_ID     = $this->container->getParameter('cekurte_insightly_task_responsible_user_id');

            $response = $insightly->addTaskComment($entity->getInsightlyTaskId(), $comment);

            if (!$response instanceof \stdClass) {
                throw new \Exception('The comment cannot be save on API.');
            }

            $createdAt = \DateTime::createFromFormat('Y-m-d H:i:s', $response->DATE_CREATED_UTC, new \DateTimeZone('UTC'));

            $comment = new Comment();

            $comment
                ->setInsightlyTaskCommentId($response->COMMENT_ID)
                ->setCreatedAt($createdAt->setTimezone(new \DateTimeZone('America/Sao_Paulo')))
                ->setContent($commentStr)
                ->setTask($entity)
            ;

            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('message', array(
                'type'      => 'success',
                'message'   => 'The comment was created with successfully',
            ));

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add('message', array(
                'type'      => 'error',
                'message'   => $e->getMessage(),
            ));
        }

        return $this->redirect($this->generateUrl('admin_task_show', array(
            'id' => $id,
        )));
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="admin_task_create")
     * @Method("POST")
     * @Template("CekurteInsightlyTaskBundle:Task:new.html.twig")
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

        $handler
            ->setInsightlyService($this->get('cekurte_insightly'))
            ->setInsightlyDefaultParameters(array(
                'responsible_user_id'   => $this->container->getParameter('cekurte_insightly_task_responsible_user_id'),
                'owner_user_id'         => $this->container->getParameter('cekurte_insightly_task_owner_user_id'),
                'project_id'            => $this->container->getParameter('cekurte_insightly_task_project_id'),
                'category_id'           => $this->container->getParameter('cekurte_insightly_task_category_id'),
                'priority'              => $this->container->getParameter('cekurte_insightly_task_priority'),
                'publicly_visible'      => $this->container->getParameter('cekurte_insightly_task_publicly_visible'),
                'completed'             => $this->container->getParameter('cekurte_insightly_task_completed'),
            ))
        ;

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
