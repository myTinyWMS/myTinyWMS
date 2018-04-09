<?php

namespace Mss\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Collection;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class PrintLabelService {

    /**
     * @param Collection $articles
     * @return mixed
     */
    public function printArticleLabels(Collection $articles) {
        $barcodes = [];
        $articles->each(function ($article) use (&$barcodes) {
            $barcodes[$article->id] = $this->generateBarcode($article->article_number);
        });

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf = $pdf->loadView('documents.article_labels', compact('barcodes', 'articles'));
        $pdf->setOptions([
            'page-width' => 62,
            'page-height' => 29,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'margin-right' => 0,
            'margin-top' => 3
        ]);

        return $pdf;
    }

    protected function generateBarcode($value) {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(14);
        return $barcode->generate();
    }
}