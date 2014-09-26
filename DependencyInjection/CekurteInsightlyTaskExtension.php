<?php

namespace Cekurte\InsightlyTaskBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CekurteInsightlyTaskExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('cekurte_insightly_task_responsible_user_id', $config['responsible_user_id']);
        $container->setParameter('cekurte_insightly_task_owner_user_id', $config['owner_user_id']);
        $container->setParameter('cekurte_insightly_task_project_id', $config['project_id']);
        $container->setParameter('cekurte_insightly_task_category_id', $config['category_id']);
        $container->setParameter('cekurte_insightly_task_priority', $config['priority']);
        $container->setParameter('cekurte_insightly_task_publicly_visible', $config['publicly_visible']);
        $container->setParameter('cekurte_insightly_task_completed', $config['completed']);
    }
}
