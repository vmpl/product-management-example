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
                :columns="columns"
                :rows="rows"
                :loading="loading"
                :pagination="pagination"
                :filter="filter"
                row-key="id"
                separator="vertical"
                @request="onRequest"
                @row-click="onRowClick"
            />
        </div>
    </CrudLayout>
</template>
<script>
import CrudLayout from "../../Layouts/CrudLayout.vue";
import {router} from "@inertiajs/vue3";

export default {
    props: {
        models: Array,
        size: Number,
        columns: Array,
        fetchUrl: String,
        formUrl: String,
    },
    components: {
        CrudLayout
    },
    data() {
        return {
            rows: [],
            filter: '',
            loading: false,
            pagination: {
                sortBy: 'id',
            }
        }
    },
    methods: {
        onRequest({pagination}) {
            const url = new URL(this.fetchUrl);
            url.searchParams.append('page', pagination.page);
            url.searchParams.append('rowsPerPage', pagination.rowsPerPage);
            url.searchParams.append('sortBy', pagination.sortBy);
            url.searchParams.append('descending', pagination.descending);

            this.loading = true;
            fetch(url.toString())
                .then(response => response.json())
                .then(content => {
                    const {items, rowsNumber} = content;

                    this.rows = items;
                    this.pagination = {
                        ...pagination,
                        rowsNumber,
                    };
                })
                .finally(() => {
                    this.loading = false
                })
        },
        onRowClick(event, row, index) {
            router.get(this.formUrl.replace(':id', row.id));
        }
    },
    mounted() {
      this.$refs.tableElement.requestServerInteraction();
    }
}
</script>
