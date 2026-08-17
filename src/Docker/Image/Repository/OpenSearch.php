<?php
namespace TeamNeusta\Magedev\Docker\Image\Repository;
use TeamNeusta\Magedev\Docker\Image\AbstractImage;

/**
 * Class OpenSearch
 * @package TeamNeusta\Magedev\Docker\Image\Repository
 */
class OpenSearch extends AbstractImage
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
        if ($this->config->optionExists('opensearch_version')) {
            $version = $this->config->get('opensearch_version');
        } else {
            // default to current version
            $version = "1.3.20";
        }

        $this->name('opensearch');
        $this->from('opensearchproject/opensearch:'.$version);

        // The opensearch image ships without the analysis plugins, unlike the
        // elasticsearch one. Elasticsuite declares phonetic filters and icu
        // analyzers and fails to index without them. "--batch" accepts the
        // additional permissions the plugins ask for.
        $this->run('bin/opensearch-plugin install --batch analysis-phonetic');
        $this->run('bin/opensearch-plugin install --batch analysis-icu');

        $this->expose('9200');
        $this->expose('9300');
    }
}
