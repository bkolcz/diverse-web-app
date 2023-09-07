<template>
    <table>
        <!-- <select name="by" id="by">
            <option v-for="byOption in byOptions" value={{ byOption?.k }}>{{ byOption?.v }}</option>
        </select>
        <select name="order" id="order">
            <option v-for="OrderOption in orderOptions" value={{ OrderOption?.k }}>{{ OrderOption?.v }}</option>
        </select> -->
        <tr>
            <th>UUID</th>
            <th>Created at</th>
            <th>Message</th>
        </tr>
        <tr v-for="item in messages">
            <td>{{ item?.uuid }}</td>
            <td>{{ item?.created }}</td>
            <td>{{ item?.message }}</td>
        </tr>
    </table>
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
    by: string,
    order: string,
    messages: Row[]
    byOptions: ListOption[]
    orderOptions: ListOption[]
};

export default defineComponent({
    props: {},
    data(): MessageListComponent {
        return {
            by: "name",
            order: "desc",
            messages: [],
            byOptions: [{k: "name", v: "Name"}, {k: "date", v: "Date"}],
            orderOptions: [{k: "asc", v: "Ascending"}, {k: "desc", v: "Descending"}],
        }
    },
    mounted() {
    },
    created() {
        this.getAll();
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
        }
    }
});
</script>