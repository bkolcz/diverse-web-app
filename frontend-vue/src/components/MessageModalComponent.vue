<template>
  <Teleport to="body">
    <div v-if="openModal" class="modal">
      <p>{{ row?.uuid }}</p>
      <p>{{ row?.created }}</p>
      <p>{{ row?.message }}</p>
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
  openModal: boolean,
  row: Row
}

export default defineComponent({
  props: {
    uuid: String,
  },
  data(): MessageModal {
    return {
      openModal: false,
      row: {
        uuid: "",
        created: "",
        message: "",
      }
    }
  },
  created() {
    this.getItem(this.uuid??"");
    this.openModal = true;
  },
  watch: {
    'openModal': () => {},
  },
  methods: {
    closeModal(event: Event) {
      this.openModal = false;
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