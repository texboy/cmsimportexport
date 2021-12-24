<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Page;

use Magento\Cms\Api\Data\PageInterface;
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
            /** @var \Magento\Eav\Model\Entity\Attribute $pageId */
            $pageId = $this->attributeFactory->create();
            $pageId->setId(PageInterface::PAGE_ID);
            $pageId->setDefaultFrontendLabel(PageInterface::PAGE_ID);
            $pageId->setAttributeCode(PageInterface::PAGE_ID);
            $pageId->setBackendType('int');
            $this->collection->addItem($pageId);

            /** @var \Magento\Eav\Model\Entity\Attribute $identifierAttribute */
            $identifierAttribute = $this->attributeFactory->create();
            $identifierAttribute->setId(PageInterface::IDENTIFIER);
            $identifierAttribute->setBackendType('varchar');
            $identifierAttribute->setDefaultFrontendLabel(PageInterface::IDENTIFIER);
            $identifierAttribute->setAttributeCode(PageInterface::IDENTIFIER);
            $this->collection->addItem($identifierAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $titleAttribute */
            $titleAttribute = $this->attributeFactory->create();
            $titleAttribute->setId(PageInterface::TITLE);
            $titleAttribute->setBackendType('varchar');
            $titleAttribute->setDefaultFrontendLabel(PageInterface::TITLE);
            $titleAttribute->setAttributeCode(PageInterface::TITLE);
            $this->collection->addItem($titleAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $contentHeadingAttribute */
            $contentHeadingAttribute = $this->attributeFactory->create();
            $contentHeadingAttribute->setId(PageInterface::CONTENT_HEADING);
            $contentHeadingAttribute->setBackendType('varchar');
            $contentHeadingAttribute->setDefaultFrontendLabel(PageInterface::CONTENT_HEADING);
            $contentHeadingAttribute->setAttributeCode(PageInterface::CONTENT_HEADING);
            $this->collection->addItem($contentHeadingAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $pageLayoutAttribute */
            $pageLayoutAttribute = $this->attributeFactory->create();
            $pageLayoutAttribute->setId(PageInterface::PAGE_LAYOUT);
            $pageLayoutAttribute->setBackendType('varchar');
            $pageLayoutAttribute->setDefaultFrontendLabel(PageInterface::PAGE_LAYOUT);
            $pageLayoutAttribute->setAttributeCode(PageInterface::PAGE_LAYOUT);
            $this->collection->addItem($pageLayoutAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $metaKeywordsAttribute */
            $metaKeywordsAttribute = $this->attributeFactory->create();
            $metaKeywordsAttribute->setId(PageInterface::META_KEYWORDS);
            $metaKeywordsAttribute->setBackendType('varchar');
            $metaKeywordsAttribute->setDefaultFrontendLabel(PageInterface::META_KEYWORDS);
            $metaKeywordsAttribute->setAttributeCode(PageInterface::META_KEYWORDS);
            $this->collection->addItem($metaKeywordsAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $metaDescriptionAttribute */
            $metaDescriptionAttribute = $this->attributeFactory->create();
            $metaDescriptionAttribute->setId(PageInterface::META_DESCRIPTION);
            $metaDescriptionAttribute->setBackendType('varchar');
            $metaDescriptionAttribute->setDefaultFrontendLabel(PageInterface::META_DESCRIPTION);
            $metaDescriptionAttribute->setAttributeCode(PageInterface::META_DESCRIPTION);
            $this->collection->addItem($metaDescriptionAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $contentAttribute */
            $contentAttribute = $this->attributeFactory->create();
            $contentAttribute->setId(PageInterface::CONTENT);
            $contentAttribute->setBackendType('varchar');
            $contentAttribute->setDefaultFrontendLabel(PageInterface::CONTENT);
            $contentAttribute->setAttributeCode(PageInterface::CONTENT);
            $this->collection->addItem($contentAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $layoutUpdateXmlAttribute */
            $layoutUpdateXmlAttribute = $this->attributeFactory->create();
            $layoutUpdateXmlAttribute->setId(PageInterface::LAYOUT_UPDATE_XML);
            $layoutUpdateXmlAttribute->setBackendType('varchar');
            $layoutUpdateXmlAttribute->setDefaultFrontendLabel(PageInterface::LAYOUT_UPDATE_XML);
            $layoutUpdateXmlAttribute->setAttributeCode(PageInterface::LAYOUT_UPDATE_XML);
            $this->collection->addItem($layoutUpdateXmlAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $isActive */
            $isActive = $this->attributeFactory->create();
            $isActive->setId(PageInterface::IS_ACTIVE);
            $isActive->setBackendType('int');
            $isActive->setFrontendInput('select');
            $isActive->setSourceModel(Status::class);
            $isActive->setDefaultFrontendLabel(PageInterface::IS_ACTIVE);
            $isActive->setAttributeCode(PageInterface::IS_ACTIVE);
            $this->collection->addItem($isActive);

            /** @var \Magento\Eav\Model\Entity\Attribute $sortOrderAttribute */
            $sortOrderAttribute = $this->attributeFactory->create();
            $sortOrderAttribute->setId(PageInterface::SORT_ORDER);
            $sortOrderAttribute->setBackendType('int');
            $sortOrderAttribute->setDefaultFrontendLabel(PageInterface::SORT_ORDER);
            $sortOrderAttribute->setAttributeCode(PageInterface::SORT_ORDER);
            $this->collection->addItem($sortOrderAttribute);
        }

        return $this->collection;
    }
}
