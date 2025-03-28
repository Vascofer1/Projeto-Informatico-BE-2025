<script setup lang="ts">
import { defineProps, ref, computed } from 'vue';
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
  router.get(`/event/${props.event.id}/participants`, { search: searchQuery.value }, {
    preserveScroll: true,
    preserveState: true,
    replace: true, // evita ficar a encher o histórico do browser
  });
}
const searchQuery = ref(props.filters?.search || '');
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="container mx-auto p-4">
      <h1 class="text-3xl font-bold mb-6">{{ props.event.name }} - Participantes</h1>

      <input v-model="searchQuery" @input="onSearch" type="text" placeholder="Pesquisa participantes"
        class="w-full p-2 border rounded-lg" />
        <br><br>

      <div class="mb-4">
        <button
          @click="router.get(`/event/${props.event.id}/participants`, { status: 'por confirmar' }, { preserveScroll: true, preserveState: true })"
          class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 transition">
            View unconfirmed participants
        </button>

        <button v-if="props.filters?.status === 'por confirmar'"
          @click="router.get(`/event/${props.event.id}/participants`, {}, { preserveScroll: true, preserveState: true })"
          class="ml-4 bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition">
          🔄 View all
        </button>
      </div>
      <!-- Tabela de Participantes -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
              <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-700">Telefone</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="participant in props.participants.data" :key="participant.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm text-gray-900">{{ participant.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ participant.email }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ participant.phone }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="flex justify-center mt-6 space-x-1">
        <button v-for="link in props.participants.links" :key="link.label" :disabled="!link.url"
          @click="goToPage(link.url)" v-html="link.label"
          class="px-3 py-1 rounded text-sm font-medium border transition" :class="{
            'bg-blue-500 text-white border-blue-500': link.active,
            'text-gray-600 border-gray-300 hover:bg-gray-100': !link.active && link.url,
            'text-gray-400 border-gray-200 cursor-not-allowed': !link.url
          }" />
      </div>

      <!-- Botões de Exportação -->
      <div class="mt-8 flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
        <a :href="buildExportUrl('csv')"
          class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition text-center">
          📄 Export CSV
        </a>

        <a :href="buildExportUrl('pdf')"
          class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition text-center">
          🧾 Export PDF
        </a>
      </div>
    </div>
  </AppLayout>
</template>
