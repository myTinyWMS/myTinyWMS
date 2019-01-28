<?php

namespace Mss\Services;

use Barryvdh\Snappy\PdfWrapper;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PrintLabelService {

    const PDF_OPTIONS_SMALL = [
        'page-width' => 61,
        'page-height' => 28,
        'margin-bottom' => 0,
        'margin-left' => 0,
        'margin-right' => 0,
        'margin-top' => 0,
        'encoding' => 'utf-8'
    ];

    const PDF_OPTIONS_LARGE = [
        'page-width' => 152,
        'page-height' => 100,
        'margin-bottom' => 4,
        'margin-left' => 4,
        'margin-right' => 4,
        'margin-top' => 4,
        'encoding' => 'utf-8',
        'orientation' => 'Landscape'
    ];

    /**
     * @param Collection $articles
     * @return boolean
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function printArticleLabels(Collection $articles, $labelSize = 'small') {
        $barcodes = [];
        $articles->each(function ($article) use (&$barcodes, $labelSize) {
            $barcodes[$article->id] = ($labelSize == 'large') ? $this->generateQrLarge($article->article_number ?? '#'.$article->id) : $this->generateQrSmall($article->article_number ?? '#'.$article->id);
        });

        /**
         * @var PdfWrapper $pdf
         */
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf = $pdf->loadView('documents.article_labels_'.$labelSize, compact('barcodes', 'articles'));
        $options = ($labelSize == 'large') ? self::PDF_OPTIONS_LARGE : self::PDF_OPTIONS_SMALL;
        $pdf->setOptions($options);

        return $this->sendPdfToLocalPrinter($pdf, $labelSize);
    }

    /**
     * @param PdfWrapper $pdf
     * @param string $labelSize
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendPdfToLocalPrinter(PdfWrapper $pdf, $labelSize = 'small') {
        $client = new Client();
        $printer = ($labelSize == 'large') ? 'normal' : 'brother';
        $response = $client->request('POST', env('PRINT_URL'), [
            'connect_timeout' => 5,
            'http_errors' => false,
            'multipart' => [
                ['name' => 'type', 'contents' => $printer],
                ['name' => 'format', 'contents' => 'pdf'],
                ['name' => 'data', 'contents' => $pdf->output(), 'filename' => 'label.pdf']
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            Log::warning('Label printing failed', ['reponse' => $response->getBody()->getContents()]);
        }

        return ($response->getStatusCode() == 200);
    }

    /**
     * @param $value
     * @return \CodeItNow\BarcodeBundle\Utils\type
     * @throws \Exception
     */
    protected function generateQrSmall($value) {
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

    /**
     * @param $value
     * @return \CodeItNow\BarcodeBundle\Utils\type
     * @throws \Exception
     */
    protected function generateQrLarge($value) {
        $qrCode = new QrCode();
        $qrCode
            ->setText($value)
            ->setSize(250)
            ->setPadding(0)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel('')
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        return $qrCode->generate();
    }
}