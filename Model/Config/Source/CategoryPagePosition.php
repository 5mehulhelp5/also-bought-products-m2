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
 * Class CategoryPagePosition
 * @package MageMoto\AlsoBought\Model\Config\Source
 */
class CategoryPagePosition implements OptionSourceInterface
{
    const BEFORE_CONTENT = 'before-content';
    const AFTER_CONTENT  = 'after-content';
    const BEFORE_SIDEBAR = 'before-sidebar';
    const AFTER_SIDEBAR  = 'after-sidebar';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BEFORE_CONTENT, 'label' => __('Main Content Top')],
            ['value' => self::AFTER_CONTENT, 'label' => __('Main Content Bottom')],
            ['value' => self::BEFORE_SIDEBAR, 'label' => __('Sidebar Main Top')],
            ['value' => self::AFTER_SIDEBAR, 'label' => __('Sidebar Main Bottom')]
        ];
    }
}
