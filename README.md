# Legacy Mysql Search 
<sup>It's a magento2-module for the [metapackage](https://github.com/swissup/search-mysql-legacy/).</sup>

The news sometimes isn't good, but there is always a way out. Yes, [MySQL is no longer supported](https://devdocs.magento.com/guides/v2.4/release-notes/backward-incompatible-changes/#230---24) for search since Magento 2.4. And you must install Elasticsearch 7.6.x before upgrading to the latest Magento version.

Elastic search is a great solution. Even so, a lot of our customers still prefer using hostings that don’t have it. Let us help you to install Magento 2.4 without Elastic search enabled.

### We released the Legacy MySQL Search module for Magento 2
We integrated Magento 2.3.5 MySQL Search mechanism with Magento 2.4. The Legacy MySQL Search module enables a new value in the Search Engine drop-down of Catalog Search config settings.

So you don’t need things to get complicated. You have simply to choose the "Legacy MySQL (Deprecated) value" in drop-down.

![the_new_legacy_mysql_search_module_for_magento_2-1](https://user-images.githubusercontent.com/412612/176845092-e72c72dd-6f96-43f9-8bb7-04238802eb3e.png)


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
bin/magento setup:upgrade --safe-mode=1
bin/magento setup:di:compile
bin/magento indexer:reindex catalogsearch_fulltext
```

### F.A.Q

#### How to install Magento 2.4.0 without Elasticsearch require during installation?

Use --disable-modules option

```
bin/magento setup:install -h
...
--disable-modules[=DISABLE-MODULES] List of comma-separated module names, that must be avoided during installation.
```

```
php bin/magento setup:install --disable-modules=Magento_InventoryElasticsearch,Magento_Elasticsearch7,Magento_Elasticsearch6,Magento_Elasticsearch
```

After installing enable all elastic search module with below command.
```bash
php bin/magento module:enable Magento_Elasticsearch Magento_Elasticsearch6 Magento_Elasticsearch7 Magento_InventoryElasticsearch
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

#### `setup:upgrade` fails with "Current version of RDBMS is not supported"

This is a Magento core check, not an extension issue. `Magento\Framework\Setup\Declaration\Schema\Db\MySQL\SqlVersionProvider`
only accepts the DB versions listed in `app/etc/di.xml`. In Magento 2.4.8 those are:

| Engine | Accepted versions |
|--------|-------------------|
| MySQL | `5.7.x`, `8.0.x`, `8.4.x` |
| MariaDB | `10.2.x`–`10.6.x`, `11.4.x` |

**Everything else is rejected — including MariaDB 10.7–10.11, 11.0–11.3 and 11.5+.** This is a real gap: core still
tolerates the legacy 10.2–10.6 range, but never 10.11 — so a hosting-side "minor" upgrade from
10.6 to 10.11 breaks `setup:upgrade` while the site itself keeps running. Adobe's supported
database for 2.4.8 is **MariaDB 11.4 LTS** or **MySQL 8.4 LTS**.

Move the database to a supported version (11.4 LTS), or roll it back to the version that worked.
Do not "fix" this by adding a pattern for your version to `di.xml`: `getMariaDbSuffixKey()` has no
branch for those releases, so it falls back to its 10.6 profile and declarative schema then emits
DDL against the wrong engine profile.

This extension itself has no DB-version logic and imposes no database requirement of its own beyond
Magento's — its search and layered-navigation SQL runs unchanged on 10.6, 10.11 and 11.4.

### Configuration

#### Multi-word Search Logic

**Location:** Stores > Configuration > Catalog > Catalog Search > Multi-word Search Logic (MySQL Legacy)

Control how multi-word searches are interpreted:

**OR Mode (Default)** - Original Magento 2.3 behavior
- Search: "red bag"
- Finds: Products containing "red" OR "bag"
- Result: More products, less precise matches

**AND Mode (Recommended)** - Elasticsearch-like behavior
- Search: "red bag"  
- Finds: Products containing "red" AND "bag"
- Result: Fewer products, more relevant matches

**Note:** Clear cache after changing this setting for immediate effect.

```bash
bin/magento cache:clean config
```

**Future:** Based on community feedback, AND mode may become the default in version 2.0.
