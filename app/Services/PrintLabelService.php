<?php

namespace Mss\Services;

use Barryvdh\Snappy\PdfWrapper;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PrintLabelService {

    /**
     * @param Collection $articles
     * @return boolean
     */
    public function printArticleLabels(Collection $articles) {
        $barcodes = [];
        $articles->each(function ($article) use (&$barcodes) {
            $barcodes[$article->id] = $this->generateQr($article->article_number);
        });

        /**
         * @var PdfWrapper $pdf
         */
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

        return $this->sendPdfToLocalPrinter($pdf);
    }

    protected function sendPdfToLocalPrinter(PdfWrapper $pdf) {
        $client = new Client();
        $response = $client->request('POST', env('PRINT_URL'), [
            'connect_timeout' => 5,
            'http_errors' => false,
            'multipart' => [
                ['name' => 'type', 'contents' => 'brother'],
                ['name' => 'format', 'contents' => 'pdf'],
                ['name' => 'data', 'contents' => $pdf->output(), 'filename' => 'label.pdf']
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            Log::warning('Label printing failed', ['reponse' => $response->getBody()->getContents()]);
        }

        return ($response->getStatusCode() == 200);
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