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
 * Class Mysql.
 */
class Migration extends AbstractImage
{
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
        $this->initMysqlDefault();
    }

    protected function initMysqlDefault()
    {
        $this->name('migration');
        $this->from('mysql:5.6');

        $uid = getmyuid();
        $this->run('usermod -u '.$uid.' mysql');

        // addresses permission error, cannot bind socket
        $this->run("chmod -R 777 /var/run/mysqld/");

        $this->addFile("var/Docker/mysql/mysql.cnf", "/etc/mysql/conf.d/z99-docker.cnf");
        $this->run("chmod 644 /etc/mysql/conf.d/z99-docker.cnf");

        $this->addFile("var/Docker/mysql/my.cnf","/root/.my.cnf");
        $this->addFile("var/Docker/mysql/my.cnf","/var/www/.my.cnf");
    }

}
