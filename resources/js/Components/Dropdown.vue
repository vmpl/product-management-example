<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['tw-py-1', 'tw-bg-white dark:tw-bg-gray-700'],
    },
});

let open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        'tw-48': 'tw-w-48',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'tw-origin-top-left tw-left-0';
    }

    if (props.align === 'right') {
        return 'tw-origin-top-right tw-right-0';
    }

    return 'tw-origin-top';
});
</script>

<template>
    <div class="tw-relative">
        <div @click="open = ! open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="tw-fixed tw-inset-0 tw-z-40" @click="open = false" />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="tw-absolute tw-z-50 tw-mt-2 tw-rounded-md tw-shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none;"
                @click="open = false"
            >
                <div class="tw-rounded-md tw-ring-1 tw-ring-black tw-ring-opacity-5" :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>
