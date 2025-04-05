<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

// Criar um formulário reativo com Inertia
const props = defineProps({
    event: Object
});

const form = useForm({
    event_id: props.event.id,
    date: '',
    time: '',
    type: '',
    message: '',
    start_time: '',
    send_qr: false
});


const sendMessage = () => {
    if (!form.date || !form.time || !form.message) {
        alert('Por favor, preencha todos os campos antes de enviar.');
        return;
    }

    // Combinar data e hora corretamente para o Laravel
    const sendDateTime = `${form.date} ${form.time}:00`;

    form.start_time = sendDateTime;

    form.post('/schedule-email', {
        preserveScroll: true,
        onSuccess: () => {
            alert('E-mail agendado com sucesso!');
            form.reset();
        }
    });


};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Messages`,
        href: `/messages/create`,
    },
];



</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-2xl mx-auto bg-white p-6 shadow-lg rounded-lg w-full max-w-5xl">
                <h1 class="text-2xl font-bold mb-4">Nova Mensagem</h1>

                

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <!-- Data e Hora -->
                    <div>
                        <label class="block text-gray-700 font-bold">Data a enviar:</label>
                        <input v-model="form.date" type="date" class="w-full p-2 border rounded mt-2" />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold">Hora do envio:</label>
                        <input v-model="form.time" type="time" class="w-full p-2 border rounded mt-2" />
                    </div>
                </div>

                <!-- Tipo de mensagem -->
                <label class="block text-gray-700 mt-4 font-bold">Tipo de mensagem:</label>
                <select v-model="form.type" class="w-full p-2 border rounded mt-2">
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>

                <label class="flex items-center mt-4">
                    <input type="checkbox" v-model="form.send_qr" class="mr-2 font-bold">
                    Incluir QR Code no email
                </label>

                <!-- Mensagem personalizada -->
                <label class="block text-gray-700 mt-4 font-bold">Mensagem personalizada:</label>
                <textarea v-model="form.message" class="w-full p-2 border rounded mt-2" rows="4"></textarea>

                <!-- Botão de enviar -->
                <button @click="sendMessage" class="w-full bg-blue-500 text-white py-2 rounded mt-4 hover:bg-blue-600 font-bold">
                    Enviar Mensagem
                </button>
            </div>
        </div>
    </AppLayout>
</template>
