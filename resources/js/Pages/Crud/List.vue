<template>
    <AppLayout title="Crud">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crud
            </h2>
        </template>

        <div>
            <ul>
                <li v-for="grid in grids">{{ grid }}</li>
            </ul>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    components: {AppLayout},
    props: {
        title: String,
        grids: Array,
    },
}
</script>
