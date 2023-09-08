<script setup lang="ts">
</script>

<template>
  <form v-on:submit.prevent="addMessage">
    <label for="message-content">Message</label>
    <textarea name="message-content" id="message-content" cols="30" rows="10" required v-model="message"></textarea>
    <button type="submit">Add message</button>
  </form>
</template>

<script lang="ts">
import { MessageApi } from '@/apis/MessageApi';
import { defineComponent } from 'vue';
import axios from 'axios';

type MessageAddModel = {
  message: string,
}

export default defineComponent({
  data(): MessageAddModel {
    return {
      message: ""
    }
  },
  methods: {
    async addMessage() {
      const response = (await axios.post(MessageApi.message, { message: this.message })).data;
      if (response.status != 'failed') {
        alert(`Your message id is: ${response.status}`);
      } else {
        alert(`Message couldn't be saved`);
      }
    },
  }
})
</script>