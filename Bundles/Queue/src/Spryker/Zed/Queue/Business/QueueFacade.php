<?php

namespace Spryker\Zed\Queue\Business;

use Generated\Shared\Transfer\QueueMessageTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Shared\Kernel\Messenger\MessengerInterface;

/**
 * @method QueueBusinessFactory getFactory()
 */
class QueueFacade extends AbstractFacade
{

    /**
     * @param string $queueName
     * @param QueueMessageTransfer $queueMessage
     *
     * @return void
     */
    public function publishMessage($queueName, QueueMessageTransfer $queueMessage)
    {
        $this->getFactory()
            ->createQueueConnection()
            ->publish($queueName, $queueMessage);
    }

    /**
     * @param string $queueName
     * @param MessengerInterface $messenger
     * @param int $timeout
     * @param int $fetchSize
     *
     * @return void
     */
    public function startWorker(
        $queueName,
        MessengerInterface $messenger,
        $timeout = 10,
        $fetchSize = 10
    ) {
        $this->getFactory()
            ->createTaskWorker($queueName, $messenger)
            ->work($timeout, $fetchSize);
    }

}