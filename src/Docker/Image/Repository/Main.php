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
 * Class Main.
 */
class Main extends AbstractImage
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
        $this->name('main');

        $magentoVersion = $this->config->getMagentoVersion();

        $buildStrategy = 'build';
        $dockerConfig = $this->config->get('docker');
        if (array_key_exists('build_strategy', $dockerConfig)) {
            $buildStrategy = $dockerConfig['build_strategy'];
        }

        $phpVersion = null;
        $imageVersion = '1.0';
        if ($this->config->optionExists('php_version')) {
            $phpVersion = $this->config->get('php_version');
        } else {
            // default to current version
            $phpVersion = "7.3";
        }
        if ($this->config->optionExists('image_version')) {
            $imageVersion = $this->config->get('image_version');
        }

        if ($buildStrategy == 'pull') {
            $this->from('bleers/magedev-php'.$phpVersion.':'.$imageVersion);
        }
        if ($buildStrategy == 'build') {
            $this->from($this->imageFactory->create('Php'.$phpVersion));
        }

        $uid = getmyuid();
        $this->run("usermod -u " . $uid . " www-data");

        $this->addFile("var/Docker/main/certs/apache-selfsigned.crt", "/etc/apache2/apache-selfsigned.crt");
        $this->addFile("var/Docker/main/certs/apache-selfsigned.key", "/etc/apache2/apache-selfsigned.key");

        // TODO: have something like a simple template engine to replace vars
        // like DOCUMENT_ROOT AND $GATEWAY ?

        $documentRoot = $this->config->get('document_root');
        $vhostConfig = $this->fileHelper->read('var/Docker/main/000-default.conf');
        $vhostConfig = str_replace('$DOCUMENT_ROOT', $documentRoot, $vhostConfig);
        $this->add('/etc/apache2/sites-enabled/000-default.conf', $vhostConfig);

        $vhostConfig = $this->fileHelper->read('var/Docker/main/000-ssl.conf');
        $vhostConfig = str_replace('$DOCUMENT_ROOT', $documentRoot, $vhostConfig);
        $this->add('/etc/apache2/sites-enabled/000-ssl.conf', $vhostConfig);

        // $GATEWAY
        $gatewayIp = $this->config->get('gateway');
        if (empty($gatewayIp)) {
            throw new \Exception('no gateway ip found');
        }

        $phpIniPath = "var/Docker/main/php-".$phpVersion."-".$imageVersion.".ini";
        if (!$this->fileHelper->fileExists($phpIniPath)) {
            $phpIniPath = "var/Docker/main/php.ini";
        }

        $phpIni = $this->fileHelper->read($phpIniPath);
        $phpIni = str_replace("\$GATEWAY", $gatewayIp, $phpIni);
        $this->add("/usr/local/etc/php/php.ini", $phpIni);

        $this->run("chmod 775 /usr/local/etc/php/php.ini"); // for www-data to read it

        $xdebugIniPath = "var/Docker/main/xdebug/php-".$phpVersion.".ini";
        if ($this->fileHelper->fileExists($xdebugIniPath)) {
            $xdebugIni = $this->fileHelper->read($xdebugIniPath);
            $xdebugIni = str_replace("\$GATEWAY", $gatewayIp, $xdebugIni);
            $this->add("/usr/local/etc/php/conf.d/xdebug.ini.dis", $xdebugIni);
            $this->run("chmod 775 /usr/local/etc/php/conf.d/xdebug.ini.dis"); // for www-data to read it
        }

        $this->addFile("var/Docker/mysql/my.cnf","/root/.my.cnf");
        $this->run("cp /root/.my.cnf /var/www/.my.cnf");
        $this->run("chown www-data:www-data /var/www/.my.cnf");

        $this->run("curl -O https://getcomposer.org/composer-stable.phar");
        $this->run("mv composer-stable.phar /usr/bin/composer");
        $this->run("chmod 777 /usr/bin/composer");
        $this->run("chmod +x /usr/bin/composer");

        $this->addFile("var/Docker/main/loadssh.sh", "/usr/bin/loadssh.sh");
        $this->run("chmod 777 /usr/bin/loadssh.sh");
        $this->run("chmod +x /usr/bin/loadssh.sh");

        $this->addFile("var/Docker/vendor/mini_sendmail-1.3.9/mini_sendmail", "/usr/bin/mini_sendmail");
        $this->run("chmod +x /usr/bin/mini_sendmail");

        $this->run('a2enmod rewrite');
        $this->run('a2enmod ssl');

        // expose grunt port
        $this->expose("35729");

        // expose ssl
        $this->expose("443");
    }
}
