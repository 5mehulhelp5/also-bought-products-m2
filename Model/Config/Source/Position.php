<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Position implements OptionSourceInterface
{
    const BEFORE_CONTENT  = 'before-content';
    const AFTER_CONTENT   = 'after-content';
    const BEFORE_RELATED  = 'before-related';
    const AFTER_RELATED   = 'after-related';
    const BEFORE_UPSELL   = 'before-upsell';
    const AFTER_UPSELL    = 'after-upsell';
    const BEFORE_INFO_TAB = 'before-info';
    const MANUALLY        = 'manually';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BEFORE_CONTENT, 'label' => __('Main Content Top')],
            ['value' => self::AFTER_CONTENT, 'label' => __('Main Content Bottom')],
            ['value' => self::BEFORE_RELATED, 'label' => __('Before Native Related Products Block')],
            ['value' => self::AFTER_RELATED, 'label' => __('After Native Related Products Block')],
            ['value' => self::BEFORE_UPSELL, 'label' => __('Before Up-sells Products Block')],
            ['value' => self::AFTER_UPSELL, 'label' => __('After Up-sells Products Block')],
            ['value' => self::BEFORE_INFO_TAB, 'label' => __('Before Information Tab')],
            ['value' => self::MANUALLY, 'label' => __('Manually')]
        ];
    }
}
