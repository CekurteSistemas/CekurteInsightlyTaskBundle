<?php

namespace Cekurte\InsightlyTaskBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cekurte_insightly_task');

        $rootNode
            ->children()
                ->integerNode('responsible_user_id')->isRequired()->cannotBeEmpty()->end()
                ->integerNode('owner_user_id')->isRequired()->cannotBeEmpty()->end()
                ->integerNode('project_id')->isRequired()->cannotBeEmpty()->end()
                ->integerNode('category_id')->isRequired()->cannotBeEmpty()->end()
                ->integerNode('priority')->isRequired()->cannotBeEmpty()->min(1)->max(3)->end()
                ->booleanNode('publicly_visible')->isRequired()->end()
                ->booleanNode('completed')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
