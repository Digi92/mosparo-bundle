<?php

/**
 * @package   MosparoBundle
 * @author    Arnaud RITTI <arnaud.ritti@gmail.com>
 * @copyright 2024 Arnaud RITTI
 * @license   MIT <https://github.com/arnaud-ritti/mosparo-bundle/blob/main/LICENSE.md>
 * @link      https://github.com/arnaud-ritti/mosparo-bundle
 */

declare(strict_types=1);

namespace Mosparo\MosparoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mosparo');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('project')
            ->beforeNormalization()
                ->ifTrue(static function ($v) {
                    $excludedKeys = ['default_project' => true];
                    if (\array_key_exists('projects', $v) || \array_key_exists('project', $v)) {
                        return false;
                    }

                    // Is there actually anything to use once excluded keys are considered?
                    return (bool) array_diff_key($v, $excludedKeys);
                })
                ->then(static function ($v) {
                    $project = [];
                    foreach ($v as $key => $value) {
                        $project[$key] = $v[$key];
                        unset($v[$key]);
                    }
                    $v['projects'] = [($v['default_project'] ?? 'default') => $project];

                    return $v;
                })
            ->end()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('default_project')->defaultValue('default')->end()
                ->arrayNode('projects')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('instance_url')->isRequired()->end()
                            ->scalarNode('uuid')->isRequired()->end()
                            ->scalarNode('public_key')->isRequired()->end()
                            ->scalarNode('private_key')->isRequired()->end()
                            ->booleanNode('verify_ssl')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
