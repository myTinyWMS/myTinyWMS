<template>
    <modal name="newNoteModal" height="auto" classes="modal">
        <h4 class="modal-title">Neue Notiz</h4>

        <form method="post" v-bind:action="route('article.add_note', [article.id])" @submit="submit">
            <div class="row">
                <div class="w-full">
                    <div class="form-group">
                        <label for="new_note" class="form-label">Notiz</label>
                        <textarea id="new_note"  name="content" class="form-textarea" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" v-bind:value="csrf" name="_token" />
                <button type="button" class="btn btn-default" @click="$modal.hide('newNoteModal')">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </form>
    </modal>

</template>

<script>
    export default {
        props: ['article'],

        data() {
            return {
                csrf: ""
            };
        },

        methods: {
            submit(e) {
                let that = this;

                if (that.new_note == '') {
                    alert('Bitte einen Text eingeben!');
                    e.preventDefault();
                    return false;
                }

                $.post(route('article.add_note', that.article.id), {content: that.new_note.note}).done(function (data) {
                    console.log(data);
                });
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
        }
    }
</script>