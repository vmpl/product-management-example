<template>
    <CrudLayout title="Form | Crud" :list="models">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Form | Crud
            </h2>
        </template>

        <q-form @submit.prevent="submit">
            <template v-for="field in fields" :key="field.name">
                <component :is="field.component.type"
                           v-model="form[field.name]"
                           v-bind="{
                               name: field.name,
                               label: field.label,
                               ...field.component.props
                           }"/>
            </template>
            <q-btn label="Submit" type="submit" color="primary"/>
        </q-form>
    </CrudLayout>
</template>
<script>
import CrudLayout from "../../Layouts/CrudLayout.vue";
import {router} from "@inertiajs/vue3";
import {QInput} from "quasar";

export default {
    props: {
        models: Array,
        fields: Array,
        postUrl: String,
    },
    components: {
        CrudLayout,
        QInput,
    },
    data() {
        const form = Object.fromEntries(this.fields.map(it => [it.name, undefined]));
        return {
            form,
        }
    },
    methods: {
        submit() {
            router.post(this.postUrl, this.form);
        }
    }
}
</script>
