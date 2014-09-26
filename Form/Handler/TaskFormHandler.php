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
     * @var array
     */
    protected $insightlyDefaultParameters;

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
     * Set defaults params to insightly task service
     *
     * @param array $params
     *
     * @return TaskFormHandler
     */
    public function setInsightlyDefaultParameters(array $params = array())
    {
        $this->insightlyDefaultParameters = $params;

        return $this;
    }

    /**
     * Get a default parameter of insightly configuration
     *
     * @param string $parameter
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getInsightlyDefaultParameter($parameter)
    {
        if (!isset($this->insightlyDefaultParameters[$parameter])) {
            throw new \InvalidArgumentException(sprintf('The parameter %s is not isset!', $parameter));
        }

        return $this->insightlyDefaultParameters[$parameter];
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
                    'DETAILS'               => $data->getContent(),
                    'RESPONSIBLE_USER_ID'   => $this->getInsightlyDefaultParameter('responsible_user_id'),
                    'OWNER_USER_ID'         => $this->getInsightlyDefaultParameter('owner_user_id'),
                    'PROJECT_ID'            => $this->getInsightlyDefaultParameter('project_id'),
                    'CATEGORY_ID'           => $this->getInsightlyDefaultParameter('category_id'),
                    'PRIORITY'              => $this->getInsightlyDefaultParameter('priority'),
                    'PUBLICLY_VISIBLE'      => $this->getInsightlyDefaultParameter('publicly_visible'),
                    'COMPLETED'             => $this->getInsightlyDefaultParameter('completed'),
                ));

                if (!$response instanceof \stdClass) {
                    throw new \Exception('Ocorreu um erro ao abrir o chamado, por favor, tente mais tarde.');
                }

                $data
                    ->setInsightlyTaskId($response->TASK_ID)
                ;

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
