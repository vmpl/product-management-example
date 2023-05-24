<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTeamForm from '@/Pages/Teams/Partials/CreateTeamForm.vue';
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="tw-font-semibold tw-text-xl tw-text-gray-800 dark:tw-text-gray-200 tw-leading-tight">
                Create Team
            </h2>
        </template>

        <div>
            <div class="tw-max-w-7xl tw-mx-auto tw-py-10 sm:tw-px-6 lg:tw-px-8">
                <CreateTeamForm />
            </div>
        </div>
    </AppLayout>
</template>
