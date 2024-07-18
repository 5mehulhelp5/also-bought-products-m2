<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageMoto\AlsoBought\Block\Product\ProductList\AlsoBought;
use MageMoto\AlsoBought\Block\Product\ProductList\ProductList;
use MageMoto\AlsoBought\Helper\Data;
use MageMoto\AlsoBought\Model\Config\Source\CategoryPagePosition;

/**
 * Class AddBlock
 * @package MageMoto\AlsoBought\Observer
 */
class AddBlock implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * AddBlock constructor.
     *
     * @param Data $helperData
     * @param Session $checkoutSession
     * @param RequestInterface $request
     */
    public function __construct(
        Data $helperData,
        Session $checkoutSession,
        RequestInterface $request
    ) {
        $this->helperData      = $helperData;
        $this->_request        = $request;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled() || !$this->helperData->getConfig('enabled')) {
            return $this;
        }

        $actionName = $this->_request->getFullActionName();
        $types      = [];
        $enableLN   = $this->helperData->getConfigValue('layered_navigation/general/ajax_enable')
            || $this->helperData->getConfigValue('layered_navigation/general/enable');
        switch ($actionName) {
            case 'catalog_product_view':
                $types = [
                    'content' => 'content',
                    'related' => 'catalog.product.related',
                    'upsell'  => 'product.info.upsell',
                    'info'    => 'product.info.details'
                ];
                break;
            case 'catalog_category_view':
                $types = [
                    'content' => 'content',
                    'sidebar' => $enableLN ? 'layer.catalog.leftnav' : 'catalog.leftnav'
                ];
                break;
            case 'checkout_cart_index':
                $types = [
                    'content' => 'content',
                    'cross'   => 'checkout.cart.crosssell'
                ];
                break;
        }
        if (empty($types)) {
            return $this;
        }
        $type = array_search($observer->getElementName(), $types, true);
        if ($type) {
            $transport = $observer->getTransport();
            $output    = $transport->getOutput();
            $position  = $this->helperData->getConfig('position');
            $html      = $this->getBlockHtml($observer->getEvent(), $actionName, $position);
            if ('before-' . $type === $position) {
                $output = "<div class='mmalsobouhgt-block' id=\"magemoto-alsobought-block-{$position}\" data-position=\"{$position}\">" . $html . '</div>' . $output;
            } elseif ('after-' . $type === $position) {
                $output .= "<div class='mmalsobouhgt-block' id=\"magemoto-alsobought-block-{$position}\" data-position=\"{$position}\">" . $html . '</div>';
            }
            $transport->setOutput($output);
        }

        return $this;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCartItems()
    {
        $productIds = [];
        $quote      = $this->checkoutSession->getQuote();

        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if ($product) {
                $productIds[] = $product->getId();
            }
        }

        return $productIds;
    }

    /**
     * @param $event
     * @param $actionName
     * @param $position
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getBlockHtml($event, $actionName, $position)
    {
        if ($actionName === 'checkout_cart_index') {
            $productIds = $this->getCartItems();
            if (empty($productIds)) {
                return '';
            }
            $layout = $event->getLayout();
            $block  = $layout->createBlock(ProductList::class);

            return $block->setProductIds($productIds)
                ->setAction($actionName)
                ->toHtml();
        }

        if ($position === CategoryPagePosition::BEFORE_SIDEBAR
            || $position === CategoryPagePosition::AFTER_SIDEBAR
        ) {
            $template = 'MageMoto_AlsoBought::product/list/sidebar.phtml';
        } else {
            $template = 'MageMoto_AlsoBought::product/list/alsobought.phtml';
        }
        $layout = $event->getLayout();
        $block  = $layout->createBlock(AlsoBought::class);

        return $block->setTemplate($template)
            ->toHtml();
    }
}
