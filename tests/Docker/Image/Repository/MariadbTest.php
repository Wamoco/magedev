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

namespace TeamNeusta\Magedev\Test\Docker\Image\Repository;

use Mockery as m;
use TeamNeusta\Magedev\Docker\Image\Repository\Mariadb;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;

/**
 * Class: MariadbTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class MariadbTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testConfigureUsesDefaultVersion()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('env_vars')->andReturn([]);
        $config->shouldReceive('optionExists')->with('mariadb_version')->andReturn(false);
        $imageFactory = m::mock(ImageFactory::class);
        $fileHelper = m::mock(FileHelper::class);
        // no version specific config file, fall back to the shared one
        $fileHelper->shouldReceive('fileExists')
            ->with('var/Docker/mysql/mariadb-10.11.cnf')->andReturn(false);
        $fileHelper->shouldReceive('read')
            ->with('var/Docker/mysql/mariadb.cnf')->andReturn('mariadb.cnf content');
        $fileHelper->shouldReceive('read')
            ->with('var/Docker/mysql/my.cnf')->andReturn('my.cnf content');

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,run,add,from]")->makePartial();
        $contextBuilder->shouldReceive('from')
            ->with('mariadb:10.11')->times(1);
        $contextBuilder->shouldReceive('run')
            ->with('usermod -u '.getmyuid().' mysql')->times(1);

        $contextBuilder->shouldReceive('add')
            ->with('/etc/mysql/conf.d/z99-docker.cnf', 'mariadb.cnf content')->times(1);
        $contextBuilder->shouldReceive('add')
            ->with('/root/.my.cnf', 'my.cnf content')->times(1);
        $contextBuilder->shouldReceive('add')
            ->with('/var/www/.my.cnf', 'my.cnf content')->times(1);

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Mariadb(
            $config,
            $imageFactory,
            $fileHelper,
            $contextBuilder,
            $imageApiFactory,
            $nameBuilder
        );
        $image->configure();

        self::assertSame('mariadb', $image->getName());
    }

    public function testConfigureUsesConfiguredVersion()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('env_vars')->andReturn([]);
        $config->shouldReceive('optionExists')->with('mariadb_version')->andReturn(true);
        $config->shouldReceive('get')->with('mariadb_version')->andReturn('11.4');
        $imageFactory = m::mock(ImageFactory::class);
        $fileHelper = m::mock(FileHelper::class);
        // a version specific config file wins over the shared one
        $fileHelper->shouldReceive('fileExists')
            ->with('var/Docker/mysql/mariadb-11.4.cnf')->andReturn(true);
        $fileHelper->shouldReceive('read')
            ->with('var/Docker/mysql/mariadb-11.4.cnf')->andReturn('mariadb-11.4.cnf content');
        $fileHelper->shouldReceive('read')
            ->with('var/Docker/mysql/my.cnf')->andReturn('my.cnf content');

        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,run,add,from]")->makePartial();
        $contextBuilder->shouldReceive('from')
            ->with('mariadb:11.4')->times(1);
        $contextBuilder->shouldReceive('run')
            ->with('usermod -u '.getmyuid().' mysql')->times(1);

        $contextBuilder->shouldReceive('add')
            ->with('/etc/mysql/conf.d/z99-docker.cnf', 'mariadb-11.4.cnf content')->times(1);
        $contextBuilder->shouldReceive('add')
            ->with('/root/.my.cnf', 'my.cnf content')->times(1);
        $contextBuilder->shouldReceive('add')
            ->with('/var/www/.my.cnf', 'my.cnf content')->times(1);

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Mariadb(
            $config,
            $imageFactory,
            $fileHelper,
            $contextBuilder,
            $imageApiFactory,
            $nameBuilder
        );
        $image->configure();
    }
}
