<template>
    <CrudLayout title="Crud" :list="models">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
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
