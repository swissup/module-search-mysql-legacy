<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\Aggregation\Builder;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Request\Dimension;

use Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\Aggregation\DataProviderInterface;

/**
 * MySQL search aggregation bucket builder.
 *
 * @deprecated 102.0.0
 * @see \Magento\ElasticSearch
 */
interface BucketInterface
{
    /**
     * Build bucket.
     *
     * @param DataProviderInterface $dataProvider
     * @param Dimension[] $dimensions
     * @param RequestBucketInterface $bucket
     * @param Table $entityIdsTable
     * @return array
     */
    public function build(
        DataProviderInterface $dataProvider,
        array $dimensions,
        RequestBucketInterface $bucket,
        Table $entityIdsTable
    );
}
