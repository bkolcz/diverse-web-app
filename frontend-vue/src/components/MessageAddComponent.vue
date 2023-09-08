<script setup lang="ts">
</script>

<template>
  <div class="container">
    <form v-on:submit.prevent="addMessage">
    <div class="form-group">
      <label for="message-content" class="col-form-label">Message</label>
      <textarea name="message-content" id="message-content" cols="30" rows="10" class="form-control" required v-model="message"></textarea>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Add message</button>
  </form>
  </div>
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