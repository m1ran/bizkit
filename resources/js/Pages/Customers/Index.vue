<script setup>
import { onErrorCaptured, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CustomerForm from './Partials/CustomerForm.vue';
import CustomerTable from './Partials/CustomerTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const form = useForm({
    first_name: '',
    last_name: '',
    patronymic_name: '',
    phone: '',
    email: '',
    notes: '',
});

const customer = ref(null);
const showFormModal = ref(false);

const openModal = (data = null) => {
    customer.value = data;

    if (data) {
        Object.entries(data).forEach(([key, val]) => {
            if (key in form) form[key] = val
        });
    } else {
        form.reset();
    }

    form.clearErrors();
    showFormModal.value = true;
};

const closeModal = () => {
    showFormModal.value = false;
};

const formOptions = {
    preserveScroll: true,
    onSuccess: () => {
        closeModal();
        form.reset();
    },
    onError: (error) => {
        alert('dfdsfsdf');
    }
}

const saveCustomer = () => {
    if (customer.value) {
        form.post(route('customers.update', { id: customer.value.id }), formOptions);
    } else {
        form.post(route('customers.store'), formOptions);
    }
};
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- <CustomerForm />

            <SectionBorder /> -->

            <CustomerTable
                @open="openModal"
                :customers="$page.props.customers"
                :filters="$page.props.filters"
            />

            <DialogModal :show="showFormModal" @close="closeModal">
                <template #title>New Customer</template>
                <template #content>
                    <CustomerForm v-model:form="form" @submitted="saveCustomer" />
                </template>
                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        @click="saveCustomer"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="ms-3"
                    >
                        Save
                    </PrimaryButton>
                </template>
            </DialogModal>
        </div>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  layout: (h, page) =>
    h(AppLayout, { title: 'Customers' }, {
        default: () => page
    }),
};
</script>
