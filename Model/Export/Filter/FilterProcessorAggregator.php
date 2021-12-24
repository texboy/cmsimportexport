<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Filter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Texboy\CmsImportExport\Model\Export\Filter\FilterProcessorInterface;
/**
 * @api
 */
class FilterProcessorAggregator
{
    /**
     * @var FilterProcessorInterface[]
     */
    private $handler;

    /**
     * @param FilterProcessorInterface[] $handler
     * @throws LocalizedException
     */
    public function __construct(array $handler = [])
    {
        foreach ($handler as $filterProcessor) {
            if (!($filterProcessor instanceof FilterProcessorInterface)) {
                throw new LocalizedException(__(
                    'Filter handler must be instance of "%interface"',
                    ['interface' => FilterProcessorInterface::class]
                ));
            }
        }

        $this->handler = $handler;
    }

    /**
     * @param string $type
     * @param AbstractCollection $collection
     * @param string $columnName
     * @param string|array $value
     * @throws LocalizedException
     */
    public function process($type, AbstractCollection $collection, $columnName, $value)
    {
        if (!isset($this->handler[$type])) {
            throw new LocalizedException(__(
                'No filter processor for "%type" given.',
                ['type' => $type]
            ));
        }
        $this->handler[$type]->process($collection, $columnName, $value);
    }
}
