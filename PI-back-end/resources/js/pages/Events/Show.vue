<script setup lang="ts">
import { defineProps, computed, onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { CheckCircle, Clock, MapPin, FileText, ClipboardList, PlusCircle, Users, Mail, Pencil, Tag } from 'lucide-vue-next';
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend, ArcElement, PieController } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend, ArcElement, PieController);

interface ChartDataItem {
  question: string;
  labels: string[];
  data: number[];
}

interface Event {
  id: number;
  name: string;
  type: string;
  category: string;
  location: string;
  start_date: string;
  start_time: string;
  end_date: string;
  end_time: string;
  description: string;
  status: string;
  limit_participants: number;
  image?: string;
  participants_count?: number;
  confirmed_count?: number;
  waiting_count?: number;
}

const props = defineProps<{ event: Event; formExists: boolean; chartData: ChartDataItem[] }>();

const shownCharts = ref<boolean[]>(props.chartData.map(() => true));

const charts: Ref<(Chart | null)[]> = ref([]);

const createOrUpdateChart = (chartItem: ChartDataItem, index: number) => {
  const ctx = document.getElementById(`chart-${index}`) as HTMLCanvasElement;
  if (!ctx) return;

  const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const tickColor = isDark ? '#ffffff' : '#6b7280';
  const gridColor = isDark ? '#374151' : '#e5e7eb';

  if (charts.value[index]) {
    charts.value[index]?.destroy();
  }

  charts.value[index] = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartItem.labels,
      datasets: [{
        label: 'Frequency',
        data: chartItem.data,
        backgroundColor: chartItem.data.map((value) => {
          if (value === 0) return '#ef4444';
          if (value <= 2) return '#eab308';
          if (value <= 5) return '#22c55e';
          return '#06b6d4';
        }),
        borderRadius: 6,
        barThickness: 40,
      }]
    },
    options: {
      responsive: true,
      layout: { padding: 20 },
      plugins: {
        legend: { display: false },
        title: { display: false },
        tooltip: {
          backgroundColor: '#111827',
          titleColor: '#fff',
          bodyColor: '#d1d5db',
          cornerRadius: 4
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, color: tickColor, font: { size: 14 } },
          grid: { color: gridColor }
        },
        x: {
          ticks: { color: tickColor, font: { size: 14 } },
          grid: { display: false }
        }
      }
    }
  });
};

onMounted(() => {
  props.chartData.forEach((chart, index) => {
    if (shownCharts.value[index]) {
      createOrUpdateChart(chart, index);
    }
  });
});

