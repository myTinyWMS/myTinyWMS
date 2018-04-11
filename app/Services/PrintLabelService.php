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
            $barcodes[$article->id] = $this->generateQr($article->article_number);
        });

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf = $pdf->loadView('documents.article_labels', compact('barcodes', 'articles'));
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
            ->setSize(50)
            ->setPadding(0)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel('')
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        return $qrCode->generate();
    }
}