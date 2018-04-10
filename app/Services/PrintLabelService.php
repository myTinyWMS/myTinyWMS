<?php

namespace Mss\Services;

use CodeItNow\BarcodeBundle\Utils\QrCode;
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
            'margin-top' => 1,
            'encoding' => 'utf-8'
        ]);

        return $pdf;
    }

    /**
     * @param Collection $articles
     * @return mixed
     */
    public function printArticleLabels2(Collection $articles) {
        $barcodes = [];
        $articles->each(function ($article) use (&$barcodes) {
            $barcodes[$article->id] = $this->generateQr($article->article_number);
        });

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf = $pdf->loadView('documents.article_labels2', compact('barcodes', 'articles'));
        $pdf->setOptions([
            'page-width' => 62,
            'page-height' => 29,
            'margin-bottom' => 1,
            'margin-left' => 1,
            'margin-right' => 1,
            'margin-top' => 4,
            'encoding' => 'utf-8'
        ]);

        return $pdf;
    }

    /**
     * @param Collection $articles
     * @return mixed
     */
    public function printArticleLabels3(Collection $articles) {
        $barcodes = [];
        $articles->each(function ($article) use (&$barcodes) {
            $barcodes[$article->id] = $this->generateQr2($article->article_number);
        });

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf = $pdf->loadView('documents.article_labels3', compact('barcodes', 'articles'));
        $pdf->setOptions([
            'page-width' => 62,
            'page-height' => 29,
            'margin-bottom' => 1,
            'margin-left' => 1,
            'margin-right' => 1,
            'margin-top' => 4,
            'encoding' => 'utf-8'
        ]);

        return $pdf;
    }

    protected function generateQr($value) {
        $qrCode = new QrCode();
        $qrCode
            ->setText($value)
            ->setSize(95)
            ->setPadding(0)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel('')
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        return $qrCode->generate();
    }

    protected function generateQr2($value) {
        $qrCode = new QrCode();
        $qrCode
            ->setText($value)
            ->setSize(50)
            ->setPadding(0)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel('')
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        return $qrCode->generate();
    }

    protected function generateBarcode($value) {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code39);
        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(26);
        return $barcode->generate();
    }
}