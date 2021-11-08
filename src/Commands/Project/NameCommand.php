<?php
/**
 * Greetings from Wamoco GmbH, Bremen, Germany.
 * @author Wamoco Team<info@wamoco.de>
 * @license See LICENSE.txt for license details.
 */

namespace TeamNeusta\Magedev\Commands\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: NameCommand.
 *
 * @see NameCommand
 */
class NameCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config
    ) {
        $this->config = $config;
        parent::__construct();
    }
    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('project:name');
        $this->setDescription('get project name');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $this->config->get('project_name');
        $output->write(strtolower($projectName));
    }
}
