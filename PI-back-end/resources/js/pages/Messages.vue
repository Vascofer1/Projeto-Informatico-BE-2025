<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps({
    event: Object
});

const form = useForm({
    event_id: '',
    date: '',
    time: '',
    type: '',
    message: '',
    start_time: '',
    send_qr: false
});

const textareaRef = ref<HTMLTextAreaElement | null>(null);

const insertAtCursor = (text: string) => {
    const textarea = textareaRef.value;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const current = form.message;

    form.message = current.substring(0, start) + text + current.substring(end);

    nextTick(() => {
        textarea.setSelectionRange(start + text.length, start + text.length);
        textarea.focus();
    });
};

const dynamicFields = [
    { label: 'Participant name', value: '{{ name }}' },
    { label: 'Email', value: '{{ email }}' },
    { label: 'Event', value: '{{ event }}' },
    { label: 'Date', value: '{{ date }}' },
    { label: 'Time', value: '{{ hour }}' },
    { label: 'Location', value: '{{ location }}' },
    { label: 'Form link', value: '{{ Formlink }}' },
];

const sendMessage = () => {
  // Limpa erros anteriores
  form.clearErrors();

  // Validação frontend
  const errors: Record<string, string> = {};

  if (!form.date) errors.date = 'Date is required.';
  if (!form.time) errors.time = 'Time is required.';
  if (!form.message) errors.message = 'Message is required.';
  if (!form.type) errors.type = 'Message type is required.';

  // Se houver erros, mostra e não envia
  if (Object.keys(errors).length > 0) {
    form.setError(errors);
    return;
  }

  // Combina a data e hora para enviar ao backend
  form.start_time = `${form.date} ${form.time}:00`;

  // Define a rota com base no tipo de mensagem
  const route = form.type === 'whatsapp' ? '/schedule-whatsapp' : '/schedule-email';

  form.post(route, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Events / ${props.event.name} /Messages`,
        href: `/messages/create`,
    },
];




const defaultMessages = [
    `Hello {{ name }}, don't forget to join us for the event "{{ event }}" on {{ date }} at {{ hour }} at {{ location }}.`,
    `Hi {{ name }}, mark your calendar! The event "{{ event }}" is happening on {{ date }} at {{ hour }} at {{ location }}.`,
    `Dear {{ name }}, we are excited to see you at "{{ event }}" on {{ date }} at {{ hour }} at {{ location }}.`,
    `Hello {{ name }}, reminder: "{{ event }}" is just around the corner! Join us on {{ date }} at {{ hour }} at {{ location }}.`,
    `Hi {{ name }}, get ready for "{{ event }}"! It's happening on {{ date }} at {{ hour }} at {{ location }}.`
];

onMounted(() => {
    form.event_id = props.event?.id || '';
    const randomIndex = Math.floor(Math.random() * defaultMessages.length);
    form.message = defaultMessages[randomIndex]; // Define uma mensagem padrão aleatória
});

const cycleMessage = () => {
    const currentIndex = defaultMessages.indexOf(form.message);
    const nextIndex = (currentIndex + 1) % defaultMessages.length;
    form.message = defaultMessages[nextIndex];
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-8 shadow-xl rounded-2xl space-y-6">
        <h1 class="text-3xl text-gray-800 dark:text-white">
          Schedule a message for - <span class="text-4xl font-bold">{{ props.event.name }}</span>
        </h1>

        <!-- Flash messages -->
        <div v-if="$page.props.flash.success"
          class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg">
          ✅ {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error"
          class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg">
          ❌ {{ $page.props.flash.error }}
        </div>

        <!-- Data e hora -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Date to send</label>
            <input v-model="form.date" type="date"
              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-blue-200" />
            <div v-if="form.errors.date" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.date }}</div>
          </div>

          <div>
            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Time to send</label>
            <input v-model="form.time" type="time"
              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-blue-200" />
            <div v-if="form.errors.time" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.time }}</div>
          </div>
        </div>

        <!-- Tipo -->
        <div>
          <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Message Type</label>
          <select v-model="form.type"
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-blue-200">
            <option disabled value="">Select type...</option>
            <option value="email">Email</option>
          </select>
          <div v-if="form.errors.type" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.type }}</div>
        </div>

        <!-- QR Code -->
        <div v-if="form.type === 'email'" class="flex items-center mt-2 space-x-2">
          <input type="checkbox" v-model="form.send_qr"
            class="w-5 h-5 text-orange-600 dark:text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-orange-500 dark:focus:ring-blue-500" />
          <label class="text-gray-700 dark:text-gray-300 font-medium">Include QR Code in the email</label>
        </div>

            
        <!-- Mensagem personalizada -->
        <div>
          <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Custom Message</label>

                    <!-- Campos Dinâmicos -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span v-for="field in dynamicFields" :key="field.value" @click="insertAtCursor(field.value)"
                            class="cursor-pointer bg-orange-100 dark:bg-blue-100 text-orange-700 dark:text-blue-700 px-3 py-1 rounded-full hover:bg-orange-200 dark:hover:bg-blue-200 text-sm font-medium transition">
                            ➕ {{ field.label }}
                        </span>
                    </div>

          <div class="flex justify-end">
            <button @click="cycleMessage"
              class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white py-1 px-4 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 font-medium transition">
              Change Message
            </button>
          </div>

          <textarea ref="textareaRef" v-model="form.message"
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl mt-2 shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2focus:ring-blue-200"
            rows="4" placeholder="Type your message..."></textarea>

          <div v-if="form.errors.message" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.message }}</div>
        </div>


        <!-- Botão -->
        <div>
          <button @click="sendMessage"
            class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white py-3 text-lg font-semibold rounded-xl shadow-lg transition disabled:opacity-50"
            :disabled="form.processing">
            Schedule Message
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
