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
 * Class CartPagePosition
 * @package MageMoto\AlsoBought\Model\Config\Source
 */
class CartPagePosition implements OptionSourceInterface
{
    const BEFORE_CONTENT = 'before-content';
    const AFTER_CONTENT  = 'after-content';
    const BEFORE_CROSS   = 'before-cross';
    const AFTER_CROSS    = 'after-cross';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BEFORE_CONTENT, 'label' => __('Main Content Top')],
            ['value' => self::AFTER_CONTENT, 'label' => __('Main Content Bottom')],
            ['value' => self::BEFORE_CROSS, 'label' => __('Before Cross-sells Products Block')],
            ['value' => self::AFTER_CROSS, 'label' => __('After Cross-sells Products Block')]
        ];
    }
}
