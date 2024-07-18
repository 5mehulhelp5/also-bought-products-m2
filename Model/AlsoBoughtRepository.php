<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use MageMoto\AlsoBought\Api\AlsoBoughtRepositoryInterface;
use MageMoto\AlsoBought\Model\ResourceModel\Associate\CollectionFactory;

/**
 * Class AlsoBoughtRepository
 * @package MageMoto\AlsoBought\Model
 */
class AlsoBoughtRepository implements AlsoBoughtRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    protected $associateCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * AlsoBoughtRepository constructor.
     *
     * @param CollectionFactory $associateCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CollectionFactory $associateCollectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->associateCollectionFactory = $associateCollectionFactory;
        $this->productRepository          = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function getList($sku)
    {
        $productIds          = [$this->productRepository->get($sku)->getId()];
        $collection          = [];
        $associateProductIds = $this->associateCollectionFactory->create()
            ->getProductListByIds($productIds);
        foreach ($associateProductIds as $productId) {
            try {
                $collection[] = $this->productRepository->getById($productId);
            } catch (Exception $e) {
                continue;
            }
        }

        return $collection;
    }
}
