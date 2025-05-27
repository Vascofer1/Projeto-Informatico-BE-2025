<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const errors = ref({});

// Dados do evento carregados inicialmente
const event = usePage().props.event;  // Supondo que os dados do evento já estão disponíveis

// Usando o hook useForm para lidar com o formulário
const form = useForm({
  id: event.id,
  name: event.name,
  type: event.type !== "Lecture" && event.type !== "Workshop" && event.type !== "Conference" ? "Other" : event.type,
  newType: event.type !== "Lecture" && event.type !== "Workshop" && event.type !== "Conference" ? event.type : "",
  
  category: event.category !== "Technology" && event.category !== "Health" && event.category !== "Sports" ? "Other" : event.category,
  newCategory: event.category !== "Technology" && event.category !== "Health" && event.category !== "Sports" ? event.category : "",
  
  location: event.location !== "Online" ? "On-site" : event.location,
  newLocation: event.location !== "Online" && event.location !== "On-site" ? event.location : "",
  
  start_date: event.start_date,
  start_time: event.start_time,
  end_date: event.end_date,
  end_time: event.end_time,
  limit_participants: event.limit_participants,
  description: event.description,
  image: null,
  custom_background: null,
});

// Função para lidar com a alteração do campo de imagem
const handleImageChange = (e: any) => {
  form.image = e.target.files[0];
};

const formatTime = (time: string) => {
  return time ? time.slice(0, 5) : ""; // Remove os segundos (H:i:s -> H:i)
};

onMounted(() => {
  form.start_time = formatTime(event.start_time);
  form.end_time = formatTime(event.end_time);
});

// Garante que o tempo é enviado no formato correto
const formatTimeForBackend = (time: string) => {
  return time ? `${time}:00` : ""; // Adiciona segundos (H:i -> H:i:s)
};

