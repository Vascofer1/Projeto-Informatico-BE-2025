<script setup lang="ts">
import { defineProps, ref, computed } from 'vue';
import { router } from "@inertiajs/vue3";
import { Link } from '@inertiajs/vue3';


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
}

const props = defineProps<{ events: Event[] }>();

const searchQuery = ref<string>("");

// Filtrar eventos com base na pesquisa
const filteredEvents = computed(() =>
    searchQuery.value
        ? props.events.filter((event) =>
            event.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        : props.events
);

const viewEvent = (eventId: number) => {
    router.get(`/events/${eventId}`);
};

const hoveredEvent = ref<number | null>(null);
</script>

<template>
    <div class="p-6 bg-gray-100 min-h-screen">
        <!-- Top Bar -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Eventos</h1>
            <button @click="router.get('/events/create')"
                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                Criar Novo Evento
            </button>

        </div>

        <!-- Barra de Pesquisa -->
        <div class="mb-6">
            <input v-model="searchQuery" type="text" placeholder="Pesquisa eventos"
                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400" />
        </div>

        <!-- Grid de Eventos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div v-for="event in filteredEvents" :key="event.id"
                class="p-4 bg-white shadow rounded cursor-pointer transition-all duration-300"
                :class="{ 'bg-gray-200 scale-105 shadow-lg': hoveredEvent === event.id }" @click="viewEvent(event.id)"
                @mouseover="hoveredEvent = event.id" @mouseleave="hoveredEvent = null">
                <h2 class="text-lg font-semibold">{{ event.name }}</h2>
                <p class="text-gray-600">{{ event.startdate }}</p>
                <p class="text-gray-500 text-sm">{{ event.location }}</p>
                <p :class="{
                    'text-green-500': event.status === 'por decorrer',
                    'text-red-500': event.status === 'finalizado',
                    'text-blue-500': event.status === 'a decorrer'
                }">
                    {{ event.status }}
                </p>
                <button>
                    <Link :href="`/inscricao/${event.id}`"
                        class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block" @click.stop>
                    Landing Page
                    </Link>


                </button>

            </div>
        </div>

        <!-- Paginação </template>(Placeholder) -->
        <div class="flex justify-center mt-6 space-x-2">
            <button class="text-red-500">&lt;</button>
            <span>1</span>
            <span>2</span>
            <span>3</span>
            <span>...</span>
            <span>10</span>
            <button class="text-red-500">&gt;</button>
        </div>
    </div>




</template>
