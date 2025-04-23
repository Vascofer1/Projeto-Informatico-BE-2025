<script setup lang="ts">
import { defineProps, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { CheckCircle, Clock, MapPin, FileText, ClipboardList, PlusCircle, Users, Mail, Pencil, Tag } from 'lucide-vue-next';

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

const props = defineProps<{ event: Event; formExists: boolean; }>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / ${props.event.name}`,
    href: `/events/${props.event.id}`,
  },
];

// Define cores do status dinamicamente
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
console.log(props.event)
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

          <Link v-if="event.participants_count > 0" :href="`/messages/create/${event.id}`"
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
      </div>
    </div>
  </AppLayout>
</template>
