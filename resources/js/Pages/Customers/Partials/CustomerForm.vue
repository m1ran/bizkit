<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';

const { form } = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emits = defineEmits(['submitted']);

const onPhoneFocus = () => {
    if (!form.phone) {
        form.phone = '+';
    }
};

const onPhoneBlur = () => {
    if (form.phone === '+') {
        form.phone = '';
    }
};
</script>

<template>
    <form @submit.prevent="$emit('submitted')">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="customer-first-name"
                    v-model="form.first_name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="customer-last-name"
                    v-model="form.last_name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div>

            <div v-if="$page.props.locale !== 'en'" class="col-span-6 sm:col-span-3">
                <InputLabel for="patronymic_name" value="Patronymic Name" />
                <TextInput
                    id="customer-patronymic-name"
                    v-model="form.patronymic_name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.patronymic_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="phone" value="Phone" />
                <TextInput
                    id="customer-phone"
                    v-model="form.phone"
                    type="text"
                    class="block w-full mt-1"
                    @focus="onPhoneFocus"
                    @blur="onPhoneBlur"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="name" value="Email" />
                <TextInput
                    id="customer-email"
                    v-model="form.email"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="notes" value="Notes" />
                <TextAreaInput
                    id="customer-notes"
                    v-model="form.notes"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.notes" class="mt-2" />
            </div>
        </div>

        <button type="submit" class="hidden" />
    </form>
</template>
