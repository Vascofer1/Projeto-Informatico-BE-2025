<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  event: Object,
  participants: Array
})
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold">{{ event.name }} - Participantes</h1>
    <ul>
      <li v-for="participant in participants" :key="participant.id" class="p-2 border-b">
        {{ participant.name }} - {{ participant.email }} - {{ participant.phone }}
      </li>
    </ul>
  </div>
</template>
