<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Swissup\SearchMysqlLegacy\Model\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\IndexBuilderInterface;

use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\App\ObjectManager;

use Swissup\SearchMysqlLegacy\Model\Search\FilterMapper\DimensionsProcessor;
use Swissup\SearchMysqlLegacy\Model\Search\SelectContainer\SelectContainer;
use Swissup\SearchMysqlLegacy\Model\Search\SelectContainer\SelectContainerBuilder;
use Swissup\SearchMysqlLegacy\Model\Search\BaseSelectStrategy\StrategyMapper as BaseSelectStrategyMapper;
use Swissup\SearchMysqlLegacy\Model\Search\BaseSelectStrategy\BaseSelectStrategyInterface;
use Swissup\SearchMysqlLegacy\Model\Search\FilterMapper\FilterMapper;

/**
 * Build base Query for Index
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @deprecated 101.0.0
 * @see \Magento\ElasticSearch
 */
class IndexBuilder implements IndexBuilderInterface
{
    /**
     * @var DimensionsProcessor
     */
    private $dimensionsProcessor;

    /**
     * @var SelectContainerBuilder
     */
    private $selectContainerBuilder;

    /**
     * @var BaseSelectStrategyMapper
     */
    private $baseSelectStrategyMapper;

    /**
     * @var FilterMapper
     */
    private $filterMapper;

    /**
     * @param ResourceConnection $resource
     * @param ScopeConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param ConditionManager $conditionManager
     * @param IndexScopeResolver $scopeResolver
     * @param TableMapper $tableMapper
     * @param ScopeResolverInterface $dimensionScopeResolver
     * @param DimensionsProcessor|null $dimensionsProcessor
     * @param SelectContainerBuilder|null $selectContainerBuilder
     * @param BaseSelectStrategyMapper|null $baseSelectStrategyMapper
     * @param FilterMapper|null $filterMapper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        ResourceConnection $resource,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        ConditionManager $conditionManager,
        IndexScopeResolver $scopeResolver,
        TableMapper $tableMapper,
        ScopeResolverInterface $dimensionScopeResolver,
        ?DimensionsProcessor $dimensionsProcessor = null,
        ?SelectContainerBuilder $selectContainerBuilder = null,
        ?BaseSelectStrategyMapper $baseSelectStrategyMapper = null,
        ?FilterMapper $filterMapper = null
    ) {
        $this->dimensionsProcessor = $dimensionsProcessor ?: ObjectManager::getInstance()
            ->get(DimensionsProcessor::class);

        $this->selectContainerBuilder = $selectContainerBuilder ?: ObjectManager::getInstance()
            ->get(SelectContainerBuilder::class);

        $this->baseSelectStrategyMapper = $baseSelectStrategyMapper ?: ObjectManager::getInstance()
            ->get(BaseSelectStrategyMapper::class);

        $this->filterMapper = $filterMapper ?: ObjectManager::getInstance()
            ->get(FilterMapper::class);
    }

    /**
     * Build index query
     *
     * @param RequestInterface $request
     * @return Select
     * @throws \DomainException
     * @throws \InvalidArgumentException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(RequestInterface $request)
    {
        /** @var SelectContainer $selectContainer */
        $selectContainer = $this->selectContainerBuilder->buildByRequest($request);

        /** @var BaseSelectStrategyInterface $baseSelectStrategy */
        $baseSelectStrategy = $this->baseSelectStrategyMapper->mapSelectContainerToStrategy($selectContainer);

        $selectContainer = $baseSelectStrategy->createBaseSelect($selectContainer);
        $selectContainer = $this->filterMapper->applyFilters($selectContainer);
        $selectContainer = $this->dimensionsProcessor->processDimensions($selectContainer);

        return $selectContainer->getSelect();
    }
}
