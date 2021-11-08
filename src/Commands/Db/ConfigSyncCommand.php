<?php
/**
 * Greetings from Wamoco GmbH, Bremen, Germany.
 * @author Wamoco Team<info@wamoco.de>
 * @license See LICENSE.txt for license details.
 */

namespace TeamNeusta\Magedev\Commands\Db;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\MagerunHelper;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: ConfigSyncCommand.
 *
 * @see AbstractCommand
 */
class ConfigSyncCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config               $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper $magerunHelper
     * @param \TeamNeusta\Magedev\Services\DockerService       $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->fileHelper = $fileHelper;
        $this->dockerService = $dockerService;
        parent::__construct();
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('db:configsync');
        $this->setDescription('sync config into local db using mageconfigsync');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceFolder = $this->config->get('source_folder');
        $this->checkPrerequisites();

        if (!$this->fileHelper->fileExists($sourceFolder . '/../etc/config/develop.yml')) {
            throw new \Exception('etc/config/develop.yml not found');
        }

        $this->dockerService->execute('vendor/bin/mageconfigsync load ../etc/config/develop.yml --env develop');
    }

    /**
     * checkPrerequisites.
     */
    public function checkPrerequisites()
    {
        if (!$this->isMageConfigSyncInstalled()) {
            throw new \Exception('sorry, mageconfigsync is not installed, please require punkstar/mageconfigsync');
        }
    }

    /**
     * isMageConfigSyncInstalled.
     *
     * @return bool
     */
    public function isMageConfigSyncInstalled()
    {
        $sourceFolder = $this->config->get('source_folder');
        return $this->fileHelper->fileExists($sourceFolder.'/vendor/bin/mageconfigsync');
    }

}

