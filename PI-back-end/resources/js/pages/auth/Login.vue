<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-100 via-white to-indigo-200 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 flex items-center justify-center px-4">
        <Head title="Login" />

        <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-xl p-10 space-y-6">
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">Eventify</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Enter your credentials to access the platform</p>
            </div>

            <div v-if="status" class="rounded-md bg-green-100 dark:bg-green-200/10 p-3 text-sm text-green-700 dark:text-green-400 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Email</label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" />
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition"
                        />
                    </div>
                    <div v-if="form.errors.email" class="text-sm text-red-500 mt-1">{{ form.errors.email }}</div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="text-sm font-medium text-gray-600 dark:text-gray-300">Password</label>
                        <a v-if="canResetPassword" :href="route('password.request')" class="text-sm text-indigo-500 hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" />
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition"
                        />
                    </div>
                    <div v-if="form.errors.password" class="text-sm text-red-500 mt-1">{{ form.errors.password }}</div>
                </div>

                <!-- Remember -->
                <div class="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        id="remember"
                        v-model="form.remember"
                        class="accent-indigo-600 dark:accent-indigo-500 rounded"
                    />
                    <label for="remember" class="text-sm text-gray-600 dark:text-gray-300">Remember me</label>
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 transition text-white font-semibold py-2.5 rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" />
                    <span v-if="!form.processing">Log in</span>
                    <span v-else>Logging in...</span>
                </button>
            </form>
        </div>
    </div>
</template>
