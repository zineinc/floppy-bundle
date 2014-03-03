<?php


namespace ZineInc\StorageBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('zineinc_storage');

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
                    ->defaultValue(5)
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
                    ->defaultValue('file')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('plupload')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('js')
                                    ->defaultValue('https://raw.github.com/moxiecode/plupload/master/js/plupload.full.min.js')
                                ->end()
                                ->scalarNode('swf')
                                    ->defaultValue('https://raw.github.com/moxiecode/plupload/master/js/Moxie.swf')
                                ->end()
                                ->scalarNode('xap')
                                    ->defaultValue('https://raw.github.com/moxiecode/plupload/master/js/Moxie.xap')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}