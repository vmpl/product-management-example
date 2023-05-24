<template>
    <q-select
        :label="label"
        v-model="selectionRead"
        @click.prevent.stop="() => $refs.gridDialog.show()"
        :rules="rules"
        :lazy-rules="lazyRules"
        multiple
        use-chips
        outlined
    />
    <q-dialog ref="gridDialog">
        <ModelGrid
            :size="size"
            :columns="columns"
            :url-fetch="urlFetch"
            selection="multiple"
            v-model="value"
        />
    </q-dialog>
</template>
<script lang="ts">
import ModelGrid from "./ModelGrid.vue";
import {PropType} from "vue";

export default {
    components: {ModelGrid},
    props: {
        label: String,
        size: Number,
        columns: Array<Object>,
        urlFetch: String,
        modelValue: Array<Object>,
        rules: Array<Function>,
        lazyRules: Object as PropType<Boolean | 'ondemand' | undefined>,
    },
    emits: ['update:modelValue'],
    computed: {
        value: {
            get() {
                return this.modelValue ?? [];
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        selectionRead: {
            get() {
                return this.value.map(({id, name}) => {
                    return {label: name, key: id};
                })
            },
            set(value) {
                const keys = value.map(it => it.key);
                this.value = this.value.filter(it => keys.includes(it.id));
            }
        },
    }
}
</script>
