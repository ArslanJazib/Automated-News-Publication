<?php

namespace GMAIL\Controllers;

use Smalot\PdfParser\Parser;
class PdfManagers {

    public function __construct() { }

    public function processPdf($pdfData) {

        $parser = new Parser();

        $pdfContentParser = $parser->parseContent($pdfData);

        $pdfContent = $pdfContentParser->getText();

        $pdfContent.="<div class='pdf-content'>".$pdfContent."</div>";

        return $pdfContent;
    }
}