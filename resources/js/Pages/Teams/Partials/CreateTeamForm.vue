<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
});

const createTeam = () => {
    form.post(route('teams.store'), {
        errorBag: 'createTeam',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="createTeam">
        <template #title>
            Team Details
        </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="tw-col-span-6">
                <InputLabel value="Team Owner" />

                <div class="tw-flex tw-items-center tw-mt-2">
                    <img class="tw-object-cover tw-w-12 tw-h-12 tw-rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">

                    <div class="tw-ml-4 tw-leading-tight">
                        <div class="tw-text-gray-900 dark:tw-text-white">{{ $page.props.auth.user.name }}</div>
                        <div class="tw-text-sm tw-text-gray-700 dark:tw-text-gray-300">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="tw-col-span-6 sm:tw-col-span-4">
                <InputLabel for="name" value="Team Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="tw-block tw-w-full tw-mt-1"
                    autofocus
                />
                <InputError :message="form.errors.name" class="tw-mt-2" />
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'tw-opacity-25': form.processing }" :disabled="form.processing">
                Create
            </PrimaryButton>
        </template>
    </FormSection>
</template>
