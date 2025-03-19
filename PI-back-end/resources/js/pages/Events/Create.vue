<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const form = ref({
  name: "",
  type: "",
  category: "",
  location: "",
  start_date: "",
  start_time: "",
  end_date: "",
  end_time: "",
  image: null as File | null,
  limit: "",
  description: "",
});

const submit = () => {
  const formData = new FormData();
  Object.keys(form.value).forEach((key) => {
    if (form.value[key as keyof typeof form.value]) {
      formData.append(key, form.value[key as keyof typeof form.value] as string);
    }
  });

  router.post("/events", formData);
};
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen flex justify-center">
    <div class="bg-blue-400 p-6 rounded-lg shadow-lg w-full max-w-2xl">
      <h2 class="text-xl font-bold mb-4">Informação básica</h2>

      <input v-model="form.name" placeholder="Nome do evento" class="w-full p-2 mb-4 border rounded" />

      <div class="flex space-x-2">
        <select v-model="form.type" class="w-1/2 p-2 border rounded">
          <option value="">Tipo</option>
          <option value="Workshop">Workshop</option>
          <option value="Conferência">Conferência</option>
        </select>

        <select v-model="form.category" class="w-1/2 p-2 border rounded">
          <option value="">Categoria</option>
          <option value="Tecnologia">Tecnologia</option>
          <option value="Saúde">Saúde</option>
        </select>
      </div>

      <h3 class="mt-4">Localização</h3>
      <div class="flex space-x-2">
        <button @click="form.location = 'Local'" class="p-2 bg-white border rounded">Local</button>
        <button @click="form.location = 'Online'" class="p-2 bg-white border rounded">Online</button>
        <button @click="form.location = 'A ser anunciado'" class="p-2 bg-white border rounded">A ser anunciado</button>
      </div>

      <input v-model="form.location" placeholder="Localização do evento" class="w-full p-2 mt-2 border rounded" />

      <h3 class="mt-4">Data e hora</h3>
      <div class="flex space-x-2">
        <input type="date" v-model="form.start_date" class="w-1/2 p-2 border rounded" />
        <input type="time" v-model="form.start_time" class="w-1/2 p-2 border rounded" />
      </div>

      <div class="flex space-x-2 mt-2">
        <input type="date" v-model="form.end_date" class="w-1/2 p-2 border rounded" />
        <input type="time" v-model="form.end_time" class="w-1/2 p-2 border rounded" />
      </div>

      <h3 class="mt-4">Imagem do Evento</h3>
      <input type="file" @change="(e: any) => (form.image = e.target.files[0])" class="w-full p-2 border rounded bg-white" />

      <h3 class="mt-4">Limite de Participantes</h3>
      <input v-model="form.limit" type="number" placeholder="Número" class="w-full p-2 border rounded" />

      <h3 class="mt-4">Descrição</h3>
      <textarea v-model="form.description" class="w-full p-2 border rounded"></textarea>

      <button @click="submit" class="mt-4 bg-blue-600 text-white p-2 rounded w-full hover:bg-blue-700">
        Criar Evento
      </button>
    </div>
  </div>
</template>
