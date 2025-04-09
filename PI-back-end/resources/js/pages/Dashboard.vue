<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import type { User } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const props = defineProps({
  user: Object as () => User,
  recentEvents: Array,
  ongoingEvents: Array,
  upcomingEvents: Array,
});

function formatDate(dateStr: string): string {
  const [year, month, day] = dateStr.split('-');
  return `${day}-${month}-${year}`;
}

function formatEventDate(event: any) {
  const formatTime = (timeStr: string) => timeStr.slice(0, 5);
  const sameDay = event.start_date === event.end_date;
  if (sameDay) {
    return {
      dateLine: formatDate(event.start_date),
      timeLine: `${formatTime(event.start_time)} - ${formatTime(event.end_time)}`,
    };
  }
  return {
    dateLine: `${formatDate(event.start_date)} ${formatTime(event.start_time)}`,
    timeLine: `${formatDate(event.end_date)} ${formatTime(event.end_time)}`,
  };
}
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <h1 class="text-3xl font-bold mb-6 text-center text-gray-900 dark:text-white">
        Welcome Back, {{ props.user.name }}
      </h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Recent Events -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg text-center">
          <h2 class="text-xl font-semibold mb-4 text-blue-600 dark:text-blue-400">
            Recent Events
          </h2>
          <p
            v-if="!props.recentEvents || props.recentEvents.length === 0"
            class="text-gray-500 dark:text-gray-300"
          >
            No events available
          </p>
          <ul v-else class="divide-y divide-gray-300 dark:divide-gray-700">
            <template v-for="event in props.recentEvents" :key="event.id">
              <li class="py-2">
                <Link :href="`/events/${event.id}`" class="block text-lg font-bold text-gray-900 dark:text-white">
                  {{ event.name }}
                </Link>
                <div class="mt-1">
                  <div class="text-sm text-gray-700 dark:text-gray-300">
                    {{ formatEventDate(event).dateLine }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ formatEventDate(event).timeLine }}
                  </div>
                </div>
              </li>
            </template>
          </ul>
        </div>

        <!-- Ongoing Events -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg text-center">
          <h2 class="text-xl font-semibold mb-4 text-green-600 dark:text-green-400">
            Ongoing Events
          </h2>
          <p
            v-if="!props.ongoingEvents || props.ongoingEvents.length === 0"
            class="text-gray-500 dark:text-gray-300"
          >
            No events available
          </p>
          <ul v-else class="divide-y divide-gray-300 dark:divide-gray-700">
            <template v-for="event in props.ongoingEvents" :key="event.id">
              <li class="py-2">
                <Link :href="`/events/${event.id}`" class="block text-lg font-bold text-gray-900 dark:text-white">
                  {{ event.name }}
                </Link>
                <div class="mt-1">
                  <div class="text-sm text-gray-700 dark:text-gray-300">
                    {{ formatEventDate(event).dateLine }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ formatEventDate(event).timeLine }}
                  </div>
                </div>
              </li>
            </template>
          </ul>
        </div>

        <!-- Upcoming Events -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg text-center">
          <h2 class="text-xl font-semibold mb-4 text-purple-600 dark:text-purple-400">
            Upcoming Events
          </h2>
          <p
            v-if="!props.upcomingEvents || props.upcomingEvents.length === 0"
            class="text-gray-500 dark:text-gray-300"
          >
            No events available
          </p>
          <ul v-else class="divide-y divide-gray-300 dark:divide-gray-700">
            <template v-for="event in props.upcomingEvents" :key="event.id">
              <li class="py-2">
                <Link :href="`/events/${event.id}`" class="block text-lg font-bold text-gray-900 dark:text-white">
                  {{ event.name }}
                </Link>
                <div class="mt-1">
                  <div class="text-sm text-gray-700 dark:text-gray-300">
                    {{ formatEventDate(event).dateLine }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ formatEventDate(event).timeLine }}
                  </div>
                </div>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