watch(shownCharts, (newValues, oldValues) => {
  newValues.forEach((show, index) => {
    if (show && !oldValues[index]) {
      nextTick(() => createOrUpdateChart(props.chartData[index], index));
    } else if (!show && charts.value[index]) {
      charts.value[index]?.destroy();
      charts.value[index] = null;
    }
  });
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / ${props.event.name}`,
    href: `/events/${props.event.id}`,
  },
];

const statusColors = computed(() => {
  switch (props.event.status) {
    case "Upcoming": return "bg-green-500";
    case "On going": return "bg-blue-500";
    case "Finished": return "bg-red-500";
    default: return "bg-gray-400";
  }
});

const formatDate = (date: string | undefined): string => {
  if (!date) return '';
  const [year, month, day] = date.split('-');
  return `${day}-${month}-${year}`;
};

const formatTime = (time: string | undefined): string => {
  if (!time) return '';
  const [hour, minute] = time.split(':');
  return `${hour}:${minute}`;
};

const formattedTime = formatTime(props.event?.start_time);
const formattedEndTime = formatTime(props.event?.end_time);
const formattedStartDate = formatDate(props.event?.start_date);
const formattedEndDate = formatDate(props.event?.end_date);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen space-y-8">
      <!-- Header Section -->
      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Event Info Card -->
        <div
  class="relative bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 flex-1 flex gap-6"
  :class="{ 'flex-col': !event.image, 'flex-row': event.image }"
>
  <!-- Header (Event Name inside the card) -->
  <div class="absolute top-4 left-6 right-6 z-10">
    <h1 class="text-4xl font-bold text-gray-800 dark:text-white drop-shadow-md">{{ event.name }}</h1>
  </div>

          <!-- Imagem -->
          <div
    v-if="event.image"
    class="w-full md:w-48 h-auto aspect-[2/3] rounded-xl overflow-hidden shadow-lg mt-14"
  >
    <img
      :src="`/storage/${event.image}`"
      alt="Event Image"
      class="w-full h-full object-cover"
    />
  </div>

          <!-- Info -->
          <div class="flex-1 space-y-6 text-gray-700 dark:text-gray-300">
            <div class="flex justify-end items-start">
              <span
                class="px-4 py-1 text-base font-semibold text-white rounded-full"
                :class="statusColors"
              >
                {{ event.status === 'Upcoming' ? 'Upcoming' : event.status }}
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
              <p><span class="font-semibold text-gray-900 dark:text-white">
                <MapPin class="inline-block w-5 h-5" /> Location:</span> {{ event.location }}</p>
              <p><span class="font-semibold text-gray-900 dark:text-white">
                <Clock class="inline-block w-5 h-5" /> Start:</span> {{ formattedStartDate }} {{ formattedTime }}</p>
              <p><span class="font-semibold text-gray-900 dark:text-white">
                <Tag class="inline-block w-5 h-5" /> Type:</span> {{ event.type }}</p>
              <p><span class="font-semibold text-gray-900 dark:text-white">
                <Clock class="inline-block w-5 h-5" /> End:</span> {{ formattedEndDate }} {{ formattedEndTime }}</p>
              <p><span class="font-semibold text-gray-900 dark:text-white">
                <ClipboardList class="inline-block w-5 h-5" /> Category:</span> {{ event.category }}</p>
            </div>
            <br>
            <p><span class="font-semibold text-gray-900 dark:text-white">
              <Users class="inline-block w-5 h-5" /> Limit Participants:</span> {{ event.limit_participants }}</p>
            <p class="pr-16 text-justify"><span class="font-semibold text-gray-900 dark:text-white">
              <FileText class="inline-block w-5 h-5" /> Description:</span> {{ event.description || 'No description available.' }}</p>
          </div>

          <!-- Botão Editar -->
          <button
            v-if="props.event.status === 'Upcoming'"
            @click="router.get(`/events/${props.event.id}/edit`)"
            class="absolute bottom-4 right-4 bg-yellow-500 hover:bg-yellow-600 text-white p-3 rounded-full shadow-lg transition"
            title="Edit Event"
          >
            <Pencil class="w-5 h-5" />
          </button>
        </div>

        <!-- Action Buttons Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 flex flex-col gap-4 w-full lg:w-80">
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">Actions</h2>

          <p class="font-bold text-gray-800 dark:text-gray-200">Forms</p>
          <Link :href="`/inscricao/${event.id}`"
            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center flex items-center justify-center gap-2">
            <FileText class="w-5 h-5" /> View Registration Form
          </Link>

          <Link :href="`/events/${event.id}/form`"
            v-if="formExists"
            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center flex items-center justify-center gap-2">
            <ClipboardList class="w-5 h-5" /> View Event Form
          </Link>

          <Link v-if="!formExists"
            :href="`/events/${event.id}/questions`"
            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center flex items-center justify-center gap-2">
            <PlusCircle class="w-5 h-5" /> Create Event Form
          </Link>

          <p class="font-bold text-gray-800 dark:text-gray-200">Participants</p>
          <Link :href="`/event/${event.id}/participants`"
            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center flex items-center justify-center gap-2">
            <Users class="w-5 h-5" /> View Participants
          </Link>

          <Link :href="`/messages/create/${event.id}`"
            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center flex items-center justify-center gap-2">
            <Mail class="w-5 h-5" /> Schedule Message
          </Link>
        </div>
      </div>

      <!-- Stats Section -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 space-y-4">
  <h2 class="text-xl font-bold text-gray-800 dark:text-white">Statistics</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 rounded-xl flex items-center space-x-4 shadow-sm">
      <CheckCircle class="w-8 h-8 text-green-600" />
      <div>
        <p class="text-gray-700 dark:text-gray-200 text-base">Confirmed Participants</p>
        <p class="text-3xl font-bold text-green-600">{{ event.confirmed_count || 0 }}</p>
      </div>
    </div>
    <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-500 p-4 rounded-xl flex items-center space-x-4 shadow-sm">
      <Clock class="w-8 h-8 text-yellow-600" />
      <div>
        <p class="text-gray-700 dark:text-gray-200 text-base">Waiting Participants</p>
        <p class="text-3xl font-bold text-yellow-600">{{ event.waiting_count || 0 }}</p>
      </div>
    </div>
  </div>
  <br>
  <div v-if="chartData.length > 0" class="mt-10 space-y-6">
    <div class="flex flex-wrap gap-4 mb-4">
      <label
        v-for="(chart, index) in chartData"
        :key="`checkbox-${index}`"
        class="inline-flex items-center space-x-2 text-sm text-gray-800 dark:text-gray-200"
      >
        <input
          type="checkbox"
          v-model="shownCharts[index]"
          class="form-checkbox h-4 w-4 text-blue-600"
        />
        <span>{{ chart.question }}</span>
      </label>
    </div>

    <div v-if="chartData.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
          <div v-for="(chart, index) in chartData" :key="index" class="bg-gray-100 dark:bg-gray-700 p-6 rounded-xl shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ chart.question }}</h3>
    <canvas :id="`chart-${index}`" class="w-full max-w-xl mx-auto"></canvas>
  </div>
</div>
  </div>
  <div v-else class="text-gray-600 dark:text-gray-300">
    No statistics available for this event.
  </div>
</div>
</div>

</AppLayout>
</template>