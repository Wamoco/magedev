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
 * Class Varnish.
 */
class Varnish extends AbstractImage
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
        $this->name('varnish');

        $buildStrategy = 'build';
        $dockerConfig = $this->config->get('docker');
        if (array_key_exists('build_strategy', $dockerConfig)) {
            $buildStrategy = $dockerConfig['build_strategy'];
        }

        if ($buildStrategy == 'pull') {
            $this->from('bleers/varnish:1.1');
        }
        if ($buildStrategy == 'build') {
            $this->from($this->imageFactory->create("Varnish4"));
        }

        $this->addFile('var/Docker/varnish/conf/supervisord.conf', '/etc/supervisor/conf.d/supervisord.conf');
        $this->addFile('var/Docker/varnish/etc/default/varnish', '/etc/default/varnish');
        $this->addFile('var/Docker/varnish/etc/varnish/default.vcl', '/etc/varnish/default.vcl');
        $this->addFile('var/Docker/varnish/start.sh', '/start.sh');

    }
}
