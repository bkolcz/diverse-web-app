<template>
  <button @click="openModal($event)" class="btn btn-info">Show</button>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-message">
      <table class="table">
        <tr>
          <th>UUID</th>
          <td>{{ row?.uuid }}</td>
        </tr>
        <tr>
          <th>Created at</th>
          <td>{{ row?.created }}</td>
        </tr>
        <tr>
          <th>Message</th>
          <td>{{ row?.message }}</td>
        </tr>
      </table>
      <button @click="closeModal($event)" class="btn btn-primary">Close</button>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-message {
  position: fixed;
  z-index: 999;
  top: 20vh;
  left: 45%;
  width: 50vh;
  margin-left: -150px;
  background-color: white;
  border: 1px solid black;
  border-radius: 5px;
  padding: 1em;
}
</style>
<script lang="ts">
import { MessageApi } from '@/apis/MessageApi';
import { defineComponent } from 'vue';
import axios from 'axios';

type Row = {
  uuid: string,
  created: string,
  message: string,
}

type MessageModal = {
  isOpen: boolean,
  row: Row
}

export default defineComponent({
  props: {
    uuid: String,
  },
  data(): MessageModal {
    return {
      isOpen: false,
      row: {
        uuid: "",
        created: "",
        message: "",
      }
    }
  },
  created() {
    this.getItem(this.uuid ?? "");
  },
  watch: {
    'isOpen': () => { },
  },
  methods: {
    openModal(event: Event) {
      this.isOpen = true;
    },
    closeModal(event: Event) {
      this.isOpen = false;
    },
    async getItem(uuid: string): Promise<void> {
      const response = (await axios.get(`${MessageApi.message}/${uuid}`)).data;
      for (const messageKey of Object.keys(response)) {
        const keyRegex = MessageApi.messageKeyRegExp.exec(messageKey);
        const timeRegex = MessageApi.dateRegExp.exec(keyRegex?.groups?.time ?? "")
        this.row = {
          uuid: keyRegex?.groups?.uuid ?? "",
          created: `${timeRegex?.groups?.year}-${timeRegex?.groups?.month}-${timeRegex?.groups?.day} ${timeRegex?.groups?.hour}:${timeRegex?.groups?.min}:${timeRegex?.groups?.sec}.${timeRegex?.groups?.milis}`,
          message: response[messageKey]?.message ?? ""
        };
        break;
      }
    }
  }
});
</script>