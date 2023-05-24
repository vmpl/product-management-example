<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>


<template>
    <input
        v-model="proxyChecked"
        type="checkbox"
        :value="value"
        class="tw-rounded dark:tw-bg-gray-900 tw-border-gray-300 dark:tw-border-gray-700 tw-text-indigo-600 tw-shadow-sm focus:tw-ring-indigo-500 dark:focus:tw-ring-indigo-600 dark:focus:tw-ring-offset-gray-800"
    >
</template>
