<?php
namespace Dotdigitalgroup\Email\Model\Sync\Contact;

class Delete extends \Dotdigitalgroup\Email\Model\Sync\Contact\Bulk
{


    public function sync($collection)
    {
        foreach($collection as $item)
        {
            $result = true;
            $websiteId = $item->getWebsiteId();
            $email = unserialize($item->getImportData());
            $this->_client = $this->_helper->getWebsiteApiClient($websiteId);

            if ($this->_client) {
                $apiContact = $this->_client->postContacts($email);
                if (!isset($apiContact->message) && isset($apiContact->id)) {
                    $result = $this->_client->deleteContact($apiContact->id);
                } elseif (isset($apiContact->message) && !isset($apiContact->id)) {
                    $result = $apiContact;
                }

                if ($result) {
                    $this->_handleSingleItemAfterSync($item, $result);
                }
            }
        }
    }

    protected function _handleSingleItemAfterSync($item, $result)
    {
        if (isset($result->message) or !$result) {
            $message = (isset($result->message)) ? $result->message : 'Error unknown';
            $item->setImportStatus(\Dotdigitalgroup\Email\Model\Importer::FAILED)
                ->setMessage($message)
                ->save();
        } else {
            $item->setImportStatus(\Dotdigitalgroup\Email\Model\Importer::IMPORTED)
                ->setImportFinished(time())
                ->setImportStarted(time())
                ->setMessage('')
                ->save();
        }
    }
}