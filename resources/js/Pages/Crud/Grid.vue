<template>
    <CrudLayout title="Crud | Grid" :list="models">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crud | Grid
            </h2>
        </template>

        <div class="q-pa-md">
            <q-toolbar v-if="toolbarAvailable" class="q-mb-lg q-gutter-x-md">
                <q-select :options="actions"
                          v-model="chosenAction"
                          label="Action"
                          class="bg-white"
                          clearable
                />
                <q-btn
                    class="bg-white"
                    label="Run"
                    @click="() => submitMassAction()"
                    :disable="disableMassAction"
                />
            </q-toolbar>
            <ModelGrid
                :size="size"
                :columns="columns"
                :url-fetch="urlFetch"
                :url-form="urlForm"
                :url-delete="urlDelete"
                :selection="selection"
                v-model="selected"
            />
        </div>
    </CrudLayout>
</template>
<script lang="ts">
import CrudLayout from "../../Layouts/CrudLayout.vue";
import ModelGrid from "../../Components/ModelGrid.vue";
import axios from "axios";

export default {
    components: {ModelGrid, CrudLayout},
    props: {
        models: Array<String>,
        size: Number,
        columns: Array<Object>,
        urlFetch: String,
        urlForm: String,
        urlDelete: String,
        urlAction: String,
        actions: {
            type: Array<String>,
            default: [],
        }
    },
    data() {
        return {
            chosenAction: '',
            selected: [],
        }
    },
    computed: {
        toolbarAvailable() {
            return !!this.actions.length;
        },
        selection() {
            return !this.chosenAction ? 'none' : 'multiple';
        },
        disableMassAction() {
            return !this.chosenAction || !this.selected.length;
        }
    },
    methods: {
        submitMassAction() {
            const ids = this.selected.map(it => it.id);
            axios.post(this.urlAction.replace(':Action', this.chosenAction), {ids})
                .then(response => {
                    return this.$q.dialog({
                        message: `Items has change status to ${this.chosenAction}`,
                        color: 'success',
                        seamless: true,
                        position: "bottom",
                    })
                })
                .catch(error => {
                    console.error(error)
                    return this.$q.dialog({
                        message: 'Error occur while apply mass action.',
                        color: 'error',
                        seamless: true,
                        position: "bottom",
                    })
                })
        },
    }
}
</script>
<style lang="scss">
.q-input {
    width: 200px;
}
</style>
