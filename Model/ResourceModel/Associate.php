<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use MageMoto\AlsoBought\Helper\Data;
use MageMoto\AlsoBought\Model\Config\Source\Type;
use Zend_Db_Select;

/**
 * Class Rpf
 * @package MageMoto\AlsoBought\Model\ResourceModel
 */
class Associate extends AbstractDb
{
    /**
     * @var CollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var Product
     */
    protected $resourceModel;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var array Save all product ID indexed with status exist or not
     */
    private $productExistIds = [];

    /**
     * Associate constructor.
     *
     * @param Context $context
     * @param CollectionFactory $orderItemCollectionFactory
     * @param Data $helper
     * @param Product $resourceModel
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        CollectionFactory $orderItemCollectionFactory,
        Data $helper,
        Product $resourceModel,
        $connectionName = null
    ) {
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->helper                     = $helper;
        $this->resourceModel              = $resourceModel;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('mm_wbab_product_assoc', 'assoc_id');
    }

    /**
     * Reset data also bought get from order
     *
     * @param bool $all
     * @param null $orderIds
     *
     * @return bool
     * @throws LocalizedException
     */
    public function reIndex($all = false, $orderIds = null)
    {
        $currentOrderId = $all ? 0 : $this->helper->getCurrentOrderId();
        if (!$currentOrderId) {
            $all = true;
        }

        if ($all) {
            $this->getConnection()->delete($this->getMainTable());
        }

        $processOrderStatus = $this->helper->getProcessOrderStatus();
        if (empty($processOrderStatus)) {
            return false;
        }
        if ($all || !$orderIds) {
            $condition = ['gt' => $currentOrderId];
        } else {
            $condition = ['in' => $orderIds];
        }

        $itemCollection = $this->getItemCollection($processOrderStatus, $condition);
        if (!$itemCollection->getSize()) {
            return false;
        }

        $customerPurchases = [];
        $orderId           = 0;
        $isCustomerType    = $this->helper->getIndexType() === Type::TYPE_CUSTOMER;
        foreach ($itemCollection as $item) {
            $orderId = max($orderId, $item->getOrderId());

            $productId = $item->getProductId();
            if (!$this->checkProductExist($productId)) {
                continue;
            }

            $key = ($isCustomerType && $item->getCustomerId()) ? 'c_' . $item->getCustomerId() : 'o_' . $item->getOrderId();
            if (!isset($customerPurchases[$key])) {
                $customerPurchases[$key] = [];
            }

            $customerPurchases[$key][$productId] = 1;
        }
        unset($itemCollection); // unset $itemCollection to free memory

        $productSup   = [];
        $productAssoc = [];

        /** @var array $purchase */
        foreach ($customerPurchases as $purchase) {
            foreach ($purchase as $productId => $value) {
                if (!isset($productSup[$productId])) {
                    $productSup[$productId]   = 0;
                    $productAssoc[$productId] = [];
                }
                $productSup[$productId]++;

                $this->addAssociate($productId, $purchase, $productAssoc[$productId]);
            }
        }
        unset($customerPurchases); // unset $customerPurchases to free memory

        $dataAssocInsert = [];
        /** @var array $proArray */
        foreach ($productAssoc as $proId => $proArray) {
            if (!$all) {
                $row = $this->getConnection()->fetchRow('Select * from ' . $this->getMainTable() . ' where product_id = ' . $proId);
                if (!empty($row)) {
                    $productSup[$proId] += $row['product_sup'];
                    $this->getConnection()->update($this->getMainTable(), [
                        'product_sup' => $productSup[$proId],
                        'rpf'         => $row['sup'] * $row['sup'] / $productSup[$proId]
                    ], 'product_id = ' . $proId);
                }
            }

            foreach ($proArray as $proAssocId => $sup) {
                if (!$all) {
                    $row = $this->getConnection()->fetchRow('Select * from ' . $this->getMainTable() . ' where product_id = ' . $proId . ' and product_assoc_id = ' . $proAssocId);
                    if (!empty($row)) {
                        $sup = $row['sup'] + $sup;
                        $this->getConnection()->update($this->getMainTable(), [
                            'sup' => $sup,
                            'rpf' => $sup * $sup / $row['product_sup']
                        ], 'product_id = ' . $proId . ' and product_assoc_id = ' . $proAssocId);
                        continue;
                    }
                }
                $dataAssocInsert[] = [
                    'product_id'       => $proId,
                    'product_assoc_id' => $proAssocId,
                    'product_sup'      => $productSup[$proId],
                    'sup'              => $sup,
                    'rpf'              => $sup * $sup / $productSup[$proId]
                ];
            }

            if (count($dataAssocInsert) >= 1000) {
                $this->getConnection()->insertMultiple($this->getMainTable(), $dataAssocInsert);
                $dataAssocInsert = [];
            }
        }

        if (!empty($dataAssocInsert)) {
            $this->getConnection()->insertMultiple($this->getMainTable(), $dataAssocInsert);
        }

        if ($orderId > $this->helper->getCurrentOrderId()) {
            $this->helper->setCurrentOrderId($orderId);
        }

        return true;
    }

    /**
     * @param int $productId
     * @param array $purchase
     * @param array $associate
     *
     * @return $this
     */
    private function addAssociate($productId, $purchase, &$associate)
    {
        foreach ($purchase as $proId => $value) {
            if ($proId === $productId) {
                continue;
            }

            if (!isset($associate[$proId])) {
                $associate[$proId] = 0;
            }

            $associate[$proId]++;
        }

        return $this;
    }

    /**
     * Join sales_order & sales_order_item get data
     *
     * @param $processOrderStatus
     * @param mixed $condition
     * @param null $fromDate
     *
     * @return Collection
     */
    private function getItemCollection($processOrderStatus, $condition, $fromDate = null)
    {
        /** @var Collection $itemCollection */
        $itemCollection = $this->orderItemCollectionFactory->create()
            ->addFieldToFilter('order_id', $condition)
            ->addFieldToFilter('parent_item_id', ['null' => true]);

        if (!$this->helper->getConfigGeneral('free_item')) {
            $itemCollection->addFieldToFilter('price', ['gt' => 0]);
        }

        if ($fromDate) {
            $itemCollection->addFieldToFilter('created_at', ['from' => $fromDate]);
        }

        $itemCollection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(
                [
                    'product_id' => 'product_id',
                    'order_id'   => 'order_id',
                    'store_id'   => 'store_id',
                ]
            );
        $itemCollection->getSelect()
            ->join(
                ['or' => $this->getTable('sales_order')],
                'main_table.order_id = or.entity_id AND or.status IN ("' . implode('","', $processOrderStatus) . '")',
                ['status', 'customer_id']
            );

        return $itemCollection;
    }

    /**
     * @param $productId
     *
     * @return mixed
     */
    private function checkProductExist($productId)
    {
        if (!array_key_exists($productId, $this->productExistIds)) {
            $connection = $this->resourceModel->getConnection();
            $select     = $connection->select()->from($this->resourceModel->getEntityTable(), 'entity_id')
                ->where('entity_id = :entity_id');

            if ($connection->fetchOne($select, [':entity_id' => $productId])) {
                $this->productExistIds[$productId] = true;
            } else {
                $this->productExistIds[$productId] = false;
            }
        }

        return $this->productExistIds[$productId];
    }
}
