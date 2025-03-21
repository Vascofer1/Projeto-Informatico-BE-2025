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
        <h1 class="text-3xl font-bold mb-4">{{ event.name }}</h1>
        <p><strong>Tipo:</strong> {{ event.type }}</p>
        <p><strong>Categoria:</strong> {{ event.category }}</p>
        <p><strong>Localização:</strong> {{ event.location }}</p>
        <p><strong>Data e Hora:</strong> {{ event.start_date }} às {{ event.start_time }} - {{ event.end_date }} às {{
            event.end_time }}</p>
        <p><strong>Descrição:</strong> {{ event.description }}</p>

        <br>
        <button>
            <Link :href="`/event/${event.id}/participants`" class="bg-blue-500 text-white px-4 py-2 rounded">
            Ver Participantes
            </Link>
        </button>
        <br>

        <div class="flex justify-start mt-4">
            <button @click="router.get('/events')" class="px-4 py-2 bg-gray-500 text-white rounded">
                Voltar
            </button>
        </div>
    </div>
    </AppLayout>


</template>
