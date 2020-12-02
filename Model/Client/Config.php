<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Swissup\SearchMysqlLegacy\Model\Client;

use Magento\AdvancedSearch\Model\Client\ClientOptionsInterface;

class Config implements ClientOptionsInterface
{
    public function prepareClientOptions($options = [])
    {
        $defaultOptions = [];
        $options = array_merge($defaultOptions, $options);

        $allowedOptions = array_merge(array_keys($defaultOptions), ['engine']);

        return array_filter(
            $options,
            function (string $key) use ($allowedOptions) {
                return in_array($key, $allowedOptions);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
