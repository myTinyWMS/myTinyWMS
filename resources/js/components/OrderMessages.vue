<template>
    <div class="row flex order-messages">
        <div class="tabs border-r">
            <div class="flex flex-col py-2 pr-4 pl-2 border-b" v-for="(message, index) in messages" :key="index" :class="currentIndex == index ? 'active' : ''" @click="currentIndex = index">
                <div class="flex">
                    <div class="w-1/2 font-bold text-sm" :title="message.user ? message.user.name : ''">
                        {{ getSenderName(message) }}
                    </div>
                    <div class="w-1/2 text-xs text-gray-500 text-right" :title="message.received | moment('DD.MM.YYYY HH:mm:SS')">
                        {{ message.received | moment('DD.MM.YYYY') }}
                    </div>
                </div>
                <div class="text-sm mt-4">
                    {{ message.subject }}
                    <span class="label label-primary" v-if="!message.read">NEU</span>
                </div>
            </div>
        </div>
        <div class="flex-1 px-4">
            <div class="flex flex-col py-2 pr-4 border-b">
                <div class="flex">
                    <div class="text-xs text-gray-500 flex-1">
                        <z icon="time" class="fill-current w-3 h-3 inline-block"></z> {{ messages[currentIndex].received | moment('dddd, DD.MM YYYY, HH:mm ') + 'Uhr' }}
                        <template v-if="messages[currentIndex].sender.includes('System')">
                            von {{ messages[currentIndex].user ? messages[currentIndex].user.name : 'System' }} an {{ messages[currentIndex].receiver.join(', ') }}
                        </template>
                        <template v-else>
                            von {{ messages[currentIndex].sender.join(', ') }}
                        </template>
                    </div>
                    <dot-menu>
                        <a :href="route('order.message_forward_form', [messages[currentIndex]])" title="Weiterleiten"><i class="fa fa-forward"></i> Weiterleiten</a>
                        <template v-if="order">
                        <a :href="route('order.message_create', {'order': order, 'answer': messages[currentIndex].id})"><i class="fa fa-reply"></i> Antworten</a>

                        <a :href="route('order.message_read', [order, messages[currentIndex]])" title="Als Gelesen markieren" v-if="!messages[currentIndex].read"><i class="fa fa-eye"></i> Gelesen</a>
                        <a :href="route('order.message_unread', [order, messages[currentIndex]])" title="Als Ungelesen markieren" v-else><i class="fa fa-eye"></i> Ungelesen</a>
                        </template>
                        <a title="In Bestellung verschieben" @click.prevent="$modal.show('assignOrderMessageModal', {message_id: messages[currentIndex].id })"><i class="fa fa-share"></i> Verschieben</a>
                        <a :href="route('order.message_delete', {'message': messages[currentIndex], 'order': order})" onclick="return confirm('Wirklich löschen?')" title="Nachricht löschen"><i class="fa fa-trash-o"></i> Löschen</a>
                    </dot-menu>
                </div>

                <h1 class="my-2 pm-2 border-b">{{ messages[currentIndex].subject }}</h1>

                <iframe seamless frameborder="0" class="w-full h-screen" :srcdoc="messages[currentIndex].htmlBody" v-if="messages[currentIndex].htmlBody != ''"></iframe>
                <div class="w-full h-screen" v-else>
                    {{ cleanTextBody(messages[currentIndex].textBody) }}
                </div>

                <div class="mt-4 border-t pt-4" v-if="messages[currentIndex].attachments.length">
                    <div class="text-xs mb-4">
                        <z icon="attachment" class="fill-current w-3 h-3 inline-block"></z> {{ messages[currentIndex].attachments.length }} {{ $tc('plural.attachment', messages[currentIndex].attachments.length) }}:
                    </div>

                    <div class="flex">
                        <a v-for="(attachment, index) in messages[currentIndex].attachments" :href="route('order.message_attachment_download', [message.id, attachment.fileName])" class="block border flex flex-col items-center p-4 mr-4 hover:bg-gray-400">
                            <z icon="document" class="fill-current w-8 h-8 mb-4"></z>
                            <div class="text-sm">{{ $attachment.orgFileName }}</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: ['messages', 'order'],

        data() {
            return {
                currentIndex: 0,
            }
        },

        methods: {
            cleanTextBody(content) {
                return this.nl2br(this.stripTags(content));
            },
            stripTags(content) {
                let regex = /(<([^>]+)>)/ig;

                return content.replace(regex, "");
            },
            nl2br (str) {
                // Some latest browsers when str is null return and unexpected null value
                if (typeof str === 'undefined' || str === null) {
                    return '';
                }

                let breakTag = '<br>';

                return (str + '').replace(/(\r\n|\n\r|\r|\n)/g, breakTag + '$1');
            },
            getSenderName(message) {
                return message.sender.includes('System') ? 'System' : 'Lieferant'
            }
        }
    }
</script>