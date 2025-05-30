<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\CustomReportsBundle\DependencyInjection;

use Pimcore\Bundle\CoreBundle\DependencyInjection\ConfigurationHelper;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pimcore_custom_reports');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->addDefaultsIfNotSet();

        $rootNode
            ->children()
                    ->arrayNode('definitions')
                        ->normalizeKeys(false)
                        ->prototype('array')
                            ->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('name')->end()
                                ->scalarNode('niceName')->end()
                                ->scalarNode('sql')->end()
                                ->scalarNode('group')->end()
                                ->scalarNode('groupIconClass')->end()
                                ->scalarNode('iconClass')->end()
                                ->booleanNode('menuShortcut')->end()
                                ->scalarNode('reportClass')->end()
                                ->scalarNode('chartType')->end()
                                ->scalarNode('pieColumn')->end()
                                ->scalarNode('pieLabelColumn')->end()
                                ->variableNode('xAxis')->end()
                                ->variableNode('yAxis')->end()
                                ->integerNode('modificationDate')->end()
                                ->integerNode('creationDate')->end()
                                ->booleanNode('shareGlobally')->end()
                                ->variableNode('sharedUserNames')->end()
                                ->variableNode('sharedRoleNames')->end()
                                ->arrayNode('dataSourceConfig')
                                    ->prototype('variable')
                                    ->end()
                                ->end()
                                ->arrayNode('columnConfiguration')
                                    ->prototype('variable')
                                    ->end()
                                ->end()
                                ->booleanNode('pagination')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('adapters')
                        ->useAttributeAsKey('name')
                            ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end();

        ConfigurationHelper::addConfigLocationWithWriteTargetNodes($rootNode, ['custom_reports' => PIMCORE_CONFIGURATION_DIRECTORY . '/custom_reports']);

        return $treeBuilder;
    }
}
