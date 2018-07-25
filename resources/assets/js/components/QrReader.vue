<template>
    <QrcodeReader @decode="onDecode" @init="onInit" :video-constraints="{ facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } }"></QrcodeReader>
</template>

<script>
    import { QrcodeReader } from 'vue-qrcode-reader'

    export default {
        components: { QrcodeReader },

        props: [
            'targetUrl'
        ],

        data() {
            return {
                scanned: ''
            }
        },

        created: function () {
            console.log('created');
            this.initKeyboard();
        },

        methods: {
            onDecode (decodedString) {
                window.location.href = this.targetUrl + decodedString;
            },

            initKeyboard () {
                console.log('init keyboard');
                var that = this;
                window.addEventListener('keypress', function(event) {
                    $('#output').append(event.code);
                    console.log(event.code);
                    if (event.keyCode == 35) {
                        window.location.href = this.targetUrl + that.scanned;
                    } else {
                        that.scanned += String.fromCharCode(event.charCode);
                    }
                });
            },

            async onInit (promise) {
                // show loading indicator


                try {
                    await promise
                    console.log('done?');
                    // successfully initialized
                } catch (error) {
                    console.log('error');

                    console.log(error.name);
                    if (error.name === 'NotAllowedError') {
                        // user denied camera access permisson
                    } else if (error.name === 'NotFoundError') {
                        // no suitable camera device installed
                    } else if (error.name === 'NotSupportedError') {
                        // page is not served over HTTPS (or localhost)
                    } else if (error.name === 'NotReadableError') {
                        // maybe camera is already in use
                    } else if (error.name === 'OverconstrainedError') {
                        // passed constraints don't match any camera. Did you requested the front camera although there is none?
                    } else {
                        // browser is probably lacking features (WebRTC, Canvas)
                    }
                } finally {
                    console.log('cam initialized');
                    // hide loading indicator
                }
            }
        }
    }

</script>