<template>
    <AppLayout title="Crud">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crud
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <q-list bordered separator>
                        <q-item clickable v-ripple>
                            <q-item-section v-for="grid in grids" class="text-white">{{ grid }}</q-item-section>
                        </q-item>
                    </q-list>
                </div>
            </div>
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
