<script setup lang="ts">
import { defineProps, ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps({
  event: Object,
  participants: Array
})


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Events / ${props.event.name} / Participants`,
        href: `/events/${props.event.id}/participants`,
    },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
  <div>
    <h1 class="text-3xl font-bold">{{ event.name }} - Participantes</h1>
    <ul>
      <li v-for="participant in participants" :key="participant.id" class="p-2 border-b">
        {{ participant.name }} - {{ participant.email }} - {{ participant.phone }}
      </li>
    </ul>
  </div>
  </AppLayout>
</template>
