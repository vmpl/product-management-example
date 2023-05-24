<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <p class="tw-text-sm tw-text-red-600 dark:tw-text-red-400">
            {{ message }}
        </p>
    </div>
</template>
