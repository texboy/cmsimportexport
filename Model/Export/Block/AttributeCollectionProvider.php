<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Block;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;
use Texboy\CmsImportExport\Model\Export\Filter\Status\Status;

/**
 * @api
 */
class AttributeCollectionProvider
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @throws \InvalidArgumentException
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributeFactory $attributeFactory
    ) {
        $this->collection = $collectionFactory->create(Collection::class);
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function get(): Collection
    {
        if (count($this->collection) === 0) {
            /** @var \Magento\Eav\Model\Entity\Attribute $blockId */
            $blockId = $this->attributeFactory->create();
            $blockId->setId(BlockInterface::BLOCK_ID);
            $blockId->setDefaultFrontendLabel(BlockInterface::BLOCK_ID);
            $blockId->setAttributeCode(BlockInterface::BLOCK_ID);
            $blockId->setBackendType('int');
            $this->collection->addItem($blockId);

            /** @var \Magento\Eav\Model\Entity\Attribute $identifierAttribute */
            $identifierAttribute = $this->attributeFactory->create();
            $identifierAttribute->setId(BlockInterface::IDENTIFIER);
            $identifierAttribute->setBackendType('varchar');
            $identifierAttribute->setDefaultFrontendLabel(BlockInterface::IDENTIFIER);
            $identifierAttribute->setAttributeCode(BlockInterface::IDENTIFIER);
            $this->collection->addItem($identifierAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $titleAttribute */
            $titleAttribute = $this->attributeFactory->create();
            $titleAttribute->setId(BlockInterface::TITLE);
            $titleAttribute->setBackendType('varchar');
            $titleAttribute->setDefaultFrontendLabel(BlockInterface::TITLE);
            $titleAttribute->setAttributeCode(BlockInterface::TITLE);
            $this->collection->addItem($titleAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $contentAttribute */
            $titleAttribute = $this->attributeFactory->create();
            $titleAttribute->setId(BlockInterface::CONTENT);
            $titleAttribute->setBackendType('varchar');
            $titleAttribute->setDefaultFrontendLabel(BlockInterface::CONTENT);
            $titleAttribute->setAttributeCode(BlockInterface::CONTENT);
            $this->collection->addItem($titleAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $isActive */
            $isActive = $this->attributeFactory->create();
            $isActive->setId(BlockInterface::IS_ACTIVE);
            $isActive->setBackendType('int');
            $isActive->setFrontendInput('select');
            $isActive->setSourceModel(Status::class);
            $isActive->setDefaultFrontendLabel(BlockInterface::IS_ACTIVE);
            $isActive->setAttributeCode(BlockInterface::IS_ACTIVE);
            $this->collection->addItem($isActive);
        }

        return $this->collection;
    }
}
