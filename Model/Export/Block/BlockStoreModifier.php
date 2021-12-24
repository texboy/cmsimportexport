<?php

namespace Texboy\CmsImportExport\Model\Export\Block;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\ResourceModel\Block as ResourceBlock;
use Magento\Framework\Serialize\SerializerInterface;

class BlockStoreModifier
{
    const STORE_ID_COLUMN = 'store_ids';
    /**
     * @var ResourceBlock
     */
    private $resourceBlock;

    /**
     * @var SerializerInterface.
     */
    private $serializer;

    /**
     * @param ResourceBlock $resourceBlock
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ResourceBlock $resourceBlock,
        SerializerInterface $serializer
    ) {
        $this->resourceBlock = $resourceBlock;
        $this->serializer = $serializer;
    }

    /**
     * @param array $blockData
     * @return array
     */
    public function addStoreIdToBlocks(array $blockData): array
    {
        $data = [];
        foreach ($blockData as $block) {
            $data[] = $this->processBlock($block);
        }
        return $data;
    }

    /**
     * @param BlockInterface $block
     * @return array
     */
    private function processBlock(BlockInterface $block): array
    {
        $storeIds = $this->resourceBlock->lookupStoreIds($block->getId());

        $data = $block->getData();
        $data[self::STORE_ID_COLUMN] = $this->serializer->serialize($storeIds);
        return $data;
    }
}
