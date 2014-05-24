<?php


namespace Floppy\Bundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('floppy');

        $root->children()
                ->arrayNode('endpoint')
                    ->children()
                        ->scalarNode('host')
                            ->cannotBeEmpty()
                            ->isRequired()
                        ->end()
                        ->scalarNode('protocol')
                            ->defaultValue('http')
                        ->end()
                        ->scalarNode('path')
                            ->defaultValue('')
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('checksum_length')
                    ->defaultValue(-1)
                ->end()
                ->arrayNode('filepath_chooser')
                    ->children()
                        ->scalarNode('dir_count')
                            ->defaultValue(2)
                        ->end()
                        ->scalarNode('chars_for_dir')
                            ->defaultValue(3)
                        ->end()
                        ->scalarNode('orig_file_dir')
                            ->defaultValue('orig')
                        ->end()
                        ->scalarNode('variant_file_dir')
                            ->defaultValue('v')
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                ->scalarNode('secret_key')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('file_key')
                    ->defaultValue('file')
                ->end()
                ->booleanNode('enable_doctrine_file_type')
                    ->defaultTrue()
                ->end()
                ->scalarNode('doctrine_file_type_name')
                    ->defaultValue('floppy_file')
                    ->cannotBeEmpty()
                ->end()
                ->variableNode('file_type_extensions')
                    ->defaultValue(array(
                        'image' => array('jpg', 'jpeg', 'png', 'gif')
                    ))
                ->end()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('file_type_aliases')
                            ->defaultValue(array(
                                'image' => array(
                                    'name' => 'Images',
                                    'extensions' => array(),
                                ),
                            ))
                            ->useAttributeAsKey('alias', true)
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('alias')
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('name')
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->variableNode('extensions')
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('plupload')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('swf')
                                    ->defaultValue('%%request_base_path%%/bundles/floppy/plupload/Moxie.swf')
                                ->end()
                                ->scalarNode('xap')
                                    ->defaultValue('%%request_base_path%%/bundles/floppy/plupload/Moxie.xap')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('preview')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('image')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('width')
                                            ->defaultValue(80)
                                        ->end()
                                        ->scalarNode('height')
                                            ->defaultValue(80)
                                        ->end()
                                        ->variableNode('supported_extensions')
                                            ->defaultValue(array())
                                        ->end()
                                    ->end()
                                ->end()
                                    ->arrayNode('file')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('name')
                                            ->defaultValue('n-a')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}