<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const errors = computed(() => page.props.errors);

const props = defineProps({
  event: Object,
});

const formData = ref({
  name: "",
  email: "",
  phone: "",
});

const backgroundStyle = computed(() => {
  let url = "";

  if (props.event.custom_background) {
    url = `/storage/${props.event.custom_background}`;
  } else {
    switch (props.event.category) {
      case 'Sports':
        url = '/images/bg-sport.jpg';
        break;
      case 'Health':
        url = '/images/bg-health.jpg';
        break;
      case 'Technology':
        url = '/images/bg-tech.jpg';
        break;
      default:
        return { backgroundColor: '#bfdbfe' }; // equivalente a bg-blue-100
    }
  }

  return {
    backgroundImage: `url('${url}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
  };
});

// Contador regressivo
const countdown = ref("");
const calculateCountdown = () => {
  const eventDate = new Date(`${props.event.start_date}T${props.event.start_time}`);
  const now = new Date();
  const diff = eventDate.getTime() - now.getTime();

  if (diff <= 0) {
    countdown.value = "The event has already started!";
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
  const minutes = Math.floor((diff / (1000 * 60)) % 60);
  const seconds = Math.floor((diff / 1000) % 60);

  countdown.value = `${days}d ${hours}h ${minutes}m ${seconds}s`;
};

const phrases = [
  `Get ready for a one-of-a-kind ${props.event.type} focused on ${props.event.category}!`,
  `Experience the thrill of a ${props.event.category}-themed ${props.event.type} like no other!`,
  `Discover a unique ${props.event.type} celebrating the world of ${props.event.category}.`,
  `Step into the future of ${props.event.category} with this unforgettable ${props.event.type}!`,
  `Join us for an extraordinary ${props.event.type} that will redefine your understanding of ${props.event.category}.`
];

// Escolher uma frase aleatoriamente
const randomPhrase = ref('');
const getRandomPhrase = () => {
  const randomIndex = Math.floor(Math.random() * phrases.length);
  randomPhrase.value = phrases[randomIndex];
};

onMounted(() => {
  getRandomPhrase();
  calculateCountdown();
  setInterval(calculateCountdown, 1000);
});

const eventHasStarted = computed(() => {
  const eventDate = new Date(`${props.event.start_date}T${props.event.start_time}`);
  const now = new Date();
  return now >= eventDate;
});

const isSameDay = computed(() => props.event.start_date === props.event.end_date);

// Função para formatar a data conforme desejado
const formatDate = (date: string) => {
  const eventDate = new Date(date);
  return eventDate.toLocaleDateString("en-GB", { year: 'numeric', month: 'long', day: 'numeric' });
};

// Função para formatar a hora
const formatTime = (time: string) => {
  const eventTime = new Date(`1970-01-01T${time}`);
  return eventTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formattedStartDate = computed(() => formatDate(props.event.start_date));
const formattedStartTime = computed(() => formatTime(props.event.start_time));
const formattedEndDate = computed(() => formatDate(props.event.end_date));
const formattedEndTime = computed(() => formatTime(props.event.end_time));

const duration = computed(() => {
  const start = new Date(`${props.event.start_date}T${props.event.start_time}`);
  const end = new Date(`${props.event.end_date}T${props.event.end_time}`);
  const diff = end.getTime() - start.getTime();
  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff / (1000 * 60)) % 60);
  return `${hours}h ${minutes}m`;
});

const submitForm = () => {
  if (eventHasStarted.value) {
    alert("Sorry, this event has already started. Registration is closed.");
    return;
  }

  router.post(`/inscricao/${props.event.id}`, formData.value, {
    onSuccess: () => {
      alert("Registration completed successfully!");
      formData.value = { name: "", email: "", phone: "" }; // Limpar formulário
    },
  });
};
</script>

<template>
  <div class="min-h-screen p-6" :style="backgroundStyle">

    <div class="max-w-4xl mx-auto bg-white/80 backdrop-blur shadow-xl rounded-lg p-6">
      <h1 class="text-3xl text-center text-gray-800">
        Registration for <span class="font-bold">{{ props.event.name }}</span>
      </h1>
      <br>
      <p class="text-center text-4xl text-gray-500 font-semibold mb-6">{{ countdown }}</p>

      <h1 class="text-center text-gray-800">
        {{ randomPhrase }}
      </h1>
      <br>
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Information Section -->
        <div class="flex-1">
          <h2 class="text-xl font-semibold text-gray-700">Event Information:</h2>

          <div v-if="isSameDay">
            <p class="text-gray-600">Date: <span class="font-bold">{{ formattedStartDate }}</span></p>
            <p class="text-gray-600">Time: <span class="font-bold">{{ formattedStartTime }} - {{ formattedEndTime
                }}</span></p>
          </div>

          <div v-else>
            <p class="text-gray-600">Start: <span class="font-bold">{{ formattedStartDate }} {{ formattedStartTime
                }}</span></p>
            <p class="text-gray-600">End: <span class="font-bold">{{ formattedEndDate }} {{ formattedEndTime }}</span>
            </p>
          </div>

          <p class="text-gray-600">Duration: <span class="font-bold">{{ duration }}</span></p>
          <p class="text-gray-600">Location: <span class="font-bold">{{ props.event.location }}</span></p>
          <p class="text-gray-600 text-justify">
            Description:
            <span class="font-bold">
              {{ props.event.description ? props.event.description : "There's nothing about this event yet. Stay tuned for more information!!" }}
            </span>
          </p>
        </div>

        <!-- Event Image Section -->
        <div v-if="props.event.image" class="flex-shrink-0 w-full md:w-1/3 mx-auto md:mx-0 md:ml-6 mb-4 md:mb-0">
          <img :src="`/storage/${props.event.image}`" alt="Imagem do evento"
            class="w-full md:w-auto md:h-auto max-h-40 md:max-h-56 object-contain rounded-lg shadow-lg">
        </div>
      </div>

      <br>
      <form v-if="!eventHasStarted" @submit.prevent="submitForm" class="space-y-4 mt-6">
        <input v-model="formData.name" placeholder="Name" class="border p-2 w-full text-black dark:text-black"
          required />
        <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>

        <input v-model="formData.email" type="email" placeholder="Email"
          class="border p-2 w-full text-black dark:text-black" required />
        <p v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</p>

        <input v-model="formData.phone" type="tel" placeholder="Phone Number"
          class="border p-2 w-full text-black dark:text-black" required />
        <p v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</p>

        <div class="flex justify-center">
          <button type="submit" class="bg-blue-500 text-white p-2 rounded w-1/3">Subscribe</button>
        </div>

        <p class="text-center text-gray-600 mt-4">
          Don't miss this unique opportunity!
        </p>
      </form>

      <p v-else class="text-center text-red-600 text-2xl font-bold mt-8">
        Sorry, registration is already closed.
      </p>
    </div>
  </div>
</template>
