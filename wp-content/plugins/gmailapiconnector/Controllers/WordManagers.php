<?php

namespace GMAIL\Controllers;

use DOMDocument;
use DOMXPath;
use ZipArchive;

class WordManagers {

    public function __construct() { }

    public function processDoc($docData) {

        $tempFilename = tempnam(sys_get_temp_dir(), 'docx');

        file_put_contents($tempFilename, $docData);

        $docContent = '';

        $zip = new ZipArchive();

        if ($zip->open($tempFilename) === true) {

            $documentXml = $zip->getFromName('word/document.xml');

            $dom = new DOMDocument();

            $dom->loadXML($documentXml);

            $xpath = new DOMXPath($dom);
            
            $textElements = $xpath->query('//w:t');

            foreach ($textElements as $element) {
                $docContent .= $element->nodeValue . ' ';
            }

            $zip->close();

            $docContent.='<div class="doc-content">'.$docContent.'</div>';

            // Add text of docs in content
            return $docContent;
        } else {
            echo 'Failed to open the docx file';
        }
    }
}