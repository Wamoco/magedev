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

namespace TeamNeusta\Magedev\Commands\Db;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: DumpCommand.
 *
 * @see AbstractCommand
 */
class DumpCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;


    /**
     * __construct.
     *
     * @param ShellService $shellService
     */
    public function __construct(
        ShellService $shellService
    )
    {
        parent::__construct();
        $this->shellService = $shellService;
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('db:dump');
        $this->setDescription('dump db');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dumpFile = 'dump.sql'; //TODO: make this configurable

        $this->shellService->execute('mysqldump --column-statistics=0 -h 0.0.0.0 -u root -proot magento > ' . $dumpFile);

        parent::execute($input, $output);
    }
}
