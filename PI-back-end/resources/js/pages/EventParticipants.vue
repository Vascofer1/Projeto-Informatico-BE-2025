<script setup lang="ts">
import { defineProps, ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router } from "@inertiajs/vue3";

const props = defineProps({
  event: Object,
  participants: Array,
  filters: Object
})

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / ${props.event.name} / Participants`,
    href: `/events/${props.event.id}/participants`,
  },
];

function goToPage(url) {
  if (url) {
    router.visit(url, {
      preserveScroll: true,
      preserveState: true,
    })
  }
}

function buildExportUrl(format: 'csv' | 'pdf') {
  const base = `/eventos/${props.event.id}/participantes/export-${format}`;
  const params = new URLSearchParams(props.filters).toString();
  return params ? `${base}?${params}` : base;
}

function onSearch() {
  router.get(`/event/${props.event?.id}/participants`, { 
    search: searchQuery.value || '' // Garante que será string vazia se for null/undefined
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  });
}
const searchQuery = ref(props.filters?.search || '');

const selectedStatus = ref(props.filters?.status || '');

function onStatusChange() {
  const params: any = {
    search: searchQuery.value,
  };
  if (selectedStatus.value) {
    params.status = selectedStatus.value;
  }

  router.get(`/event/${props.event.id}/participants`, params, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto p-4 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <input 
        v-model="searchQuery" 
        @input="onSearch" 
        type="text" 
        placeholder="Search participants..."
        class="w-full p-2 border rounded-lg bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-800 dark:text-white"
      />
      <br><br>

      <div class="mb-4">
        <label for="status" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Filter by Status
        </label>
        <select 
          id="status" 
          v-model="selectedStatus" 
          @change="onStatusChange" 
          class="p-2 border rounded w-full sm:w-64 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-800 dark:text-white">
          <option value="">All</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Unconfirmed">Unconfirmed</option>
        </select>
      </div>

      <!-- Tabela de Participantes -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                Email
              </th>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                Cell phone
              </th>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr 
              v-for="participant in props.participants.data" 
              :key="participant.id" 
              class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                {{ participant.name }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                {{ participant.email }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                {{ participant.phone }}
              </td>
              <td 
                class="px-6 py-4 text-sm"
                :class="{
                  'text-green-600 dark:text-green-400': participant.status === 'Confirmed',
                  'text-red-600 dark:text-red-400': participant.status === 'Unconfirmed', 
                  'text-gray-600 dark:text-gray-300': participant.status !== 'Confirmed' && participant.status !== 'Unconfirmed'
                }">
                {{ participant.status }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="flex justify-center mt-6 space-x-2">
  <template v-for="(link, index) in props.participants.links" :key="index">
    <button
      v-if="link.url"
      @click="goToPage(link.url)"
      class="px-3 py-1 rounded transition"
      :class="{
        'bg-orange-600 dark:bg-blue-600 text-white': link.active,
        'text-gray-700 hover:bg-gray-300 dark:text-gray-300 dark:hover:bg-gray-700': !link.active
      }"
      v-html="link.label"
    ></button>
  </template>
</div>

      <!-- Botões de Exportação -->
      <div class="mt-8 flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
        <a 
          :href="buildExportUrl('csv')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition text-center">
          📄 Export CSV
        </a>
        <a 
          :href="buildExportUrl('pdf')"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition text-center">
          🧾 Export PDF
        </a>
      </div>
    </div>
  </AppLayout>
</template>
