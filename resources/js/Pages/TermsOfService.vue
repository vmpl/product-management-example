<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    terms: String,
});
</script>

<template>
    <Head title="Terms of Service" />

    <div class="tw-font-sans tw-text-gray-900 dark:tw-text-gray-100 tw-antialiased">
        <div class="tw-pt-4 tw-bg-gray-100 dark:tw-bg-gray-900">
            <div class="tw-min-h-screen tw-flex tw-flex-col tw-items-center tw-pt-6 sm:tw-pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="tw-w-full sm:tw-max-w-2xl tw-mt-6 tw-p-6 tw-bg-white dark:tw-bg-gray-800 tw-shadow-md tw-overflow-hidden sm:tw-rounded-lg tw-prose dark:tw-prose-invert" v-html="terms" />
            </div>
        </div>
    </div>
</template>
