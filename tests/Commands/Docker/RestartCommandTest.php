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

namespace TeamNeusta\Magedev\Test\Commands\Docker;

use Mockery as m;
use TeamNeusta\Magedev\Commands\Docker\RestartCommand;

/**
 * Class: RestartCommandTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class RestartCommandTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $command = m::mock('\Symfony\Component\Console\Command\Command')->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('execute')->times(2);

        $application = m::mock('\Symfony\Component\Console\Application');
        $application->shouldReceive('find')->with('docker:stop')->andReturn($command);
        $application->shouldReceive('find')->with('docker:start')->andReturn($command);

        $command = m::mock(
            RestartCommand::class,
            [
                'getApplication' => $application,
            ]
        );
        $command->execute($input, $output);
    }
}
