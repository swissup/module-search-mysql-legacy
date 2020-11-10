<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\Query\Builder;

use Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\ScoreBuilder;

/**
 * MySQL search query builder.
 *
 * @deprecated 102.0.0
 * @see \Magento\ElasticSearch
 */
interface QueryInterface
{
    /**
     * Build query.
     *
     * @param \Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\ScoreBuilder $scoreBuilder
     * @param \Magento\Framework\DB\Select $select
     * @param \Magento\Framework\Search\Request\QueryInterface $query
     * @param string $conditionType
     * @return \Magento\Framework\DB\Select
     */
    public function build(
        ScoreBuilder $scoreBuilder,
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\Search\Request\QueryInterface $query,
        $conditionType
    );
}
