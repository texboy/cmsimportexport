<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Filter\Types;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Texboy\CmsImportExport\Model\Export\Filter\FilterProcessorInterface;

class IntFilter implements FilterProcessorInterface
{
    /**
     * @param AbstractCollection $collection
     * @param string $columnName
     * @param array|string $value
     * @return void
     */
    public function process(AbstractCollection $collection, string $columnName, $value): void
    {
        if (is_array($value)) {
            $from = $value[0] ?? null;
            $to = $value[1] ?? null;

            if (is_numeric($from) && !empty($from)) {
                $collection->addFieldToFilter($columnName, ['from' => $from]);
            }

            if (is_numeric($to) && !empty($to)) {
                $collection->addFieldToFilter($columnName, ['to' => $to]);
            }

            return;
        }

        $collection->addFieldToFilter($columnName, ['eq' => $value]);
    }
}
