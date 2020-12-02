<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Swissup\SearchMysqlLegacy\Model\Client;

use Magento\AdvancedSearch\Model\Client\ClientInterface;

/**
 * LMysql client
 */
class LMysql implements ClientInterface
{
    /**
     * @var bool
     */
    private $pingResult;


    /**
     * Ping
     *
     * @return bool
     */
    public function ping() : bool
    {
        if ($this->pingResult === null) {
            $this->pingResult = true;
        }

        return $this->pingResult;
    }

    /**
     * Validate connection
     *
     * @return bool
     */
    public function testConnection() : bool
    {
        return $this->ping();
    }
}
