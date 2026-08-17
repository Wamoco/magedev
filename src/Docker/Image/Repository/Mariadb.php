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

namespace TeamNeusta\Magedev\Docker\Image\Repository;

use TeamNeusta\Magedev\Docker\Image\AbstractImage;

/**
 * Class Mariadb.
 *
 * Drop in replacement for the Mysql image, used when "db_engine": "mariadb"
 * is set in magedev.json. The official mariadb image is compatible with the
 * mysql one in all aspects magedev relies on: it ships a "mysql" system user,
 * reads /etc/mysql/conf.d/*.cnf and understands the MYSQL_* environment
 * variables.
 */
class Mariadb extends AbstractImage
{
    /**
     * default version, listed for magento 2.4.7-p10 in adobes system
     * requirements.
     *
     * @var string
     */
    const DEFAULT_VERSION = '10.11';

    /**
     * getBuildName.
     *
     * @return string
     */
    public function getBuildName()
    {
        return $this->nameBuilder->buildName(
             $this->getName()
        );
    }

    /**
     * configure.
     */
    public function configure()
    {
        if ($this->config->optionExists('mariadb_version')) {
            $version = $this->config->get('mariadb_version');
        } else {
            $version = self::DEFAULT_VERSION;
        }

        $this->name('mariadb');
        $this->from('mariadb:'.$version);

        $uid = getmyuid();
        $this->run('usermod -u '.$uid.' mysql');

        // addresses permission error, cannot bind socket
        $this->run('mkdir -p /var/run/mysqld/ && chmod -R 777 /var/run/mysqld/');

        $this->addFile($this->findServerConfig($version), '/etc/mysql/conf.d/z99-docker.cnf');
        $this->run('chmod 644 /etc/mysql/conf.d/z99-docker.cnf');

        $this->addFile('var/Docker/mysql/my.cnf', '/root/.my.cnf');
        $this->addFile('var/Docker/mysql/my.cnf', '/var/www/.my.cnf');
    }

    /**
     * findServerConfig.
     *
     * The server settings are the same for all mariadb versions, so there is
     * one shared config file. A version specific file wins if it exists, which
     * allows tuning a single version - either in magedev itself or in a project
     * via .magedev/var/Docker/mysql/mariadb-10.11.cnf
     *
     * @param string $version
     *
     * @return string
     */
    protected function findServerConfig($version)
    {
        $versionConfig = 'var/Docker/mysql/mariadb-'.$version.'.cnf';
        if ($this->fileHelper->fileExists($versionConfig)) {
            return $versionConfig;
        }

        return 'var/Docker/mysql/mariadb.cnf';
    }
}
