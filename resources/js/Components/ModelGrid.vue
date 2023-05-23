<template>
    <q-table
        ref="tableElement"
        :columns="gridColumns"
        :rows="gridRows"
        :loading="loading"
        :pagination="pagination"
        row-key="id"
        :selection="selection"
        v-model:selected="value"
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
                    <strong>{{ col.label }}</strong>
                    <q-btn
                        v-if="filter.hasOwnProperty(col.field)"
                        :icon="filter[col.field] ? 'filter_alt' : 'search'"
                        flat
                        round
                        @click.prevent.stop
                    >
                        <q-popup-edit v-model="filter[col.field]" auto-save v-slot="scope">
                            <q-input
                                v-model="scope.value"
                                dense
                                autofocus
                                clearable
                                @keyup.enter="scope.set"
                            />
                        </q-popup-edit>
                    </q-btn>
                </q-th>
            </q-tr>
        </template>

        <template v-slot:body-cell="props">
            <q-td :props="props">
                <template v-if="props.col.component.type === 'inline'">
                    {{props.row[props.col.field]}}
                </template>
                <template v-else-if="props.col.component.type === 'q-img'">
                    <q-img v-if="props.row[props.col.field]"
                        :src="props.row[props.col.field].download_url"
                        loading="lazy"
                        :width="props.col.component.props.width"
                        :height="props.col.component.props.height"
                        fit="cover"
                    />
                    <q-badge v-else label="None"/>
                </template>
                <template v-else>
                    {{props.col.field}}
                    {{Object.keys(props.row)}}
                    {{props.col.component}}
                </template>
            </q-td>
        </template>
        <template v-slot:body-cell-actions="props">
            <q-td auto-width class="q-gutter-x-sm">
                <q-btn icon="edit" @click.stop="() => onEdit(props)"/>
                <q-btn icon="delete" @click.stop="() => confirmDelete(props)"/>
            </q-td>
        </template>
    </q-table>
</template>
<script lang="ts">
import {router} from "@inertiajs/vue3";
import axios from "axios";
import {PropType} from "vue";

export default {
    props: {
        size: Number,
        columns: Array<Object>,
        urlFetch: String,
        urlForm: String,
        urlDelete: String,
        selection: Object as PropType<"single" | "multiple" | "none" | undefined>,
        modelValue: {
            type: Array<Object>,
            default: [],
        },
    },
    emits: ['update:modelValue'],
    computed: {
        value: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        gridColumns() {
            const columns = this.columns;

            !this.urlForm && !this.urlDelete
                || columns.push({
                    name: 'actions',
                    label: 'Actions',
                    required: false,
                    align: 'right',
                    sortable: false,
                });

            return columns;
        },
    },
    data() {
        return {
            gridRows: [],
            filter: Object.fromEntries(this.columns.map(it => {
                if (!it.searchable) {
                    return null;
                }

                return [it.field, '']
            }).filter(it => it !== null)),
            loading: false,
            pagination: {
                sortBy: 'id',
                rowsPerPage: this.size,
            },
            watchers: [],
        }
    },
    methods: {
        onRequest({pagination}) {
            const urlRequest = new URL(this.urlFetch);
            urlRequest.searchParams.append('page', pagination.page);
            urlRequest.searchParams.append('rowsPerPage', pagination.rowsPerPage);
            urlRequest.searchParams.append('sortBy', pagination.sortBy);
            urlRequest.searchParams.append('descending', pagination.descending);
            urlRequest.searchParams.append('filter', JSON.stringify(this.filter));

            this.loading = true;
            axios.get(urlRequest.toString())
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
        },
    },
    mounted() {
        this.$refs.tableElement.requestServerInteraction();
        Object.keys(this.filter).forEach(name => {
            this.watchers.push(this.$watch(`filter.${name}`,
                () => this.$refs.tableElement.requestServerInteraction()));
        });
    },
    unmounted() {
        this.watchers.forEach(it => it());
    }
}
</script>
