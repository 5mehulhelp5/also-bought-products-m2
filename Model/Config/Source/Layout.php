<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Layout
 * @package MageMoto\AlsoBought\Model\Config\Source
 */
class Layout implements ArrayInterface
{
    const TYPE_SLIDER = 'slider';
    const TYPE_LINES  = 'grid';

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
            self::TYPE_SLIDER => __('Product Slider'),
            self::TYPE_LINES  => __('Multiple Lines')
        ];
    }
}
