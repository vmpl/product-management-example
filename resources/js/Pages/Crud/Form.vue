<template>
    <CrudLayout title="Form | Crud" :list="models">
        <template #header>
            <h2 class="tw-font-semibold tw-text-xl tw-text-gray-800 dark:tw-text-gray-200 tw-leading-tight">
                {{ $t('Form | Crud') }}
            </h2>
        </template>

        <q-form class="q-px-md q-gutter-y-lg" @submit.prevent="submit">
            <template v-for="field in fields" :key="field.name">
                <component :is="field.component.type"
                           v-model="form[field.name]"
                           v-bind="{
                               name: field.name,
                               label: field.label,
                               ...field.component.props
                           }"
                           :rules="[val => rules(field.name, val)]"
                           lazy-rules
                />
            </template>
            <q-btn label="Submit" type="submit" color="teal-10"/>
        </q-form>
    </CrudLayout>
</template>
<script lang="ts">
import CrudLayout from "../../Layouts/CrudLayout.vue";
import SelectChildren from "../../Components/SelectChildren.vue";
import {router} from "@inertiajs/vue3";
import {QInput} from "quasar";
import axios from "axios";

export default {
    props: {
        models: Array,
        fields: Array,
        postUrl: String,
        current: Array
    },
    components: {
        CrudLayout,
        QInput,
        SelectChildren,
    },
    data() {
        const form = Object.fromEntries(this.fields.map(it => [it.name, this.current[it.name]]));
        return {
            form,
        }
    },
    methods: {
        rules(fieldName, value) {
            const data = Object.fromEntries([[fieldName, value ?? '']]);
            return axios.post(this.postUrl, data, {headers: {
                'Precognition': true,
                'Precognition-Validate-Only': fieldName
            }})
                .then(response => true)
                .catch((error) => {
                    const {response} = error;
                    if (response.status === 422) {
                        return response.data.message;
                    }

                    throw new error;
                })
        },
        submit() {
            router.post(this.postUrl, this.form);
        }
    }
}
</script>
