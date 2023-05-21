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
            v-model="gridSelection"
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
        selection: Array<Object>,
    },
    data() {
        return {
            gridSelection: this.selection ?? [],
        }
    },
    computed: {
        selectionRead: {
            get() {
                return this.gridSelection.map(({id, name}) => {
                    return {label: name, key: id};
                })
            },
            set(value) {
                const keys = value.map(it => it.key);
                this.gridSelection = this.gridSelection.filter(it => keys.includes(it.id));
            }
        }
    }
}
</script>
