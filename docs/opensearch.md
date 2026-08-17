# Open Search

To use OpenSearch instead of Elasticsearch put this into your `magedev.json`:

    ...
    "search_engine": "opensearch",
    "opensearch_version": "2.19.4",
    ...

Configuration in magento:

    php bin/magento config:set catalog/search/engine opensearch
    php bin/magento config:set catalog/search/opensearch_server_hostname elasticsearch
    php bin/magento config:set catalog/search/opensearch_username admin
    php bin/magento config:set catalog/search/opensearch_password qas1TLEy010%j

The container keeps the name `elasticsearch` no matter which engine is used, so
the hostname magento connects to stays the same.

## Choosing the version

`opensearch_version` is used as the tag of the official
[opensearch image](https://hub.docker.com/r/opensearchproject/opensearch), so any
released version can be selected - including the floating major version tag:

    ...
    "search_engine": "opensearch",
    "opensearch_version": 3,
    ...

When `opensearch_version` is omitted, `1.3.20` is used.

Pick the version your magento release supports: Adobe lists OpenSearch `2` and
`3` for 2.4.7-p10 and OpenSearch `3` for 2.4.8-p4 and p5, see
[Adobes system requirements](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/system-requirements).
Third party search implementations like Elasticsuite have their own compatibility
matrix on top of that.

## Analysis plugins

Unlike the elasticsearch image, the opensearch image ships without the analysis
plugins, so magedev installs `analysis-phonetic` and `analysis-icu` when building
it. Elasticsuite declares phonetic filters and icu analyzers and fails to index
without them; the stock magento search does not need them but is unaffected.

## Switching an existing project

`magedev docker:start` only builds containers that do not exist yet, so a project
that already runs keeps its old search container after changing the engine or the
version. Recreate it:

    magedev docker:stop
    docker rm magedev-<project>-elasticsearch
    magedev docker:start

The old image is kept, so switching back is just another config change plus the
same recreate.

Indices are not carried over: a fresh container starts empty, and OpenSearch 3
cannot even read indices written by 1.x (its minimum index compatibility is
2.0). Reindex afterwards:

    magedev magento:reindex
