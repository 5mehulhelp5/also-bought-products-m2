<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Block\Product\ProductList;

use MageMoto\AlsoBought\Model\Config\Source\Layout;

/**
 * Class Widget
 * @package MageMoto\AlsoBought\Block\Product\ProductList
 */
class Widget extends AlsoBought
{
    /**
     * Get Show list Config
     *
     * @return array
     */
    public function getShowList()
    {
        return explode(',', $this->getData('show_list'));
    }

    /**
     * Get heading label
     *
     * @return string
     */
    public function getTitleBlock()
    {
        return $this->getData('title');
    }

    /**
     * Get layout config
     *
     * @return bool
     */
    public function getLayoutSlider()
    {
        $layout = $this->getData('design');

        return $layout === Layout::TYPE_SLIDER;
    }

    /**
     * @return string
     */
    public function getWidgetBlockName()
    {
        return 'mmalsobouhgt-widget-block-' . uniqid('', false);
    }
}
