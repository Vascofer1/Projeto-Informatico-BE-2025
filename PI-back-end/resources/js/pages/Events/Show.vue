<script setup lang="ts">
import { defineProps } from "vue";
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
}

const props = defineProps<{ event: Event }>();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Events / ${props.event.name}`,
        href: `/events/${props.event.id}`,
    },
];

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="flex flex-col md:flex-row items-start space-x-6">
                <!-- Imagem ao lado do conteúdo -->
                <img v-if="event.image" :src="`/storage/${event.image}`" alt="Event Image"
                    class="w-64 h-64 object-cover rounded-lg shadow-lg">

                <!-- Conteúdo do evento -->
                <div>
                    <h1 class="text-3xl font-bold mb-4">{{ event.name }}</h1>
                    <p><strong>Type:</strong> {{ event.type }}</p>
                    <p><strong>Category:</strong> {{ event.category }}</p>
                    <p><strong>Location:</strong> {{ event.location }}</p>
                    <p><strong>Date:</strong> {{ event.start_date }} às {{ event.start_time }} - {{ event.end_date }} às {{ event.end_time }}</p>
                    <p><strong>Description:</strong> {{ event.description ? event.description : 'No description available.' }}</p>

                    <div class="mt-6 flex space-x-4">
                        <Link :href="`/event/${event.id}/participants`" class="bg-blue-500 text-white px-6 py-3 rounded-lg text-lg shadow-md hover:bg-blue-600 transition">
                            Show {{ event.participants_count }} Participants
                        </Link>

                        <button @click="router.get('/events')" class="px-6 py-3 bg-gray-500 text-white rounded-lg text-lg shadow-md hover:bg-gray-600 transition">
                            Voltar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>