// Atualiza os valores antes de enviar o formulário
const submit = () => {
  const formData = new FormData();
  
  // Adiciona `_method: PUT` para Laravel entender como um update
  formData.append('_method', 'PUT');

  // Atualiza os valores corretamente antes de enviar
  if (form.type === "Other" && form.newType) {
    form.type = form.newType;
  }

  if (form.category === "Other" && form.newCategory) {
    form.category = form.newCategory;
  }

  if (form.location === "On-site" && form.newLocation) {
    form.location = form.newLocation;
  }

  Object.keys(form).forEach((key) => {
    if (form[key] !== null) {
      formData.append(key, form[key]);
    }
  });

  if (form.image) {
    formData.append('image', form.image);
  }

  router.post(`/events/${form.id}`, formData, {
    forceFormData: true, // Obrigatório para enviar ficheiros corretamente
    onError: (err) => {
      console.log("Erro na atualização:", err);
      errors.value = err;
    },
    onSuccess: () => {
      console.log("Evento atualizado com sucesso!");
    }
  });
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / Edit Event`,
    href: `/events/edit`,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-10 bg-gray-100 dark:bg-gray-900 min-h-screen flex justify-center">
      <div class="bg-white dark:bg-gray-800 p-10 rounded-lg shadow-xl w-full max-w-5xl">
        <h2 class="text-2xl mb-6 text-center text-gray-800 dark:text-white">
          EDIT EVENT - <span class="text-3xl font-bold">{{ event.name }}</span>
        </h2>

        <!-- Name -->
        <h3 class="mt-2 font-bold text-gray-800 dark:text-gray-200">Name</h3>
        <input
          v-model="form.name"
          placeholder="Event Name"
          class="w-full p-2 mb-4 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
        />

        <!-- Type -->
        <h3 class="mt-2 font-bold text-gray-800 dark:text-gray-200">Type</h3>
        <div class="flex space-x-2 mb-4">
          <div class="relative w-1/2">
            <span v-if="!form.type" class="absolute left-3 top-2 text-gray-400 pointer-events-none">
              Select Event Type
            </span>
            <select
              v-model="form.type"
              class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
            >
              <option value="" disabled hidden></option>
              <option value="Lecture">Lecture</option>
              <option value="Workshop">Workshop</option>
              <option value="Conference">Conference</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <input
            v-if="form.type === 'Other'"
            v-model="form.newType"
            placeholder="Enter event type"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
        </div>

        <!-- Category -->
        <h3 class="mt-5 font-bold text-gray-800 dark:text-gray-200">Category</h3>
        <div class="flex space-x-2 mb-4">
          <div class="relative w-1/2">
            <span v-if="!form.category" class="absolute left-3 top-2 text-gray-400 pointer-events-none">
              Select Event Category
            </span>
            <select
              v-model="form.category"
              class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
            >
              <option value="" disabled hidden></option>
              <option value="Technology">Technology</option>
              <option value="Health">Health</option>
              <option value="Sports">Sports</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <input
            v-if="form.category === 'Other'"
            v-model="form.newCategory"
            placeholder="Enter category"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
        </div>

        <!-- Location -->
        <h3 class="mt-5 font-bold text-gray-800 dark:text-gray-200">Location</h3>
        <div class="flex space-x-2 mb-4">
          <div class="relative w-1/2">
            <select
              v-model="form.location"
              class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
            >
              <option value="Online">Online</option>
              <option value="On-site">On-site</option>
            </select>
          </div>
          <input
            v-if="form.location === 'On-site'"
            v-model="form.newLocation"
            placeholder="Enter location"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
        </div>

        <!-- Start Date and Time -->
        <h3 class="mt-5 font-bold text-gray-800 dark:text-gray-200">Start Date and Time</h3>
        <div class="flex space-x-2 mb-2">
          <input
            type="date"
            v-model="form.start_date"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
          <input
            type="time"
            v-model="form.start_time"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
        </div>
        <p v-if="errors.start_date" class="text-red-500 text-lg">ERROR: {{ errors.start_date }}</p>
        <p v-if="errors.start_time" class="text-red-500 text-lg">ERROR: {{ errors.start_time }}</p>

        <!-- End Date and Time -->
        <h3 class="mt-4 font-bold text-gray-800 dark:text-gray-200">End Date and Time</h3>
        <div class="flex space-x-2 mb-2">
          <input
            type="date"
            v-model="form.end_date"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
          <input
            type="time"
            v-model="form.end_time"
            class="w-1/2 p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
          />
        </div>
        <p v-if="errors.end_date" class="text-red-500 text-lg">ERROR: {{ errors.end_date }}</p>
        <p v-if="errors.end_time" class="text-red-500 text-lg">ERROR: {{ errors.end_time }}</p>

        <!-- Event Image -->
        <h3 class="mt-5 font-bold text-gray-800 dark:text-gray-200">Event Image</h3>
        <input
          type="file"
          @change="(e: any) => (form.image = e.target.files[0])"
          accept="image/png, image/jpeg, image/jpg"
          class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600"
        />
        <p class="text-gray-600 dark:text-gray-400 text-sm">Accepted formats: PNG, JPG, JPEG</p>

        <!-- Participant Limit -->
        <h3 class="mt-5 font-bold text-gray-800 dark:text-gray-200">Participant Limit</h3>
        <input
          v-model="form.limit_participants"
          type="number"
          placeholder="Quantity"
          min="1"
          class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
        />

        <!-- Description -->
        <h3 class="mt-5 font-bold text-gray-800 dark:text-gray-200">Description</h3>
        <textarea
          v-model="form.description"
          class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100"
        ></textarea>

        <h3 class="mt-5 font-bold text-gray-700 dark:text-gray-300">Registration Form Background</h3>
<input type="file" @change="(e: any) => (form.custom_background = e.target.files[0])"
  accept="image/png, image/jpeg, image/jpg"
  class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" />
<p class="text-gray-600 dark:text-gray-400 text-sm">Accepted formats: PNG, JPG, JPEG</p>

        <!-- Buttons -->
        <div class="flex justify-between mt-4">
          <button
            @click="router.get('/events')"
            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded transition"
          >
            Cancel
          </button>
          <button
            @click="submit"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition"
          >
            Confirm Changes
          </button>
          
        </div>
      </div>
    </div>
  </AppLayout>
</template>
