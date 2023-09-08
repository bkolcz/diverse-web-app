<template>
  <button @click="openModal($event)">Show</button>
  <Teleport to="body">
    <div v-if="isOpen" class="modal">
      <table>
        <tr>
          <th>UUID</th>
          <th>Created at</th>
          <th>Message</th>
        </tr>
        <tr>
          <td>{{ row?.uuid }}</td>
          <td>{{ row?.created }}</td>
          <td>{{ row?.message }}</td>
        </tr>
      </table>
      <button @click="closeModal($event)">Close</button>
    </div>
  </Teleport>
</template>

<style scoped>
.modal {
  position: fixed;
  z-index: 999;
  top: 20%;
  left: 50%;
  width: 300px;
  margin-left: -150px;
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
    this.getItem(this.uuid??"");
  },
  watch: {
    'isOpen': () => {},
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