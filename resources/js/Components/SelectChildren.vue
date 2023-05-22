<template>
    <q-select
        v-model="selectionRead"
        class="bg-white"
        multiple
        use-chips
        :label="label"
        @click.prevent.stop="() => $refs.gridDialog.show()"
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

export default {
    components: {ModelGrid},
    props: {
        label: String,
        size: Number,
        columns: Array<Object>,
        urlFetch: String,
        modelValue: Array<Object>,
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
