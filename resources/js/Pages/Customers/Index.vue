<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CustomerForm from './Partials/CustomerForm.vue';
import CustomerTable from './Partials/CustomerTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';

const form = useForm({
    first_name: '',
    last_name: '',
    patronymic_name: '',
    phone: '',
    email: '',
    notes: '',
});

const formTitle = computed(() => {
    return customer.value ? 'Update Customer' : 'New Customer';
})

const customer = ref(null);
const showFormModal = ref(false);
const showConfirmationModal = ref(false);

const onOpenCustomerFormModal = (data = null) => {
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

const  onConfirmCustomerModal = (data) => {
    customer.value = data;
    showConfirmationModal.value = true;
}

const closeModals = () => {
    showFormModal.value = false;
    showConfirmationModal.value = false;
};

const formOptions = {
    preserveScroll: true,
    onSuccess: () => {
        closeModals();
        form.reset();
    }
}

const saveCustomer = () => {
    if (customer.value) {
        form.post(route('customers.update', { id: customer.value.id }), formOptions);
    } else {
        form.post(route('customers.store'), formOptions);
    }
};

const deleteCustomer = () => {
    form.delete(route('customers.delete', { id: customer.value.id }), formOptions);
}
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <CustomerTable
                @open="onOpenCustomerFormModal"
                @delete="onConfirmCustomerModal"
                :customers="$page.props.customers"
                :filters="$page.props.filters"
            />
            <!-- Create/Update Customer Modal -->
            <DialogModal :show="showFormModal" @close="closeModals">
                <template #title>{{ formTitle }}</template>
                <template #content>
                    <CustomerForm v-model:form="form" @submitted="saveCustomer" />
                </template>
                <template #footer>
                    <SecondaryButton @click="closeModals">
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

            <!-- Delete Customer Confirmation Modal -->
            <ConfirmationModal :show="showConfirmationModal" @close="closeModals">
                <template #title>
                    Delete Customer
                </template>

                <template #content>
                    Are you sure you want to delete this customer? Once a customer is deleted, all of its resources and data will be permanently deleted.
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModals">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteCustomer"
                    >
                        Delete Customer
                    </DangerButton>
                </template>
            </ConfirmationModal>
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
