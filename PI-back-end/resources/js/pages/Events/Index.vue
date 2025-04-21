<script setup lang="ts">
import { defineProps, ref, computed } from 'vue';
import { router } from "@inertiajs/vue3";
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Event {
    id: number;
    name: string;
    startdate: string;
    location: string;
    status: string;
    start_time: string;
    end_time: string;
    description: string;
    limit_participants: number;
    image: string;
    type: string;
    category: string;
    end_date: string;
    start_date: string;
    has_participants: boolean;
}

const props = defineProps<{
    events: {
        data: Event[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const searchQuery = ref<string>("");

const searchEvents = () => {
    router.get('/events', { search: searchQuery.value }, { preserveState: true });
};

const filteredEvents = computed(() =>
    searchQuery.value
        ? props.events.data.filter((event) =>
            event.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        : props.events.data
);

const viewEvent = (eventId: number) => {
    router.get(`/events/${eventId}`);
};

const hoveredEvent = ref<number | null>(null);

const confirmDelete = (eventId: number) => {
    if (confirm("Are you sure you want to delete this event? This action cannot be undone.")) {
        router.delete(`/events/${eventId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Podes mostrar um toast ou algo se quiseres
                console.log('Event successfully deleted');
            },
            onError: (errors) => {
                console.error('Error deleting the event: ', errors);
            }
        });
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Events',
        href: '/events',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
            <div class="flex justify-between items-center mb-6">
                <button @click="router.get('/events/create')"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition">
                    Create New Event
                </button>
            </div>

            <div class="mb-6">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search for an event"
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-100"
                    @input="searchEvents"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div
  v-for="event in filteredEvents"
  :key="event.id"
  class="p-4 bg-white dark:bg-gray-800 shadow rounded cursor-pointer transition-all duration-300"
  :class="{ 'bg-gray-200 dark:bg-gray-700 scale-105 shadow-lg': hoveredEvent === event.id }"
  @click="viewEvent(event.id)"
  @mouseover="hoveredEvent = event.id"
  @mouseleave="hoveredEvent = null"
>
  <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ event.name }}</h2>
  <p class="text-gray-600 dark:text-gray-300">{{ event.type }} - {{ event.category }}</p>
  <p class="text-gray-500 text-sm dark:text-gray-400">{{ event.location }}</p>

  <!-- STATUS + BOTÃO DE DELETE LADO A LADO -->
  <div class="flex items-center font-semibold justify-between mt-2">
    <p :class="{
      'text-green-500': event.status === 'Upcoming',
      'text-red-500': event.status === 'Finished',
      'text-blue-500': event.status === 'On going'
    }">
      {{ event.status }}
    </p>

    <!-- BOTÃO DE DELETE -->
    <button v-if="!event.has_participants"
      @click.stop="confirmDelete(event.id)"
      class="text-red-500 hover:text-red-700 flex items-center"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"/>
      </svg>
    </button>
  </div>
</div>
            </div>

            <div class="flex justify-center mt-6 space-x-2">
                <template v-for="(link, index) in props.events.links" :key="index">
                    <button
                        v-if="link.url"
                        @click="router.get(link.url)"
                        class="px-3 py-1 rounded transition"
                        :class="{
                            'bg-orange-600 text-white': link.active,
                            'text-gray-700 hover:bg-gray-300 dark:text-gray-300 dark:hover:bg-gray-700': !link.active
                        }"
                        v-html="link.label"
                    ></button>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
