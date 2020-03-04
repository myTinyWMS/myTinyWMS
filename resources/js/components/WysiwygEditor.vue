<template>
    <div>
        <input type="hidden" :name="name" v-model="localContent">
        <vueSummernoteLite ref="editor" :config="config" @ready="readyState"></vueSummernoteLite>
    </div>
</template>

<script>
    import vueSummernoteLite from "vue-summernote-lite";

    let SignatureButton = function (context) {
        let ui = $.summernote.ui;

        // create button
        let button = ui.button({
            contents: '<i class="fa fa-plus"/> ' + this.$t('Signatur'),
            tooltip: this.$t('Signatur einfügen'),
            click: function () {
                context.invoke('editor.pasteHTML', `{!! html_entity_decode(Auth::user()->signature) !!}`);
            }
        });

        return button.render();   // return button as jquery object
    };

    export default {
        components: {
            vueSummernoteLite
        },
        props : {
            showSignatureButton: false,
            signature: '',
            name: {
                required: true
            },
            content: {},
        },
        computed: {
            config: function() {
                let config = {
                    lang: 'de-DE',
                    placeholder: '',
                    height: 800,
                    fontNames: [
                        'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New',
                        'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande',
                        'Tahoma', 'Times New Roman', 'Verdana'
                    ],
                    toolbar: this.toolbarElements,
                };

                if (this.showSignatureButton) {
                    config['buttons'] = {
                        signature: this.signatureButton
                    }
                }

                return config;
            },
            signatureButton: function() {
                let that = this;

                let button = $.summernote.ui.button({
                    contents: '<i class="fa fa-plus"/> ' + this.$t('Signatur'),
                    tooltip: this.$t('Signatur einfügen'),
                    click: function () {
                        that.$refs.editor.summernote('pasteHTML', '<div>' + that.signature + '</div>');
                    }
                });

                return button.render();
            },
            toolbarElements: function() {
                let toolbar = [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],

                ];
                if (this.showSignatureButton) {
                    toolbar.push(['custom', ['signature']]);
                }

                toolbar.push(['help', ['help']]);

                return toolbar;
            }
        },
        data() {
            return {
                localContent: this.content,
            }
        },
        beforeDestroy() {
            // Always destroy your editor instance when it's no longer needed
            // this.editor.destroy()
        },
        methods: {
            readyState(editor) {
                editor.summernote('code', this.content);
                editor.$on("change", contents => {
                    this.localContent = contents;
                });
                editor.$on("focus", function() {

                });
                editor.$on("blur", function() {

                });
                editor.$on("paste", function() {

                });
            }
        }
    }
</script>
