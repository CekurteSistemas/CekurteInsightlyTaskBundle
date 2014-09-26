<?php

namespace Cekurte\InsightlyTaskBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Synchronize tasks insightly
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 */
class SynchronizeCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('cekurte:insightly:task:synchronize')
            ->setDescription('Synchronize tasks insightly.')
        ;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * Get a instance of service Insightly
     *
     * @return \Insightly
     */
    public function getInsightlyService()
    {
        return $this->getContainer()->get('cekurte_insightly');
    }

    /**
     * Execute the command's
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getDoctrine();

        $tasks = $doctrine->getManager()->getRepository('CekurteInsightlyTaskBundle:Task')->findAll();

        $output->writeln('Starting ...');

        if (!empty($tasks)) {

            foreach ($tasks as $task) {

                try {

                    $doctrine->getConnection()->beginTransaction();

                    $output->writeln(sprintf('Running the synchronize to task <info>[%s] => %s</info> ...', $task->getId(), $task->getSubject()));

                    $response = $this->getInsightlyService()->getTask($task->getInsightlyTaskId());

                    if (!$response instanceof \stdClass) {
                        throw new \Exception(sprintf('<error>An error occurred while processing the task: [%s] => %s ...</error>', $task->getId(), $task->getSubject()));
                    }

                    $output->writeln('Getting task information ...');

                    $taskDateCreated = \DateTime::createFromFormat('Y-m-d H:i:s', $response->DATE_CREATED_UTC, new \DateTimeZone('UTC'));
                    $taskDateUpdated = \DateTime::createFromFormat('Y-m-d H:i:s', $response->DATE_UPDATED_UTC, new \DateTimeZone('UTC'));

                    $task
                        ->setInsightlyTaskDateCreated($taskDateCreated->setTimezone(new \DateTimeZone('America/Sao_Paulo')))
                        ->setInsightlyTaskDateUpdated($taskDateUpdated->setTimezone(new \DateTimeZone('America/Sao_Paulo')))
                        ->setInsightlyTaskStatus($response->STATUS)
                        ->setInsightlyTaskCompleted($response->COMPLETED)
                        ->setInsightlyTaskPercentComplete($response->PERCENT_COMPLETE)
                    ;

                    $output->writeln('Saving task information on database ...');

                    $doctrine->getManager()->persist($task);
                    $doctrine->getManager()->flush();

                    $doctrine->getConnection()->commit();

                } catch (\Exception $e) {

                    $doctrine->getConnection()->rollback();

                    $output->writeln(sprintf('<error>Exception: %s</error>', $e->getMessage()));
                }
            }

            $output->writeln('Process completed!');

        } else {
            $output->writeln('No tasks found!');
        }
    }
} 