<template>
    <AppLayout :title="title">
        <template #header>
            <div class="row content-center q-gutter-x-sm">
                <q-btn icon="menu" color="primary" flat @click="leftNavOpen = !leftNavOpen"/>
                <slot name="header"/>
            </div>
        </template>
        <q-layout view="hHh lpR fFf">
            <q-drawer v-model="leftNavOpen">
                <q-scroll-area class="fit">
                    <q-list padding>
                        <q-item v-for="item in list" clickable v-ripple :href="route('crud.grid', {grid: item})">
                            <q-item-section>{{ item }}</q-item-section>
                            <q-item-section avatar>
                                <q-btn icon="add" square outline @click.stop
                                       :href="route('crud.grid.form', {grid: item})"/>
                            </q-item-section>
                        </q-item>
                    </q-list>
                </q-scroll-area>
            </q-drawer>
            <q-page-container>
                <q-page class="q-px-lg q-py-md">
                    <slot/>
                </q-page>
            </q-page-container>
        </q-layout>
    </AppLayout>
</template>
<script>
import AppLayout from "./AppLayout.vue";

export default {
    props: {
        title: String,
        list: Array,
    },
    data() {
        return {
            leftNavOpen: true,
        }
    },
    components: {
        AppLayout
    }
}
</script>
