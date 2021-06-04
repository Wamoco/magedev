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
 * Class: Kibana.
 *
 * @see AbstractContainer
 */
class Kibana extends AbstractContainer
{
    /**
     * getName.
     */
    public function getName()
    {
        return 'kibana';
    }

    /**
     * getImage.
     */
    public function getImage()
    {
        return $this->imageFactory->create('Kibana');
    }

    public function getConfig()
    {
        $config = parent::getConfig();
        $env = $config->getEnv();
        $env = array_merge($env, [
            'xpack.security.enabled=false'
        ]);
        $config->setEnv($env);
        return $config;
    }
}
