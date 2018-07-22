@extends('layout.handscanner')

@section('content')
    <h4 class="text-center">Schritt 1:</h4>
    <br/>
    <br/>
    <div class="jumbotron text-center">Bitte einen Artikel scannen</div>
    <div id="scannerview"></div>
@endsection

@section('subheader')
    <div class="subheader">Inventur</div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Create the QuaggaJS config object for the live stream
            var liveStreamConfig = {
                inputStream: {
                    type : "LiveStream",
                    target: document.querySelector('#scannerview'),
                    constraints: {
                        width: {min: 640},
                        height: {min: 480},
                        aspectRatio: {min: 1, max: 100},
                        facingMode: "environment" // or "user" for the front camera
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: (navigator.hardwareConcurrency ? navigator.hardwareConcurrency : 4),
                decoder: {
                    "readers":[
                        {"format":"ean_reader","config":{}}
                    ]
                },
                locate: true
            };
            // The fallback to the file API requires a different inputStream option.
            // The rest is the same
            var fileConfig = $.extend(
                {},
                liveStreamConfig,
                {
                    inputStream: {
                        size: 800
                    }
                }
            );

            Quagga.init(
                liveStreamConfig,
                function(err) {
                    if (err) {
                        console.log(err);
                        alert(error.message);
                        Quagga.stop();
                        return;
                    }
                    Quagga.start();
                }
            );

            // Make sure, QuaggaJS draws frames an lines around possible
            // barcodes on the live stream
            Quagga.onProcessed(function(result) {
                var drawingCtx = Quagga.canvas.ctx.overlay,
                    drawingCanvas = Quagga.canvas.dom.overlay;

                if (result) {
                    if (result.boxes) {
                        drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                        result.boxes.filter(function (box) {
                            return box !== result.box;
                        }).forEach(function (box) {
                            Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                        });
                    }

                    if (result.box) {
                        Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                    }

                    if (result.codeResult && result.codeResult.code) {
                        Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                    }
                }
            });

            // Once a barcode had been read successfully, stop quagga and
            // close the modal after a second to let the user notice where
            // the barcode had actually been found.
            Quagga.onDetected(function(result) {
                if (result.codeResult.code){
                    //$('#scanner_input').val(result.codeResult.code);
                    alert(result.codeResult.code);
                    Quagga.stop();
                }
            });

            // Stop quagga in any case, when the modal is closed
            $('#livestream_scanner').on('hide.bs.modal', function(){
                if (Quagga){
                    Quagga.stop();
                }
            });
        });


        /*console.log('startup');
        Quagga.init({
            inputStream : {
                name : "Live",
                type : "LiveStream",
                target: document.querySelector('#scannerview')
            },
            decoder : {
                readers : ["code_39_reader"]
            }
        }, function(err) {
            if (err) {
                console.log(err);
                return
            }
            console.log("Initialization finished. Ready to start");
            Quagga.start();
        });

        Quagga.onDetected(function (data) {
            alert(data.codeResult.code);
        });*/


    </script>
@endpush