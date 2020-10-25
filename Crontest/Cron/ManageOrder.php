<?php

namespace Michel\Crontest\Cron;

class ManageOrder extends \Magento\Framework\App\Action\Action
{
    public $context;
    public $_orderCollection;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->context = $context;
        $this->_orderCollection = $orderCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $conditionStatus = ['eq' => 'pending'];

        $date = (new \DateTime())->modify('-20 minutes');
        $conditionCreatedAt = ['lteq' => $date];

        $orders = $this->_orderCollection->create()->addAttributeToSelect('*')->addFieldToFilter('status', $conditionStatus)
            ->addFieldToFilter('created_at', $conditionCreatedAt);
        if ($orders->getTotalCount() >= 1) {
            foreach ($orders->getItems() as $order) {
                $order->cancel()->save();
            }
        }

        return $this;
    }
}
