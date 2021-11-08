<?php
/**
 * Greetings from Wamoco GmbH, Bremen, Germany.
 * @author Wamoco Team<info@wamoco.de>
 * @license See LICENSE.txt for license details.
 */

namespace TeamNeusta\Magedev\Commands\Db;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: SyncCommand.
 *
 * @see AbstractCommand
 */
class SyncCommand extends AbstractCommand
{
    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('db:sync');
        $this->setDescription('sync db');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $this->getApplication()->find('db:import')->execute($input, $output);
        $this->getApplication()->find('db:configsync')->execute($input, $output);
        $this->getApplication()->find('magento:up')->execute($input, $output);
        $this->getApplication()->find('magento:cache:clean')->execute($input, $output);
        $this->getApplication()->find('magento:reindex')->execute($input, $output);

        parent::execute($input, $output);
    }
}
