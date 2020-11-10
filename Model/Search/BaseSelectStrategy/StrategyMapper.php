<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Swissup\SearchMysqlLegacy\Model\Search\BaseSelectStrategy;

use Swissup\SearchMysqlLegacy\Model\Search\SelectContainer\SelectContainer;
use Swissup\SearchMysqlLegacy\Model\Adapter\Mysql\BaseSelectStrategy\BaseSelectFullTextSearchStrategy;
use Swissup\SearchMysqlLegacy\Model\Adapter\Mysql\BaseSelectStrategy\BaseSelectAttributesSearchStrategy;

/**
 * This class is responsible for deciding which BaseSelectStrategyInterface should be used for passed SelectContainer
 *
 * @deprecated 101.0.0
 * @see \Magento\ElasticSearch
 */
class StrategyMapper
{
    /**
     * @var BaseSelectFullTextSearchStrategy
     */
    private $baseSelectFullTextSearchStrategy;

    /**
     * @var BaseSelectAttributesSearchStrategy
     */
    private $baseSelectAttributesSearchStrategy;

    /**
     * @param BaseSelectFullTextSearchStrategy $baseSelectFullTextSearchStrategy
     * @param BaseSelectAttributesSearchStrategy $baseSelectAttributesSearchStrategy
     */
    public function __construct(
        BaseSelectFullTextSearchStrategy $baseSelectFullTextSearchStrategy,
        BaseSelectAttributesSearchStrategy $baseSelectAttributesSearchStrategy
    ) {
        $this->baseSelectFullTextSearchStrategy = $baseSelectFullTextSearchStrategy;
        $this->baseSelectAttributesSearchStrategy = $baseSelectAttributesSearchStrategy;
    }

    /**
     * Decides which BaseSelectStrategyInterface should be used
     *
     * @param SelectContainer $selectContainer
     * @return BaseSelectStrategyInterface
     */
    public function mapSelectContainerToStrategy(SelectContainer $selectContainer)
    {
        if ($selectContainer->isFullTextSearchRequired()
            && !$selectContainer->hasCustomAttributesFilters()
            && !$selectContainer->hasVisibilityFilter()
        ) {
            return $this->baseSelectFullTextSearchStrategy;
        }

        return $this->baseSelectAttributesSearchStrategy;
    }
}
