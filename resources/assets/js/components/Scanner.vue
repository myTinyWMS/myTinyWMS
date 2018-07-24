<script>
    export default {
        data() {
            return {
                currentScanner: 'keyboard',
                tabs: ['keyboard', 'qr']
            }
        },
        computed: {
            currentScannerComponent: function () {
                return 'scan-' + this.currentScanner
            }
        }
    }
</script>