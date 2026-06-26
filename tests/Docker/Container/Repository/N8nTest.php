<?php
/**
 * This file is part of the teamneusta/php-cli-magedev package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/mit-license MIT License
 */

namespace TeamNeusta\Magedev\Test\Docker\Image;

use Mockery as m;
use TeamNeusta\Magedev\Docker\Container\Repository\N8n;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class: N8nTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class N8nTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testGetConfig()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $input->shouldReceive('getArgument')->andReturn(null);
        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive('findPath');
        $fileHelper->shouldReceive('expandPath');
        $fileHelper->shouldReceive('fileExists')->andReturn(true);
        $fileHelper->shouldReceive('read')->andReturn('[]');
        $fileHelper->shouldReceive('createDirectory');

        $imageFactory = m::mock(ImageFactory::class);
        $config = new Config($input, $fileHelper);
        $config->load();
        $config->set('project_path', '/some/path/to/project');
        $config->set('home_path', '/home/someuser');
        $config->set('network_id', '582f685244a4');
        $config->set('env_vars', ['MYSQL_USER' => 'root', 'USERID' => 1000]);

        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $n8n = new N8n($config, $imageFactory, $nameBuilder);
        $containerConfig = $n8n->getConfig();
        self::assertSame(
            [
                '/some/path/to/project/.n8n-data:/home/node/.n8n:rw',
            ],
            $containerConfig->getHostConfig()->getBinds()
        );
        self::assertContains('N8N_SECURE_COOKIE=false', $containerConfig->getEnv());
        self::assertContains('NODE_FUNCTION_ALLOW_BUILTIN=fs', $containerConfig->getEnv());
        self::assertContains('N8N_BLOCK_FILE_ACCESS_TO_N8N_FILES=false', $containerConfig->getEnv());
    }

    public function testGetImage()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('env_vars')->andReturn([]);
        $fileHelper = m::mock(FileHelper::class);
        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run,from]");
        $contextBuilder->shouldReceive('__destruct');
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $imageFactory = new ImageFactory(
            $config,
            $fileHelper,
            $imageApiFactory,
            $nameBuilder
        );

        $n8n = new N8n($config, $imageFactory, $nameBuilder);
        self::assertSame(\TeamNeusta\Magedev\Docker\Image\Repository\N8n::class, get_class($n8n->getImage()));
    }

    public function testGetName()
    {
        $config = m::mock(Config::class);
        $imageFactory = m::mock(ImageFactory::class);
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $n8n = new N8n($config, $imageFactory, $nameBuilder);
        self::assertSame('n8n', $n8n->getName());
    }
}
