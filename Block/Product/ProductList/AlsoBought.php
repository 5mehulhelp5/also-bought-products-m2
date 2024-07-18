<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Block\Product\ProductList;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Mavenbird\AlsoBought\Helper\Data;
use Mavenbird\AlsoBought\Model\Config\Source\Layout;

class AlsoBought extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Mavenbird_AlsoBought::product/list/alsobought.phtml';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    protected $formKey;

    /**
     * AlsoBought constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->helper   = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getAjaxData()
    {
        $action = $this->getRequest()->getFullActionName();
        $this->helper->setData('action', $action);
        $isSlider = $this->getLayoutSlider();
        switch ($action) {
            case 'catalog_product_view':
                $entityId = [$this->registry->registry('current_product')->getId()];
                break;
            case 'catalog_category_view':
                $entityId = $this->registry->registry('current_category')->getId();
                break;
            default:
                $entityId = [];
        }

        if (empty($entityId)) {
            return '';
        }

        $params = [
            'url'             => $this->getUrl('alsobought/ajax/load'),
            'isSlider'        => $isSlider,
            'cache_lifetime'  => (int) $this->helper->getConfigGeneral('cache_lifetime') ?: 86400,
            'originalRequest' => [
                'action'              => $action,
                'entity_id'           => $entityId,
                'requesting_page_url' => $this->_urlBuilder->getCurrentUrl()
            ]
        ];

        return Data::jsonEncode($params);
    }

    /**
     * Get Show list Config
     *
     * @return array
     */
    public function getShowList()
    {
        return $this->helper->getShowList();
    }

    /**
     * Get heading label
     *
     * @return string
     */
    public function getTitleBlock()
    {
        return $this->helper->getConfig('heading_label');
    }

    /**
     * Get layout config
     *
     * @return bool
     */
    public function getLayoutSlider()
    {
        return $this->helper->getConfig('layout') === Layout::TYPE_SLIDER;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFormKeyHtml()
    {
        if (!$this->formKey) {
            $this->formKey = $this->getLayout()->createBlock(FormKey::class)->toHtml();
        }

        return $this->formKey;
    }

    /**
     * @return string
     */
    public function getWidgetBlockName()
    {
        return 'mmalsobouhgt-block-' . uniqid('', false);
    }
}
