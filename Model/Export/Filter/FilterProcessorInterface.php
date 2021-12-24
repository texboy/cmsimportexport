<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Filter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @api
 */
interface FilterProcessorInterface
{
    /**
     * Filter Processor Interface is used as an Extension Point for each Attribute Data Type (Backend Type)
     * to process filtering applied from Export Grid UI
     * to all attributes of Entity being exported
     *
     * @param AbstractCollection $collection
     * @param string $columnName
     * @param array|string $value
     * @return void
     */
    public function process(AbstractCollection $collection, string $columnName, $value): void;
}
