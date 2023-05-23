<template>
    <q-select
        v-model="current"
        :options="available"
        borderless
        options-cover
    />
</template>

<script lang="ts">
import {router} from "@inertiajs/vue3";

export default {
    computed: {
        current: {
            get() {
                const [value, label] = Object.entries(this.$page.props.locale.available)
                    .find(([value, label]) => value === this.$page.props.locale.current)
                return {label, value}
            },
            set({value}) {
                const urlRoute = this.$route('language', {locale: value});
                router.visit(urlRoute,
                    {replace: true})
            }
        },
        available() {
            return Object.entries(this.$page.props.locale.available)
                .map(([value, label]) => {
                    return {label, value}
                });
        },
    }
}
</script>

