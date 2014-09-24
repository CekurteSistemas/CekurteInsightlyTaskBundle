<?php

namespace Cekurte\InsightlyTaskBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Cekurte\InsightlyTaskBundle\Entity\Task;

/**
 * Task Repository.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 0.1
 */
class TaskRepository extends EntityRepository
{
    /**
     * Search for records based on an entity
     *
     * @param Task $entity
     * @param string $sort
     * @param string $direction
     * @param array $additionalFields
     * @return \Doctrine\ORM\Query
     *
     * @author João Paulo Cercal <sistemas@cekurte.com>
     * @version 0.1
     */
    public function getQuery(Task $entity, $sort, $direction, $additionalFields = array())
    {
        $queryBuilder = $this->createQueryBuilder('ck');

        $entityFields = array(
            'subject' => $entity->getSubject(),
        );

        $data = array_merge($additionalFields, $entityFields);
            
        if (!empty($data['subject'])) {

            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('ck.subject', ':subject'))
                ->setParameter('subject', "%{$data['subject']}%")
            ;            
        }

        return $queryBuilder
            ->orderBy($sort, $direction)
            ->getQuery()
        ;
    }
}
