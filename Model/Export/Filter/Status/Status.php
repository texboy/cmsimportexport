<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Filter\Status;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\InventoryApi\Api\Data\SourceItemInterface;

/**
 * @inheritdoc
 */
class Status extends AbstractSource
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        return [
            [
                'value' => 1,
                'label' => __('Enabled'),
            ],
            [
                'value' => 0,
                'label' => __('Disabled'),
            ],
        ];
    }
}
