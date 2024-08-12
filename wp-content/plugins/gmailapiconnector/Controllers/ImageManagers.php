<?php

namespace GMAIL\Controllers;

use Google\Service\Gmail\MessagePart;

class ImageManagers {

    public function __construct() { }

    public function processImage($imageData) {

        $tempFile = $this->saveImageTemporarily($imageData);

        $sideload = $this->sideloadImage($tempFile);

        if (!empty($sideload['error'])) {
            echo $sideload['error'];
        }

        $attachmentId = $this->createAttachment($sideload);

        if (is_wp_error($attachmentId) || !$attachmentId) {
            echo "Attachment error";
        }

        $this->uploadImageToMedia($attachmentId, $sideload['file']);

        $imageUrl =  $sideload['url'];

        $imageContent = "<img class='img-fluid' src='".$imageUrl."' />";

        return $imageContent;
    }

    private function saveImageTemporarily($imageData) {
        $tempFile = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tempFile, $imageData);
        return $tempFile;
    }

    private function sideloadImage($tempFile) {
        $file = [
            'name'     => basename($tempFile),
            'type'     => mime_content_type($tempFile),
            'tmp_name' => $tempFile,
            'size'     => filesize($tempFile),
        ];

        return wp_handle_sideload($file, ['test_form' => false]);
    }

    private function createAttachment($sideload) {
        return wp_insert_attachment([
            'guid'           => $sideload['url'],
            'post_mime_type' => $sideload['type'],
            'post_title'     => basename($sideload['file']),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ], $sideload['file']);
    }

    private function uploadImageToMedia($attachmentId, $file) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        wp_update_attachment_metadata($attachmentId, wp_generate_attachment_metadata($attachmentId, $file));
    }
}