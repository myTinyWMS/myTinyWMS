<template>
    <QrcodeReader @decode="onDecode" @init="onInit" :video-constraints="{ facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } }"></QrcodeReader>
</template>

<script>
    import { QrcodeReader } from 'vue-qrcode-reader'

    export default {
        components: { QrcodeReader },

        methods: {
            onDecode (decodedString) {
                alert(decodedString);
            },
            async onInit (promise) {
                // show loading indicator

                try {
                    await promise

                    // successfully initialized
                } catch (error) {
                    alert(error.name);
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
                    // hide loading indicator
                }
            }
        }
    }

</script>