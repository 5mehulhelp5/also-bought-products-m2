<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Block\Product\ProductList;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\View\Element\FormKey;
use Magento\Widget\Block\BlockInterface;
use Mavenbird\AlsoBought\Helper\Data;
use Mavenbird\AlsoBought\Model\Config\Source\Layout;
use Mavenbird\AlsoBought\Model\ResourceModel\Associate\CollectionFactory;

class ProductList extends AbstractProduct implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Mavenbird_AlsoBought::product/list/items.phtml';

    /**
     * @var CollectionFactory
     */
    protected $associateCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var
     */
    protected $formKey;

    /**
     * ProductList constructor.
     *
     * @param Context $context
     * @param CollectionFactory $associateCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helper
     * @param UrlHelper $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $associateCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Data $helper,
        UrlHelper $urlHelper,
        array $data = []
    ) {
        $this->productRepository          = $productRepository;
        $this->associateCollectionFactory = $associateCollectionFactory;
        $this->helper                     = $helper;
        $this->urlHelper                  = $urlHelper;

        parent::__construct($context, $data);
    }

    /**
     * @param $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->helper->setData('action', $action);

        return $this;
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
     * @return Product[]
     */
    public function getProductCollection()
    {
        $limit               = $this->getLimitProduct();
        $collection          = [];
        $associateProductIds = $this->associateCollectionFactory->create()
            ->getProductListByIds($this->getProductIds());
        foreach ($associateProductIds as $productId) {
            try {
                $collection[] = $this->productRepository->getById($productId);
                if (--$limit === 0) {
                    break;
                }
            } catch (Exception $e) {
                $this->_logger->critical($e->getMessage());
            }
        }

        return $collection;
    }

    /**
     * Get post parameters
     *
     * @param Product $product
     *
     * @return array
     */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);

        return [
            'action' => $url,
            'data'   => [
                'product'                               => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        $requestingPageUrl = $this->getRequest()->getParam('requesting_page_url');

        if (!empty($requestingPageUrl)) {
            $additional['useUencPlaceholder'] = true;
            $url                              = parent::getAddToCartUrl($product, $additional);

            return str_replace('%25uenc%25', $this->urlHelper->getEncodedUrl($requestingPageUrl), $url);
        }

        return parent::getAddToCartUrl($product, $additional);
    }

    /**
     * get data for post by javascript in format acceptable to $.mage.dataPost widget
     *
     * @param string $params
     *
     * @return string
     */
    public function getPostData($params)
    {
        $requestingPageUrl                             = $this->getRequest()->getParam('requesting_page_url');
        $params                                        = Data::jsonDecode($params);
        $data                                          = $params['data'];
        $data[ActionInterface::PARAM_NAME_URL_ENCODED] = $this->urlHelper->getEncodedUrl($requestingPageUrl);

        return Data::jsonEncode(['action' => $params['action'], 'data' => $data]);
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
     * @return mixed
     */
    protected function getLimitProduct()
    {
        return max((int) $this->helper->getConfig('limit_product'), 0);
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
     * Get Show list Config
     *
     * @return array
     */
    public function getShowList()
    {
        return $this->helper->getShowList();
    }
}
