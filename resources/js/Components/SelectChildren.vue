<template>
    <q-select class="bg-white" multiple use-chips :label="label" @click.prevent.stop="() => $refs.gridDialog.show()"/>
    <q-dialog ref="gridDialog">
        <ModelGrid
            :size="size"
            :columns="columns"
            :url-fetch="urlFetch"
        />
    </q-dialog>
</template>
<script lang="ts">
import ModelGrid from "./ModelGrid.vue";

export default {
    props: {
        label: String,
        size: Number,
        columns: Array<Object>,
        urlFetch: String,
    },
    components: {ModelGrid},
}
</script>
