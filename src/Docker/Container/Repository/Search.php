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
 * Class: Search.
 *
 * @see AbstractContainer
 */
class Search extends AbstractContainer
{
    /**
     * getName.
     */
    public function getName()
    {
        return 'elasticsearch';
    }

    /**
     * getImage.
     */
    public function getImage()
    {
        if ($this->isOpenSearch()) {
            return $this->imageFactory->create('OpenSearch');
        }

        return $this->imageFactory->create('Elasticsearch');
    }

    public function getConfig()
    {
        $config = parent::getConfig();
        $env = $config->getEnv();

        if ($this->isOpenSearch()) {
            $env = array_merge($env, [
                'OPENSEARCH_INITIAL_ADMIN_PASSWORD=qas1TLEy010%j',
                'plugins.security.disabled=true',
                'discovery.type=single-node'
            ]);
        } else {
            $env = array_merge($env, [
                'xpack.security.enabled=false',
            ]);
        }
        $config->setEnv($env);
        return $config;
    }

    private function isOpenSearch()
    {
        $searchEngine = $this->config->get('search_engine');
        if ($searchEngine == "opensearch") {
            return true;
        }

        return false;
    }
}
