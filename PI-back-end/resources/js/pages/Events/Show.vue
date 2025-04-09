<script setup lang="ts">
import { defineProps, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

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
    image?: string;
    participants_count?: number;
    confirmed_count?: number;
    waiting_count?: number;
}

const props = defineProps<{ event: Event }>();

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
        <div class="p-6 bg-gray-100 min-h-screen">

            <div class="bg-white rounded-xl shadow-md p-6 md:flex md:space-x-8">
                <!-- Imagem -->

                <img v-if="event.image" :src="`/storage/${event.image}`" alt="Event Image"
                    class="w-full md:w-64 h-64 object-cover rounded-lg mb-6 md:mb-0" />

                <!-- Info -->
                <div class="flex-1 space-y-6">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <h1 class="text-5xl font-bold text-gray-800">{{ event.name }}</h1>
                        <span class="px-4 py-1 text-base font-semibold text-white rounded-full" :class="statusColors">
                            {{ event.status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-800 text-base">
                        <p><span class="font-semibold">📍 Location:</span> {{ event.location }}</p>
                        <p><span class="font-semibold">📁 Type:</span> {{ event.type }}</p>
                        <p><span class="font-semibold">🏷️ Category:</span> {{ event.category }}</p>
                        <p>
                            <span class="font-semibold">🕓 Start:</span> {{ formattedStartDate }} às
                            {{ formattedTime }}
                        </p>
                        <p>
                            <span class="font-semibold">🕛 End:</span> {{ formattedEndDate }} às
                            {{ formattedEndTime }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-800 font-semibold text-lg">📝 Description:</p>
                        <p class="text-gray-700 italic">
                            {{ event.description ? event.description : 'No description available.' }}
                        </p>
                    </div>

                    <!-- Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                        <div
                            class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl flex items-center space-x-4 shadow-sm">
                            <span class="text-4xl">✅</span>
                            <div>
                                <p class="text-gray-700 text-base">Confirmed Participants</p>
                                <p class="text-3xl font-bold text-green-600">
                                    {{ event.confirmed_count || 0 }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-xl flex items-center space-x-4 shadow-sm">
                            <span class="text-4xl">⏳</span>
                            <div>
                                <p class="text-gray-700 text-base">Waiting Participants</p>
                                <p class="text-3xl font-bold text-yellow-600">
                                    {{ event.waiting_count || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="pt-6 flex flex-wrap gap-4">
                        <Link :href="`/event/${event.id}/participants`"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-base shadow transition">
                        👥 Show {{ event.participants_count }} Participants
                        </Link>
                        <button v-if="event.status === 'Upcoming'" @click="router.get(`/events/${event.id}/edit`)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg text-base shadow transition">
                            ✏️ Edit Event
                        </button>
                        <Link :href="`/messages/create/${props.event.id}`"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-base shadow transition">
                        📩 Schedule Message
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>