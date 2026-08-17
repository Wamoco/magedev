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
 * Class: Mysql.
 *
 * The database service of a project. Runs mysql by default, mariadb when
 * "db_engine": "mariadb" is configured. The container is named "mysql" in
 * both cases, so the hostname all other services (and magento itself) connect
 * to stays the same.
 *
 * @see AbstractContainer
 */
class Mysql extends AbstractContainer
{
    /**
     * getName.
     */
    public function getName()
    {
        return 'mysql';
    }

    /**
     * getImage.
     */
    public function getImage()
    {
        if ($this->isMariadb()) {
            return $this->imageFactory->create('Mariadb');
        }

        return $this->imageFactory->create('Mysql');
    }

    /**
     * getConfig.
     */
    public function getConfig()
    {
        $this->setBinds([
            $this->getDataDir().':/var/lib/mysql:rw',
        ]);

        $config = parent::getConfig();

        return $config;
    }

    /**
     * getDataDir.
     *
     * mariadb refuses to start on a data directory that was created by mysql
     * (and the other way around), so every engine keeps its data in its own
     * directory inside the project.
     *
     * @return string
     */
    protected function getDataDir()
    {
        $dataDir = $this->isMariadb() ? '/mariadb' : '/mysql';

        return $this->config->get('project_path').$dataDir;
    }

    /**
     * isMariadb.
     *
     * @return bool
     */
    private function isMariadb()
    {
        if (!$this->config->optionExists('db_engine')) {
            return false;
        }

        return $this->config->get('db_engine') == 'mariadb';
    }
}
