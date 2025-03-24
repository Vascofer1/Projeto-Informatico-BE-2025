<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
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
  image: null as File | null,
  limit_participants: "",
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

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / New Event`,
    href: `/events/create`,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 bg-gray-100 min-h-screen flex justify-center">
      <div class="bg-blue-300 p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-4">NEW EVENT</h2>

        <input v-model="form.name" placeholder="Event Name" class="w-full p-2 mb-4 border rounded" />

        <h3 class="mt-2">Type</h3>
        <div class="flex space-x-2">
          <div class="relative w-1/2">
            <span v-if="!form.type" class="absolute left-3 top-2 text-gray-400 pointer-events-none">
              Select Event Type
            </span>
            <select v-model="form.type" class="w-full p-2 border rounded bg-white">
              <option value="" disabled hidden></option>
              <option value="Lecture">Lecture</option>
              <option value="Workshop">Workshop</option>
              <option value="Conference">Conference</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <input v-if="form.type === 'Other'" v-model="form.newType" placeholder="Enter event type"
            class="w-1/2 p-2 border rounded" />
        </div>

        <h3 class="mt-5">Category</h3>
        <div class="flex space-x-2">
          <div class="relative w-1/2">
            <span v-if="!form.category" class="absolute left-3 top-2 text-gray-400 pointer-events-none">
              Select Event Category
            </span>
            <select v-model="form.category" class="w-full p-2 border rounded bg-white">
              <option value="" disabled hidden></option>
              <option value="Technology">Technology</option>
              <option value="Health">Health</option>
              <option value="Sports">Sports</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <input v-if="form.category === 'Other'" v-model="form.newCategory" placeholder="Enter category"
            class="w-1/2 p-2 border rounded" />
        </div>

        <h3 class="mt-5">Location</h3>
        <select v-model="form.location" class="w-1/2 p-2 border rounded">
          <option value="" disabled hidden>Select Event Location</option>
          <option value="Online">Online</option>
          <option value="On-site">On-site</option>
        </select>
        <input v-if="form.location === 'On-site'" v-model="form.venue" placeholder="Event venue"
          class="w-full p-2 mt-2 border rounded" />

        <h3 class="mt-5">Start Date and Time</h3>
        <div class="flex space-x-2">
          <input type="date" v-model="form.start_date" class="w-1/2 p-2 border rounded" />
          <input type="time" v-model="form.start_time" class="w-1/2 p-2 border rounded" />
        </div>

        <h3 class="mt-4">End Date and Time</h3>
        <div class="flex space-x-2">
          <input type="date" v-model="form.end_date" class="w-1/2 p-2 border rounded" />
          <input type="time" v-model="form.end_time" class="w-1/2 p-2 border rounded" />
        </div>

        <h3 class="mt-5">Event Image</h3>
        <input type="file" @change="(e: any) => (form.image = e.target.files[0])"
          class="w-full p-2 border rounded bg-white" />

        <h3 class="mt-5">Participant Limit</h3>
        <input v-model="form.limit_participants" type="number" placeholder="Quantity"
          class="w-full p-2 border rounded" min="1" />

        <h3 class="mt-5">Description</h3>
        <textarea v-model="form.description" class="w-full p-2 border rounded"></textarea>

        <div class="flex justify-between mt-4">
          <button @click="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create Event
          </button>
          <button @click="router.get('/events')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
