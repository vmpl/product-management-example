<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';

defineEmits(['submitted']);

const hasActions = computed(() => !! useSlots().actions);
</script>

<template>
    <div class="md:tw-grid md:tw-grid-cols-3 md:tw-gap-6">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="tw-mt-5 md:tw-mt-0 md:tw-col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="tw-px-4 tw-py-5 tw-bg-white dark:tw-bg-gray-800 sm:tw-p-6 tw-shadow"
                    :class="hasActions ? 'sm:tw-rounded-tl-md sm:tw-rounded-tr-md' : 'sm:tw-rounded-md'"
                >
                    <div class="tw-grid tw-grid-cols-6 tw-gap-6">
                        <slot name="form" />
                    </div>
                </div>

                <div v-if="hasActions" class="tw-flex tw-items-center tw-justify-end tw-px-4 tw-py-3 tw-bg-gray-50 dark:tw-bg-gray-800 tw-text-right sm:tw-px-6 tw-shadow sm:tw-rounded-bl-md sm:tw-rounded-br-md">
                    <slot name="actions" />
                </div>
            </form>
        </div>
    </div>
</template>
