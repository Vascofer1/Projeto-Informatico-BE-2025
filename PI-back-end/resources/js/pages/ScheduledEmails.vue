<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors">
      <input
        type="text"
        v-model="filter"
        placeholder="Filter by event name"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 px-3 py-2 rounded mb-4 w-full max-w-md"
        @input="updateFilter"
      />

      <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
          <thead>
            <tr class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-100">
              <th class="border p-2 dark:border-gray-600">Event</th>
              <th class="border p-2 dark:border-gray-600">Messages</th>
              <th class="border p-2 dark:border-gray-600">QR</th>
              <th class="border p-2 dark:border-gray-600">Date to Send</th>
              <th class="border p-2 dark:border-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="job in jobs" :key="job.id" class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
              <td class="border p-2 dark:border-gray-600">{{ job.event_name }}</td>
              <td class="border p-2 dark:border-gray-600">{{ job.message }}</td>
              <td class="border p-2 dark:border-gray-600">{{ job.send_qr }}</td>
              <td class="border p-2 dark:border-gray-600">{{ job.send_at }}</td>
              <td class="border p-2 dark:border-gray-600">
                <button
                  @click="cancelJob(job.id)"
                  class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
                >
                  Cancel
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
    title: `Scheduled Messages`,
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