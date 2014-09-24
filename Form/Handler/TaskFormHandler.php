<?php

namespace Cekurte\InsightlyTaskBundle\Form\Handler;

use Cekurte\GeneratorBundle\Form\Handler\CekurteFormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Task handler.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class TaskFormHandler extends CekurteFormHandler
{
    /**
     * @var \Insightly
     */
    protected $insightlyService;

    /**
     * Set a instance of service Insightly
     *
     * @param \Insightly $insightlyService
     *
     * @return TaskFormHandler
     */
    public function setInsightlyService(\Insightly $insightlyService)
    {
        $this->insightlyService = $insightlyService;

        return $this;
    }

    /**
     * Get a instance of service Insightly
     *
     * @return \Insightly
     */
    public function getInsightlyService()
    {
        return $this->insightlyService;
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        if ($this->formIsValid()) {

            $data = $this->getForm()->getData();

            try {

                $this->getManager()->getConnection()->beginTransaction();

                $response = $this->getInsightlyService()->addTask(array(
                    'TITLE'                 => $data->getSubject(),
                    'PRIORITY'              => 3,
                    'PUBLICLY_VISIBLE'      => true,
                    'COMPLETED'             => false,
                    'RESPONSIBLE_USER_ID'   => 658773,
                    'OWNER_USER_ID'         => 658773,
                    'DETAILS'               => $data->getContent(),
                ));

                if (!$response instanceof \stdClass) {
                    throw new \Exception('Ocorreu um erro ao abrir o chamado, por favor, tente mais tarde.');
                }

                $data->setInsightlyTaskId($response->TASK_ID);

                $this->getManager()->persist($data);
                $this->getManager()->flush();

                $this->getManager()->getConnection()->commit();

                $this->getFlashBag()->add('message', array(
                    'type'      => 'success',
                    'message'   => 'Ticket de chamado registrado com sucesso. Em breve nossa equipe entrará em contato com você.',
                ));

                return $data->getId();

            } catch (\Exception $e) {

                $this->getManager()->getConnection()->rollBack();

                $this->getFlashBag()->add('message', array(
                    'type'      => 'error',
                    'message'   => $e->getMessage(),
                ));
            }
        }

        return false;
    }
}
