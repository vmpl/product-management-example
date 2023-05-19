<template>
    <CrudLayout title="Crud | Grid" :list="models">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crud | Grid
            </h2>
        </template>

        <div class="q-pa-md">
            <q-table
                ref="tableElement"
                :columns="gridColumns"
                :rows="gridRows"
                :loading="loading"
                :pagination="pagination"
                :filter="filter"
                row-key="id"
                separator="vertical"
                @request="onRequest"
            >
                <template v-slot:header="props">
                    <q-tr :props="props">
                        <q-th
                            v-for="col in props.cols"
                            :key="col.name"
                            :props="props"
                        >
                            {{ col.label }}
                        </q-th>
                    </q-tr>
                </template>

                <template v-slot:body-cell-actions="props">
                    <q-td auto-width class="q-gutter-x-sm">
                        <q-btn icon="edit" @click.stop="() => onEdit(props)"/>
                        <q-btn icon="delete" @click.stop="() => confirmDelete(props)"/>
                    </q-td>
                </template>
            </q-table>
        </div>
    </CrudLayout>
</template>
<script lang="ts">
import CrudLayout from "../../Layouts/CrudLayout.vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";

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
        CrudLayout
    },
    data() {
        const gridColumns = [
            ...this.columns,
            {
                name: 'actions',
                label: 'Actions',
                required: false,
                align: 'right',
                sortable: false,
            }
        ];

        return {
            gridColumns,
            gridRows: [],
            filter: '',
            loading: false,
            pagination: {
                sortBy: 'id',
            }
        }
    },
    methods: {
        onRequest({pagination}) {
            const url = new URL(this.urlFetch);
            url.searchParams.append('page', pagination.page);
            url.searchParams.append('rowsPerPage', pagination.rowsPerPage);
            url.searchParams.append('sortBy', pagination.sortBy);
            url.searchParams.append('descending', pagination.descending);

            this.loading = true;
            axios.get(url.toString())
                .then(({data}) => {
                    const {items, rowsNumber} = data;

                    this.gridRows = items;
                    this.pagination = {
                        ...pagination,
                        rowsNumber,
                    };
                    this.loading = false
                })
        },
        onEdit(props) {
            const {row} = props;
            router.get(this.urlForm.replace(':id', row.id));
        },
        confirmDelete(props) {
            const {row} = props;

            this.$q.dialog({
                title: 'Confirm',
                message: 'Would you like to remove this item?',
                cancel: true,
                persistent: true,
            }).onOk(() => {
                axios.delete(this.urlDelete.replace(':id', row.id))
                    .then(response => {
                        this.$refs.tableElement.requestServerInteraction();
                        return this.$q.dialog({
                            message: 'Item was removed.',
                            color: 'success',
                            seamless: true,
                            position: "bottom",
                        })
                    })
                    .catch(error => {
                        return this.$q.dialog({
                            message: 'Error occur while delete item.',
                            color: 'error',
                            seamless: true,
                            position: "bottom",
                        })
                    })
            });
        }
    },
    mounted() {
      this.$refs.tableElement.requestServerInteraction();
    }
}
</script>
