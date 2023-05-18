<template>
    <CrudLayout title="Crud | Grid" :list="grids">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crud | Grid
            </h2>
        </template>
    </CrudLayout>
</template>
<script>
import CrudLayout from "../../Layouts/CrudLayout.vue";

export default {
    props: {
        grids: Array,
        size: Number,
        columns: Array,
    },
    components: {
        CrudLayout
    }
}
</script>
