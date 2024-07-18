<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageMoto\AlsoBought\Helper\AbstractData;

/**
 * Class Data
 * @package MageMoto\AlsoBought\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'alsobought';

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param WriterInterface $configWriter
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter
    ) {
        $this->configWriter = $configWriter;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Get Config
     *
     * @param $field
     * @param null $storeId
     *
     * @return bool|mixed
     */
    public function getConfig($field, $storeId = null)
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }
        $action = $this->getData('action') ?: $this->_request->getFullActionName();

        return $this->getModuleConfig($action . '/' . $field, $storeId);
    }

    /**
     * Get Process Order Status Config
     *
     * @return mixed
     */
    public function getProcessOrderStatus()
    {
        if ($this->getConfigGeneral('process_order_status') && $this->isEnabled()) {
            return explode(',', $this->getConfigGeneral('process_order_status'));
        }

        return false;
    }

    /**
     * @return int
     */
    public function getCurrentOrderId()
    {
        return (int) $this->getConfigGeneral('also_bought_current_order_id');
    }

    /**
     * @param $orderId
     */
    public function setCurrentOrderId($orderId)
    {
        $this->configWriter->save(static::CONFIG_MODULE_PATH . '/general/also_bought_current_order_id', $orderId);
    }

    /**
     * @return int
     */
    public function getIndexType()
    {
        return (int) $this->getConfigGeneral('type');
    }

    /**
     * Get Show list Config
     *
     * @return array
     */
    public function getShowList()
    {
        return explode(',', $this->getConfig('show_list'));
    }
}
