<script setup lang="ts">
import MessageModalComponent from '@/components/MessageModalComponent.vue';
</script>
<template>
    <div class="container">
        <select name="by" id="by" @change="changeBy($event)" class="form-select m-3">
            <option v-for="byOption in byOptions" :value=byOption?.k>{{ byOption?.v }}</option>
        </select>
        <select name="order" id="order" @change="changeOrder($event)" class="form-select m-3">
            <option v-for="orderOption in orderOptions" :value=orderOption?.k>{{ orderOption?.v }}</option>
        </select>
    </div>
    <div class="container">
        <table class="table">
        <thead>
            <tr>
                <th scope="col">UUID</th>
                <th scope="col">Created at</th>
                <th scope="col">Message</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in messages">
                <td scope="row">{{ item?.uuid }}</td>
                <td>{{ item?.created }}</td>
                <td>{{ item?.message }}</td>
                <td>
                    <MessageModalComponent :uuid="item?.uuid" />
                </td>
            </tr>
        </tbody>
    </table>
    </div>
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
        'messages': () => { },
        'open': () => { },
        'uuid': () => { },
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
            this.by = (event.target as HTMLDataElement).value ?? "name";
            this.getAll();
        },

        changeOrder(event: Event) {
            this.order = (event.target as HTMLDataElement).value ?? "asc";
            this.getAll();
        },
    }
});
</script>