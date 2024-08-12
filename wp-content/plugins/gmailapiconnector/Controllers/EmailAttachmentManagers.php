<?php

namespace GMAIL\Controllers;

use Google\Service\Gmail;
use Google\Service\Gmail\MessagePart;

class EmailAttachmentManagers {
    
    public function process_attachment($service, $part, $messageId) {
        
        $mimeType = $part->mimeType;

        $attachmentId = $part->body->attachmentId;

        $attachmentData = $service->users_messages_attachments->get('me', $messageId, $attachmentId);

        $attachmentData = $attachmentData->getData();

        $decodedAttachmentData = base64_decode(str_replace(['-', '_'], ['+', '/'], $attachmentData));

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/png':
                $imageHandler = new ImageManagers();
                return $imageHandler->processImage($decodedAttachmentData);
                break;
            case 'application/pdf':
                $pdfHandler = new PdfManagers();
                return $pdfHandler->processPdf($decodedAttachmentData);
                break;
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $docHandler = new WordManagers();
                return $docHandler->processDoc($decodedAttachmentData);
                break;
            default:
                // Handle other attachment types
                break;
        }
    }
}