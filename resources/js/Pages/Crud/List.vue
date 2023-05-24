<template>
    <CrudLayout title="Crud" :list="models">
        <template #header>
            <h2 class="tw-font-semibold tw-text-xl tw-text-gray-800 dark:tw-text-gray-200 tw-leading-tight">
                Crud
            </h2>
        </template>
    </CrudLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudLayout from "../../Layouts/CrudLayout.vue";

export default {
    components: {CrudLayout, AppLayout},
    props: {
        models: Array,
    },
}
</script>
