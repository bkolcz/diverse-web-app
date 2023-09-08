<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <header>
    <div class="wrapper">
      <nav class="navbar navbar-expand-md navbar-light bg-light">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <RouterLink to="/" class="nav-link">Home</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink to="/message-add" class="nav-link">Add Message</RouterLink>
          </li>
        </ul>
      </nav>
    </div>
  </header>
  <RouterView />
</template>
