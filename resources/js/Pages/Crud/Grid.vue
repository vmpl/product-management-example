<template>
    <CrudLayout title="Crud | Grid" :list="models">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crud | Grid
            </h2>
        </template>

        <div class="q-pa-md">
            <ModelGrid
                :size="size"
                :columns="columns"
                :url-fetch="urlFetch"
                :url-form="urlForm"
                :url-delete="urlDelete"
            />
        </div>
    </CrudLayout>
</template>
<script lang="ts">
import CrudLayout from "../../Layouts/CrudLayout.vue";
import ModelGrid from "../../Components/ModelGrid.vue";

export default {
    props: {
        models: Array<String>,
        size: Number,
        columns: Array<Object>,
        urlFetch: String,
        urlForm: String,
        urlDelete: String,
    },
    components: {
        ModelGrid,
        CrudLayout
    }
}
</script>
