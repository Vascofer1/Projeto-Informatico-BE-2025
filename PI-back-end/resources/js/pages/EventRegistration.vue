<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  event: Object,
});

const formData = ref({
  name: "",
  email: "",
  phone: "",

});

const submitForm = () => {
  router.post(`/inscricao/${props.event.id}`, formData.value, {
    onSuccess: () => {
      alert("Inscrição feita com sucesso!");
      formData.value = { name: "", email: "", phone: "" }; // Limpar formulário
    },
  });
};
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-3xl font-bold mb-4">Inscrição para {{ event.name }}</h1>
    <form @submit.prevent="submitForm" class="space-y-4">
      <input v-model="formData.name" placeholder="Nome" class="border p-2 w-full" required />
      <input v-model="formData.email" type="email" placeholder="Email" class="border p-2 w-full" required />
      <input v-model="formData.phone" type="tel" placeholder="Telemóvel" class="border p-2 w-full" required />
      <button type="submit" class="bg-blue-500 text-white p-2 rounded">Inscrever-se</button>
    </form>
  </div>
</template>
