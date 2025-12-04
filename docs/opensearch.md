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
