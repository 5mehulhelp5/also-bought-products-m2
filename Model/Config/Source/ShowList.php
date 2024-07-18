<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ShowList implements ArrayInterface
{
    const SHOW_PRICE  = 'price';
    const SHOW_CART   = 'addtocart';
    const SHOW_REVIEW = 'review';
    const SHOW_ADD    = 'addto';

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
            self::SHOW_PRICE  => __('Price'),
            self::SHOW_CART   => __('Add to cart'),
            self::SHOW_REVIEW => __('Review'),
            self::SHOW_ADD    => __('Add to Wish List & Add to Compare')
        ];
    }
}
