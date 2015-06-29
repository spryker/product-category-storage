<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Checkout\Business\Model\Workflow\Task;

use SprykerFeature\Shared\Sales\Code\AbstractItemGrouper;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerFeature\Zed\Checkout\Business\Model\Workflow\Context;
use SprykerEngine\Zed\Kernel\Locator;

class ValidateStock extends AbstractTask
{

    /**
     * @var AbstractItemGrouper
     */
    protected $itemGrouper;

    /**
     * @param AbstractItemGrouper $itemGrouper
     */
    public function __construct(AbstractItemGrouper $itemGrouper)
    {
        $this->itemGrouper = $itemGrouper;
    }

    /**
     * @param Order   $transferOrder
     * @param Context $context
     * @param array $logContext
     */
    public function __invoke(OrderTransfer $transferOrder, Context $context, array $logContext)
    {
        $groupedItems = $this->itemGrouper->groupItemsBySku($transferOrder->getItems());
        /* @var \SprykerFeature\Shared\Sales\Transfer\OrderItem $item */
        foreach ($groupedItems as $item) {
            $isAvailable = Locator::getInstance()->availabilityCheckoutConnector()->pluginCheckAvailabilityPlugin()->isSellable($item->getSku(), $item->getQuantity()); //TODO to clean with checkout refactoring!!
            if (!$isAvailable) {
                $this->addError(\SprykerFeature_Shared_Checkout_Code_Messages::ERROR_OUT_OF_STOCK);
                break;
            }
        }
    }

}
