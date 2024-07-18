<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model\ResourceModel\Associate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MageMoto\AlsoBought\Model\ResourceModel\Associate;

/**
 * Class Collection
 * @package MageMoto\AlsoBought\Model\ResourceModel\AlsoBought
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageMoto\AlsoBought\Model\Associate::class, Associate::class);
    }

    /**
     * Get all product also bought from DB by passed id.
     *
     * @param $productId
     * @param null $limit
     *
     * @return array
     */
    public function getProductListById($productId, $limit = null)
    {
        $this->addFieldToFilter('product_id', $productId)
            ->setOrder('rpf', 'DESC');

        if ($limit) {
            $this->setPageSize($limit);
        }

        return $this->getColumnValues('product_assoc_id');
    }

    /**
     * @param array $productIds
     * @param null $limit
     *
     * @return array
     */
    public function getProductListByIds($productIds, $limit = null)
    {
        $this->addFieldToFilter('product_id', ['in' => $productIds])
            ->setOrder('rpf', 'DESC');
        $this->getSelect()->group('product_assoc_id');

        if ($limit) {
            $this->getSelect()->limit($limit);
        }

        return $this->getColumnValues('product_assoc_id');
    }

    /**
     * Truncate table magemoto_wbab_product_link
     *
     * @return void
     */
    public function clear()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}
