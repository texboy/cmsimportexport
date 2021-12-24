<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Filter\Types;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Texboy\CmsImportExport\Model\Export\Filter\FilterProcessorInterface;

class VarcharFilter implements FilterProcessorInterface
{
    /**
     * @param AbstractCollection $collection
     * @param string $columnName
     * @param array|string $value
     * @return void
     */
    public function process(AbstractCollection $collection, string $columnName, $value): void
    {
        $collection->addFieldToFilter($columnName, ['like' => '%' . $value . '%']);
    }
}
