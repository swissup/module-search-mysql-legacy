<?php

namespace Swissup\SearchMysqlLegacy\Plugin\CatalogSearch\Model\Adapter\Aggregation\Checker\Query;

class CatalogView
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     *
     * @return boolean
     */
    protected function isAttributePage()
    {
        return $this->request->getFullActionName() === 'attributepages_page_view';
    }

    /**
     *
     * @param  \Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView $subject
     * @param  boolean $result
     * @return boolean
     */
    public function afterIsApplicable(
        \Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView $subject,
        $result
    ) {

        if ($this->isAttributePage()) {
            $result = true;
        }

        return $result;
    }
}
