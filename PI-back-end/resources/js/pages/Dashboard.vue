<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const props = defineProps({
  recentEvents: Array,
  ongoingEvents: Array,
  upcomingEvents: Array,
});

console.log("Recent Events:", props.recentEvents);
console.log("Ongoing Events:", props.ongoingEvents);
console.log("Upcoming Events:", props.upcomingEvents);
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 bg-gray-100 min-h-screen">
      <h1 class="text-3xl font-bold mb-6 text-center">Welcome Back, Admin</h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Recent Events -->
        <div class="p-6 bg-white rounded-lg shadow-lg text-center">
          <h2 class="text-xl font-semibold mb-4 text-blue-600">Recent Events</h2>
          <p v-if="!props.recentEvents || props.recentEvents.length === 0" class="text-gray-500">No events available</p>
          <ul v-else class="divide-y divide-gray-300">
            <li v-for="event in props.recentEvents" :key="event.id" class="py-2">
              <span class="block font-medium">{{ event.name }}</span>
              <span class="text-sm text-gray-600">{{ event.start_date }} {{ event.start_time }}</span>
            </li>
          </ul>
        </div>

        <!-- Ongoing Events -->
        <div class="p-6 bg-white rounded-lg shadow-lg text-center">
          <h2 class="text-xl font-semibold mb-4 text-green-600">Ongoing Events</h2>
          <p v-if="!props.ongoingEvents || props.ongoingEvents.length === 0" class="text-gray-500">No events available</p>
          <ul v-else class="divide-y divide-gray-300">
            <li v-for="event in props.ongoingEvents" :key="event.id" class="py-2">
              <span class="block font-medium">{{ event.name }}</span>
              <span class="text-sm text-gray-600">{{ event.start_date }} {{ event.start_time }}</span>
            </li>
          </ul>
        </div>

        <!-- Upcoming Events -->
        <div class="p-6 bg-white rounded-lg shadow-lg text-center">
          <h2 class="text-xl font-semibold mb-4 text-purple-600">Upcoming Events</h2>
          <p v-if="!props.upcomingEvents || props.upcomingEvents.length === 0" class="text-gray-500">No events available</p>
          <ul v-else class="divide-y divide-gray-300">
            <li v-for="event in props.upcomingEvents" :key="event.id" class="py-2">
              <span class="block font-medium">{{ event.name }}</span>
              <span class="text-sm text-gray-600">{{ event.start_date }} {{ event.start_time }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>