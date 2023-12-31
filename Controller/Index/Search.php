<?php

/**
 * @package     Team Ode To Code
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\B2bSpace\Controller\Index;

use Codilar\B2bSpace\Model\B2bSpace as MessageModel;
use Codilar\B2bSpace\Model\ResourceModel\B2bSpace\Collection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Search implements ActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var MessageModel
     */
    private MessageModel $messageModel;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var Collection
     */
    private Collection $message;

    /**
     * Search Constructor
     *
     * @param RequestInterface $request
     * @param MessageModel $messageModel
     * @param Collection $message
     * @param ResourceConnection $resourceConnection
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        RequestInterface $request,
        MessageModel $messageModel,
        Collection $message,
        ResourceConnection $resourceConnection,
        JsonFactory $jsonFactory
    ) {
        $this->request = $request;
        $this->messageModel = $messageModel;
        $this->resourceConnection = $resourceConnection;
        $this->jsonFactory = $jsonFactory;
        $this->message = $message;
    }

    /**
     * Return the Search data
     *
     * @return Json
     */
    public function execute(): Json
    {
        $response = [];
        $resultJson = $this->jsonFactory->create();
        $searchQuery = $this->request->getParams()['query'];
        $count = 0;

        if ($searchQuery) {
            try {
                $messageCollection = $this->message->addFieldToFilter('message', ['like' => $searchQuery . '%']);
                if (count($messageCollection)) {
                    $count = count($messageCollection);
                    foreach ($messageCollection as $message) {
                        $response[$message->getEntityID()]['message'] = $message->getMessage();
                    }
                }
            } catch (\Exception $exception) {
                $response['status'] = 400;
                $response['exception'] = $exception->getMessage();
            }
        }
        return $resultJson->setData([
            'html' => $response,
            'count' => $count
        ]);
    }
}
