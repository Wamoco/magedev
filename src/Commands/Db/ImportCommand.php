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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: ImportCommand.
 *
 * @see AbstractCommand
 */
class ImportCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config         $config
     * @param \TeamNeusta\Magedev\Services\ShellService  $shellService
     * @param \TeamNeusta\Magedev\Services\DockerService $dockerService
     */
    public function __construct(
        Config $config,
        ShellService $shellService,
        DockerService $dockerService
    ) {
        parent::__construct();
        $this->config = $config;
        $this->shellService = $shellService;
        $this->dockerService = $dockerService;
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('db:import');
        $this->setDescription('import db');
        $this->addArgument('dump_file', InputArgument::OPTIONAL, 'path to sql dump');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->optionExists('dump_file')) {
            $dumpFile = $this->config->get('dump_file');

            if ($this->isHttp($dumpFile)) {
                $url      = $dumpFile;
                $dumpFile = basename($url);
                $this->shellService->execute('curl ' . $url . ' -o ' . $dumpFile);
            }

            $this->importFile($dumpFile);
        }

        parent::execute($input, $output);
    }

    protected function isHttp($path)
    {
        return substr($path, 0, 4) === "http";
    }

    protected function importFile($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("File not found: " . $file);
        }
        $this->shellService->execute('mysql -h 0.0.0.0 -u root -proot magento < ' . $file);
    }
}
