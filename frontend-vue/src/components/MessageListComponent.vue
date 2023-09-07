<script setup lang="ts">
import MessageModalComponent from '@/components/MessageModalComponent.vue';
// TODO modal
</script>
<template>
    <table>
        <select name="by" id="by" @change="changeBy($event)">
            <option v-for="byOption in byOptions" :value=byOption?.k>{{ byOption?.v }}</option>
        </select>
        <select name="order" id="order" @change="changeOrder($event)">
            <option v-for="orderOption in orderOptions" :value=orderOption?.k>{{ orderOption?.v }}</option>
        </select>
        <tr>
            <th>UUID</th>
            <th>Created at</th>
            <th>Message</th>
        </tr>
        <tr v-for="item in messages" @click="openModal(item?.uuid)">
            <td>{{ item?.uuid }}</td>
            <td>{{ item?.created }}</td>
            <td>{{ item?.message }}</td>
        </tr>
    </table>
    <div id="modal"></div>
</template>

<script lang="ts">
import { MessageApi } from '@/apis/MessageApi';
import { defineComponent } from 'vue';
import axios from 'axios';

type Row = {
    uuid: string,
    created: string,
    message: string,
}

type ListOption = {
    k: string,
    v: string,
}

type MessageListComponent = {
    open: boolean,
    uuid: string,
    by: string,
    order: string,
    messages: Row[],
    byOptions: ListOption[],
    orderOptions: ListOption[],
};

export default defineComponent({
    props: {},
    data(): MessageListComponent {
        return {
            open: false,
            uuid: "",
            by: "name",
            order: "asc",
            messages: [],
            byOptions: [
                { k: "name", v: "Name" },
                { k: "date", v: "Date" },
            ],
            orderOptions: [
                { k: "asc", v: "Ascending" },
                { k: "desc", v: "Descending" },
            ],
        }
    },
    mounted() {
    },
    created() {
        this.getAll();
    },
    updated() {
    },
    watch: {
        'messages': () => {},
        'open': () => {},
        'uuid': () => {},
    },
    methods: {
        async getAll(): Promise<void> {
            const response = (await axios.get(MessageApi.message, { params: { by: this.by, order: this.order } })).data;
            const messages: Row[] = [];
            for (const messageKey of Object.keys(response)) {
                const keyRegex = MessageApi.messageKeyRegExp.exec(messageKey);
                const timeRegex = MessageApi.dateRegExp.exec(keyRegex?.groups?.time ?? "")
                messages.push({
                    uuid: keyRegex?.groups?.uuid ?? "",
                    created: `${timeRegex?.groups?.year}-${timeRegex?.groups?.month}-${timeRegex?.groups?.day} ${timeRegex?.groups?.hour}:${timeRegex?.groups?.min}:${timeRegex?.groups?.sec}.${timeRegex?.groups?.milis}`,
                    message: response[messageKey]?.message ?? ""
                })
            }
            this.messages = messages;
        },

        changeBy(event: Event) {
            this.by = (event.target as HTMLDataElement).value??"name";
            this.getAll();
        },

        changeOrder(event: Event) {
            this.order = (event.target as HTMLDataElement).value??"asc";
            this.getAll();
        },
        openModal(uuid: string) {
            console.log(uuid);
            this.uuid = uuid;
            // TODO popup modal
        },
    }
});
</script>