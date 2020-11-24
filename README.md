# Legacy Mysql Search 

<sup>It's a magento2-module for the [metapackage](https://github.com/swissup/search-mysql-legacy/).</sup>

### [Installation](https://docs.swissuplabs.com/m2/extensions/search-mysql-legacy/installation/)

###### For clients

There are several ways to install extension for clients:

 1. If you've bought the product at Magento's Marketplace - use
    [Marketplace installation instructions](https://docs.magento.com/marketplace/user_guide/buyers/install-extension.html)

 2. Otherwise, you have two options:
    - Install the sources directly from [our repository](https://docs.swissuplabs.com/m2/extensions/search-mysql-legacy/installation/composer/) - **recommended**
    - Download archive and use [manual installation](https://docs.swissuplabs.com/m2/extensions/search-mysql-legacy/installation/manual/)


###### For maintainers

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-search-mysql-legacy --prefer-source --ignore-platform-reqs
bin/magento module:enable Swissup_SearchMysqlLegacy Swissup_Core
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento indexer:reindex catalogsearch_fulltext
```

### F.A.Q

#### How to install Magento 2.4.0 without Elasticsearch require during installation?

Use --disable-modules option

```
bin/magento setup:install -h
...
--disable-modules[=DISABLE-MODULES] List of comma-separated module names. That must be avoided during installation.
```

```
php bin/magento setup:install --disable-modules=Magento_InventoryElasticsearch,Magento_Elasticsearch7,Magento_Elasticsearch6,Magento_Elasticsearch
```

Or disable all elastic search module with below command after running the setup:install.
```bash
php bin/magento module:disable Magento_Elasticsearch Magento_Elasticsearch6 Magento_Elasticsearch7 Magento_InventoryElasticsearch
```
#### How can I check current search engine?

You can check your current search engine using:
```
bin/magento config:show catalog/search/engine
```

#### How can I change search engine?

You can change your current search engine using:
```
bin/magento config:set catalog/search/engine 'lmysql'
```
