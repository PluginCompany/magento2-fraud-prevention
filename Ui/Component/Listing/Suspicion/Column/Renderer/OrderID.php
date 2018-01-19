<?php
namespace PluginCompany\FraudPrevention\Ui\Component\Listing\Suspicion\Column\Renderer;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Ui\Component\Listing\Columns\Column;

class OrderID extends Column
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepository $orderRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepository $orderRepository,
        array $components = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['increment_id'] = $this->getOrderLink($item['order_id']);
            }
        }
        return $dataSource;
    }

    public function getOrderLink($orderId)
    {
        if(!$orderId) return;
        $url = $this->getEditUrl($orderId);
        $name = $this->getOrderIncrementId($orderId);
        return "<a href='$url'>$name</a>";
    }

    private function getEditUrl($orderId)
    {
        return $this->context
            ->getUrl(
                'sales/order/view',
                ['order_id' => $orderId]
            );
    }

    private function getOrderIncrementId($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        return $order->getIncrementId();
    }
}
