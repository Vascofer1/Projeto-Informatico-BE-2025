<script setup lang="ts">
import { ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const form = ref({
  name: "",
  type: "",
  category: "",
  location: "",
  start_date: "",
  start_time: "",
  end_date: "",
  end_time: "",
  image: null,
  limit_participants: "",
  description: "",
});

const errors = ref({}); // Para guardar os erros

const submit = () => {
  const formData = new FormData();

  if (form.value.location === "On-site" && form.value.venue) {
    form.value.location = form.value.venue;
  }

  if (form.value.type === "Other" && form.value.newType) {
    form.value.type = form.value.newType;
  }

  if (form.value.category === "Other" && form.value.newCategory) {
    form.value.category = form.value.newCategory;
  }

  Object.keys(form.value).forEach((key) => {
    if (form.value[key as keyof typeof form.value]) {
      formData.append(key, form.value[key as keyof typeof form.value] as string);
    }
  });

  router.post("/events", formData, {
    onError: (err) => {
      errors.value = err; // Guardar os erros para mostrar no frontend
    }
  });
};

const page = usePage();
errors.value = page.props.errors || {}; // Pega erros se já existirem

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / New Event`,
    href: `/events/create`,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-10 bg-gray-100 dark:bg-gray-900 min-h-screen flex justify-center">
      <div class="bg-white dark:bg-gray-800 p-10 rounded-lg shadow-xl w-full max-w-5xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-white">NEW EVENT</h2>

        <!-- Nome -->
        <h3 class="mt-2 font-bold text-gray-700 dark:text-gray-300">Name</h3>
        <input v-model="form.name" placeholder="Event Name"
          class="w-full p-2 mb-4 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />

        <!-- Tipo -->
        <h3 class="mt-2 font-bold text-gray-700 dark:text-gray-300">Type</h3>
        <div class="flex space-x-2 mb-4">
          <div class="relative w-1/2">
            <span v-if="!form.type"
              class="absolute left-3 top-2 text-gray-400 dark:text-gray-400 pointer-events-none">
              Select Event Type
            </span>
            <select v-model="form.type"
              class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              <option value="" disabled hidden></option>
              <option value="Lecture">Lecture</option>
              <option value="Workshop">Workshop</option>
              <option value="Conference">Conference</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <input v-if="form.type === 'Other'" v-model="form.newType" placeholder="Enter event type"
            class="w-1/2 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
        </div>

        <!-- Categoria -->
        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Category</h3>
        <div class="flex space-x-2 mb-4">
          <div class="relative w-1/2">
            <span v-if="!form.category"
              class="absolute left-3 top-2 text-gray-400 dark:text-gray-400 pointer-events-none">
              Select Event Category
            </span>
            <select v-model="form.category"
              class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              <option value="" disabled hidden></option>
              <option value="Technology">Technology</option>
              <option value="Health">Health</option>
              <option value="Sports">Sports</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <input v-if="form.category === 'Other'" v-model="form.newCategory" placeholder="Enter category"
            class="w-1/2 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
        </div>

        <!-- Localização -->
        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Location</h3>
        <div class="flex space-x-2 mb-4">
          <div class="relative w-1/2">
            <span v-if="!form.location"
              class="absolute left-3 top-2 text-gray-400 dark:text-gray-400 pointer-events-none">
              Select Event Location
            </span>
            <select v-model="form.location"
              class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              <option value="" disabled hidden></option>
              <option value="Online">Online</option>
              <option value="On-site">On-site</option>
            </select>
          </div>
          <input v-if="form.location === 'On-site'" v-model="form.venue" placeholder="Event venue"
            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
        </div>

        <!-- Data e Hora de Início -->
        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Start Date and Time</h3>
        <div class="flex space-x-2 mb-2">
          <input type="date" v-model="form.start_date"
            class="w-1/2 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
          <input type="time" v-model="form.start_time"
            class="w-1/2 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
        </div>
        <p v-if="errors.start_date" class="text-red-500 dark:text-red-400 text-lg">ERROR: {{ errors.start_date }}</p>
        <p v-if="errors.start_time" class="text-red-500 dark:text-red-400 text-lg">ERROR: {{ errors.start_time }}</p>

        <!-- Data e Hora de Término -->
        <h3 class="mt-4 font-bold text-gray-700 dark:text-gray-300">End Date and Time</h3>
        <div class="flex space-x-2 mb-2">
          <input type="date" v-model="form.end_date"
            class="w-1/2 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
          <input type="time" v-model="form.end_time"
            class="w-1/2 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
        </div>
        <p v-if="errors.end_date" class="text-red-500 dark:text-red-400 text-lg">ERROR: {{ errors.end_date }}</p>
        <p v-if="errors.end_time" class="text-red-500 dark:text-red-400 text-lg">ERROR: {{ errors.end_time }}</p>

        <!-- Imagem do Evento -->
        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Event Image</h3>
        <input type="file" @change="(e: any) => (form.image = e.target.files[0])"
          accept="image/png, image/jpeg, image/jpg"
          class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" />
        <p class="text-gray-600 dark:text-gray-400 text-sm">Accepted formats: PNG, JPG, JPEG</p>

        <!-- Limite de Participantes -->
        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Participant Limit</h3>
        <input v-model="form.limit_participants" type="number" placeholder="Quantity"
          class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" min="1" />

        <!-- Descrição -->
        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Description</h3>
        <textarea v-model="form.description"
          class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>

        <!-- Botões -->
        <div class="flex justify-between mt-4">
          <button @click="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
            Confirm
          </button>
          <button @click="router.get('/events')"
            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded transition">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
