<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BlockBundle\Tests;

use PHPUnit\Framework\TestCase;
use Sonata\BlockBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testOptions()
    {
        $defaultTemplates = [
            'SonataPageBundle:Block:block_container.html.twig' => 'SonataPageBundle template',
            'SonataSeoBundle:Block:block_social_container.html.twig' => 'SonataSeoBundle (to contain social buttons)',
        ];

        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration($defaultTemplates), [[
            'default_contexts' => ['cms'],
            'blocks' => ['my.block.type' => []],
        ]]);

        $expected = [
            'default_contexts' => [
                0 => 'cms',
            ],
            'profiler' => [
                'enabled' => '%kernel.debug%',
                'template' => 'SonataBlockBundle:Profiler:block.html.twig',
                'container_types' => [
                    0 => 'sonata.block.service.container',
                    1 => 'sonata.page.block.container',
                    2 => 'sonata.dashboard.block.container',
                    3 => 'cmf.block.container',
                    4 => 'cmf.block.slideshow',
                ],
            ],
            'context_manager' => 'sonata.block.context_manager.default',
            'http_cache' => [
                'handler' => 'sonata.block.cache.handler.default',
                'listener' => true,
            ],
            'templates' => [
                'block_base' => null,
                'block_container' => null,
            ],
            'container' => [
                'types' => [
                    0 => 'sonata.block.service.container',
                    1 => 'sonata.page.block.container',
                    2 => 'sonata.dashboard.block.container',
                    3 => 'cmf.block.container',
                    4 => 'cmf.block.slideshow',
                ],
                'templates' => $defaultTemplates,
            ],
            'blocks' => [
                'my.block.type' => [
                    'contexts' => ['cms'],
                    'cache' => 'sonata.cache.noop',
                    'settings' => [],
                    'templates' => [],
                ],
            ],
            'menus' => [],
            'blocks_by_class' => [],
            'exception' => [
                'default' => [
                    'filter' => 'debug_only',
                    'renderer' => 'throw',
                ],
                'filters' => [
                    'debug_only' => 'sonata.block.exception.filter.debug_only',
                    'ignore_block_exception' => 'sonata.block.exception.filter.ignore_block_exception',
                    'keep_all' => 'sonata.block.exception.filter.keep_all',
                    'keep_none' => 'sonata.block.exception.filter.keep_none',
                ],
                'renderers' => [
                    'inline' => 'sonata.block.exception.renderer.inline',
                    'inline_debug' => 'sonata.block.exception.renderer.inline_debug',
                    'throw' => 'sonata.block.exception.renderer.throw',
                ],
            ],
        ];

        $this->assertEquals($expected, $config);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid configuration for path "sonata_block": You cannot have different config options for sonata_block.profiler.container_types and sonata_block.container.types; the first one is deprecated, in case of doubt use the latter
     */
    public function testOptionsDuplicated()
    {
        $defaultTemplates = [
            'SonataPageBundle:Block:block_container.html.twig' => 'SonataPageBundle template',
            'SonataSeoBundle:Block:block_social_container.html.twig' => 'SonataSeoBundle (to contain social buttons)',
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration($defaultTemplates), [[
            'default_contexts' => ['cms'],
            'profiler' => ['container_types' => ['test_type']],
            'container' => ['types' => ['test_type2'], 'templates' => []],
        ]]);
    }
}
