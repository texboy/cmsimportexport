<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Page;

use Magento\Framework\Exception\LocalizedException;
use Magento\Cms\Model\ResourceModel\Page\Collection as EntityCollection;
use Magento\Framework\Data\Collection as AttributeCollection;

/**
 * @api
 */
interface PageCollectionFactoryInterface
{
    /**
     * PageCollection is used to gather all the data (with filters applied) which need to be exported
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return EntityCollection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): EntityCollection;
}
