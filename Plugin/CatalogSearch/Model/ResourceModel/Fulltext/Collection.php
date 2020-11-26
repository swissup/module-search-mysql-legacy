<?php

namespace Swissup\SearchMysqlLegacy\Plugin\CatalogSearch\Model\ResourceModel\Fulltext;

class Collection
{
    private const SEARCH_ENGINE_VALUE_PATH = 'catalog/search/engine';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     *
     * @param  \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     * @param  boolean                                                        $printQuery
     * @param  boolean                                                        $logQuery
     * @return []
     */
    public function before_loadEntities(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject,
        $printQuery = false,
        $logQuery = false

    ) {
        $curPage = $subject->getCurPage();
        $pageSize = $subject->getPageSize();
        $currentSearchEngine = $this->scopeConfig->getValue(self::SEARCH_ENGINE_VALUE_PATH);
        if ($pageSize && ($currentSearchEngine === 'mysql'
            || $currentSearchEngine === 'lmysql')
        ) {
            $subject->getSelect()->limitPage($curPage, $pageSize);
        }

        return [$printQuery, $logQuery];
    }
}
