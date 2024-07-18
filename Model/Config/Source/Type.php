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

/**
 * Class IndexType
 * @package MageMoto\AlsoBought\Model\Config\Source
 */
class Type implements OptionSourceInterface
{
    const TYPE_ORDER    = 1;
    const TYPE_CUSTOMER = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function toArray()
    {
        return [
            self::TYPE_ORDER    => __('Order'),
            self::TYPE_CUSTOMER => __('Customer')
        ];
    }
}
