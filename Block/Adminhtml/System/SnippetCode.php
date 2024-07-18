<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SnippetCode extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div class="control-value" style="padding-top: 8px">';
        $html .= '<p>' . __('Use the following code to show Who Bought This Also Bought Block in any places which you want on Product Pages, Category Pages') . '</p>';

        $html .= '<strong>' . __('CMS Page/Static Block') . '</strong><br />';
        $html .= '<pre style="background-color: #f5f5dc"><code>{{block class="Mavenbird\AlsoBought\Block\Product\ProductList\Widget" title="Who bought this item also bought" design="slider" show_list="price,addtocart,review,addto"}}</code></pre>';

        $html .= '<strong>' . __('Template .phtml file') . '</strong><br />';
        $html .= '<pre style="background-color: #f5f5dc"><code>' . $this->_escaper->escapeHtml(
            '<?php echo $block->getLayout()->createBlock("Mavenbird\AlsoBought\Block\Product\ProductList\Widget")
            ->setData([
                "title" => "Who bought this item also bought",
                "design" => "slider",
                "show_list" => "price,addtocart,review,addto"
                ])
            ->toHtml();?>'
        ) . '</code></pre>';

        $html .= '<strong>' . __('Layout file') . '</strong><br />';
        $html .= '<pre style="background-color: #f5f5dc"><code>' . $this->_escaper->escapeHtml(
            '<block class="Mavenbird\AlsoBought\Block\Product\ProductList\Widget" name="mmalsobouhgt-snippet-code" >
    <arguments>
        <argument name="title" xsi:type="string">Who bought this item also bought</argument>
        <argument name="design" xsi:type="string">slider</argument>
        <argument name="show_list" xsi:type="string">price,addtocart,review,addto</argument>
    </arguments>
</block>'
        ) . '</code></pre>';

        $html .= '</div>';

        return $html;
    }
}
