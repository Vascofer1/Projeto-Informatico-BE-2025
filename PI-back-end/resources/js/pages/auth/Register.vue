<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Create an Account',
    href: '/register',
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <AuthBase
      title="Create an Account"
      description="Enter the details below to create an account"
      class="bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors"
    >
      <Head title="Register" />

      <form @submit.prevent="submit" class="flex flex-col gap-6">
        <div class="grid gap-6">
          <div class="grid gap-2">
            <Label for="name" class="text-gray-800 dark:text-gray-200">Name</Label>
            <Input
              id="name"
              type="text"
              required
              autofocus
              :tabindex="1"
              autocomplete="name"
              v-model="form.name"
              placeholder="Full name"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-200"
            />
            <InputError :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="email" class="text-gray-800 dark:text-gray-200">Email address</Label>
            <Input
              id="email"
              type="email"
              required
              :tabindex="2"
              autocomplete="email"
              v-model="form.email"
              placeholder="email@example.com"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-200"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div class="grid gap-2">
            <Label for="password" class="text-gray-800 dark:text-gray-200">Password</Label>
            <Input
              id="password"
              type="password"
              required
              :tabindex="3"
              autocomplete="new-password"
              v-model="form.password"
              placeholder="Password"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-200"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div class="grid gap-2">
            <Label for="password_confirmation" class="text-gray-800 dark:text-gray-200">Confirm password</Label>
            <Input
              id="password_confirmation"
              type="password"
              required
              :tabindex="4"
              autocomplete="new-password"
              v-model="form.password_confirmation"
              placeholder="Confirm password"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-200"
            />
            <InputError :message="form.errors.password_confirmation" />
          </div>

          <Button
            type="submit"
            class="mt-2 w-full text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
            tabindex="5"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            <span v-else>Create account</span>
          </Button>
        </div>
      </form>
    </AuthBase>
  </AppLayout>
</template>