<template>
    <modal name="newNoteModal" height="auto" classes="modal">
        <h4 class="modal-title">Neue Notiz</h4>

        <div class="row">
            <div class="w-full">
                <div class="form-group">
                    <label for="new_note" class="form-label">Notiz</label>
                    <textarea id="new_note" v-model="new_note" name="content" class="form-textarea" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" @click="$modal.hide('newNoteModal')">Abbrechen</button>
            <button type="button" class="btn btn-primary" @click="submit()" id="addNoteSubmit">Speichern</button>
        </div>
    </modal>

</template>

<script>
    import axios from 'axios';

    export default {
        props: ['article'],

        data() {
            return {
                new_note: ''
            }
        },

        methods: {
            submit(e) {
                let that = this;

                if (that.new_note == '') {
                    alert('Bitte einen Text eingeben!');
                    e.preventDefault();
                    return false;
                }

                axios.post(route('article.add_note', that.article.id), {content: that.new_note}).then(function (data) {
                    location.reload();
                });
            }
        }
    }
</script>