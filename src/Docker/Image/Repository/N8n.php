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
 * Class N8n
 * @package TeamNeusta\Magedev\Docker\Image\Repository
 */
class N8n extends AbstractImage
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
        if ($this->config->optionExists('n8n_version')) {
            $version = $this->config->get('n8n_version');
        } else {
            // default to the current n8n release
            $version = 'latest';
        }

        $this->name('n8n');
        $this->from('n8nio/n8n:' . $version);

        $this->expose('5678');
    }
}
