<template>
  <AppLayout :breadcrumbs="breadcrumbs">
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Scheduled messages</h1>

    <input
      type="text"
      v-model="filter"
      placeholder="Filter by event name"
      class="border px-2 py-1 rounded mb-4"
      @input="updateFilter"
    />

    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2">Event</th>
          <th class="border p-2">Messages</th>
          <th class="border p-2">QR</th>
          <th class="border p-2">Date to send</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="job in jobs" :key="job.id">
          <td class="border p-2">{{ job.event_name }}</td>
          <td class="border p-2">{{ job.message }}</td>
          <td class="border p-2">{{ job.send_qr }}</td>
          <td class="border p-2">{{ job.send_at }}</td>
          <td class="border p-2">
            <button @click="cancelJob(job.id)" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</AppLayout>
</template>




<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `/Scheduled-Messages`,
    href: `/messages/create`,
  },
];

//adicionar 1 hora a data de envio
const addOneHour = (date) => {
  const newDate = new Date(date)
  newDate.setHours(newDate.getHours() + 1)
  return newDate
}

const props = defineProps({
  jobs: Object,
  filters: Object
})

const filter = ref(props.filter || '')

const updateFilter = () => {
  router.get(route('scheduled-emails.index'), { event: filter.value }, { preserveState: true })
}

function cancelJob(id) {
  if (confirm('Tens a certeza que queres cancelar esta mensagem agendada?')) {
    router.delete(route('scheduled-emails.destroy', id))
  }
}

function goToPage(page) {
  router.get(route('scheduled-emails.index'), { ...filters.value, page })
}
</script>