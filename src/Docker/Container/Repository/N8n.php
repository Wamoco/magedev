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

namespace TeamNeusta\Magedev\Docker\Container\Repository;

use TeamNeusta\Magedev\Docker\Container\AbstractContainer;

/**
 * Class: N8n.
 *
 * @see AbstractContainer
 */
class N8n extends AbstractContainer
{
    /**
     * getName.
     */
    public function getName()
    {
        return 'n8n';
    }

    /**
     * getImage.
     */
    public function getImage()
    {
        return $this->imageFactory->create('N8n');
    }

    /**
     * getConfig.
     */
    public function getConfig()
    {
        // n8n keeps all of its moving data (workflows, credentials, encryption
        // key, sqlite db) in /home/node/.n8n. We bind this into the project
        // folder just like mysql does with its data directory. The directory is
        // created beforehand so it is owned by the host user instead of root
        // (docker would otherwise create the bind mount as root and n8n, which
        // runs as the "node" user, could not write to it).
        $dataDir = $this->config->get('project_path') . '/.n8n-data';
        $this->config->getFileHelper()->createDirectory($dataDir);

        $this->setBinds([
            $dataDir . ':/home/node/.n8n:rw',
        ]);

        $config = parent::getConfig();

        $env = $config->getEnv();
        $env = array_merge($env, [
            // allow login over plain http on a custom dev host
            'N8N_SECURE_COOKIE=false',
            // use task runners (recommended, silences the deprecation warning)
            'N8N_RUNNERS_ENABLED=true',
        ]);
        $config->setEnv($env);

        return $config;
    }
}
