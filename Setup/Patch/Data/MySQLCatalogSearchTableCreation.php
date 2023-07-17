<?php
declare(strict_types=1);

namespace Swissup\SearchMysqlLegacy\Setup\Patch\Data;

use Magento\CatalogSearch\Model\Indexer\IndexStructure;
use Magento\Framework\Indexer\DimensionProviderInterface;
use Magento\Framework\Search\EngineResolverInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;

/**
 * This patch will create catalogsearch_fulltext tables in MySQL DB.
 */
class MySQLCatalogSearchTableCreation implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var IndexStructure
     */
    private $indexStructure;

    /**
     * @var DimensionProviderInterface
     */
    private $dimensionProvider;

    /**
     * @var EngineResolverInterface
     */
    private $searchEngineResolver;

    /**
     * MySQLCatalogSearchTableCreation constructor.
     * @param IndexStructure $indexStructure
     * @param DimensionProviderInterface $dimensionProvider
     * @param EngineResolverInterface $searchEngineResolver
     */
    public function __construct(
        IndexStructure $indexStructure,
        DimensionProviderInterface $dimensionProvider,
        EngineResolverInterface $searchEngineResolver
    ) {
        $this->indexStructure = $indexStructure;
        $this->dimensionProvider = $dimensionProvider;
        $this->searchEngineResolver = $searchEngineResolver;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        if ($this->searchEngineResolver->getCurrentSearchEngine() === 'lmysql') {
            foreach ($this->dimensionProvider->getIterator() as $dimension) {
                $this->indexStructure->create(Fulltext::INDEXER_ID, [], $dimension);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}