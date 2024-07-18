<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Class OrderStatus
 * @package MageMoto\AlsoBought\Model\Config\Source
 */
class OrderStatus implements OptionSourceInterface
{
    /**
     * @var CollectionFactory $statusCollectionFactory
     */
    private $statusCollectionFactory;

    /**
     * Construct
     *
     * @param CollectionFactory $statusCollectionFactory
     */
    public function __construct(CollectionFactory $statusCollectionFactory)
    {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->statusCollectionFactory->create()->toOptionArray();
    }
}
