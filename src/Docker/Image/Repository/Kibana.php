<?php
namespace TeamNeusta\Magedev\Docker\Image\Repository;
use TeamNeusta\Magedev\Docker\Image\AbstractImage;

/**
 * Class Kibana
 * @package TeamNeusta\Magedev\Docker\Image\Repository
 */
class Kibana extends AbstractImage
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

    public function configure()
    {
        if ($this->config->optionExists('es_version')) {
            $esVersion = $this->config->get('es_version');
        } else {
            // default to current version
            $esVersion = "5.4.3";
        }

        $this->initKibanaByESVersion($esVersion);
    }

    /**
     * initESDefault
     *
     */
    public function initKibanaByESVersion($esVersion)
    {
        $this->name('kibana');
        $this->from('docker.elastic.co/kibana/kibana:'.$esVersion);

        $this->env('ELASTICSEARCH_HOSTS','http://elasticsearch:9200');
        $this->expose('5601');
    }
}
