<?php

namespace Texboy\CmsImportExport\Model\Export\Page;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\ResourceModel\Page as ResourcePage;
use Magento\Framework\Serialize\SerializerInterface;

class PageStoreModifier
{
    const STORE_ID_COLUMN = 'store_ids';
    /**
     * @var ResourcePage
     */
    private $resourcePage;

    /**
     * @var SerializerInterface.
     */
    private $serializer;

    /**
     * @param ResourcePage $resourcePage
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ResourcePage $resourcePage,
        SerializerInterface $serializer
    ) {
        $this->resourcePage = $resourcePage;
        $this->serializer = $serializer;
    }

    /**
     * @param array $pageData
     * @return array
     */
    public function addStoreIdToPages(array $pageData): array
    {
        $data = [];
        foreach ($pageData as $page) {
            $data[] = $this->processPage($page);
        }
        return $data;
    }

    /**
     * @param PageInterface $page
     * @return array
     */
    private function processPage(PageInterface $page): array
    {
        $storeIds = $this->resourcePage->lookupStoreIds($page->getId());

        $data = $page->getData();
        $data[self::STORE_ID_COLUMN] = $this->serializer->serialize($storeIds);
        return $data;
    }
}
