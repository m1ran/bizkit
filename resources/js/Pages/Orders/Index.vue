<script setup>
import { ref, shallowRef, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import OrderForm from './Partials/OrderForm.vue';
import OrderTable from './Partials/OrderTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import HistoryModal from '@/Components/Modals/HistoryModal.vue';
import DestroyModal from '@/Components/Modals/DestroyModal.vue';
import ApplyChangesModal from '@/Components/Modals/ApplyChangesModal.vue';

defineProps({
    orders: {
        type: Object
    },
    filters: {
        type: Object
    }
});

const form = useForm({
    first_name     : '',
    last_name      : '',
    address        : '',
    phone          : '',
    items          : [],
    notes          : '',
    finished       : false,
    customer       : null,
    customer_id    : null,
    customer_action: 'keep', // 'keep', 'update', 'create'
});

const checkOnly = ['first_name', 'last_name', 'address', 'phone'];

const customerDataIsDirty = computed(() => {
    let dirty = false;

    if (form.customer?.id) {
        checkOnly.forEach(key => {
            if (form[key] !== form.customer[key]) {
                dirty = true;
            }
        });
    }

    return dirty;
});

const formTitle = computed(() => {
    return `${order.value ? 'Update' : 'New'} Order`;
});

const order = ref(null);
const orderId = shallowRef(0);
const orderNum = shallowRef(null);
const showFormModal = shallowRef(false);
const showConfirmationModal = shallowRef(false);
const showApplyChangesModal = shallowRef(false);

const onOpenOrderFormModal = (data = null) => {
    order.value = data;

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

const onShowOrderHistory = (data) => {
    orderId.value = data.id;
    orderNum.value = data.num;
}

const onConfirmOrderModal = (data) => {
    order.value = data;
    showConfirmationModal.value = true;
}

const closeModals = () => {
    orderId.value = 0;
    showFormModal.value = false;
    showConfirmationModal.value = false;
};

const closeApplyChangesModal = () => {
    showApplyChangesModal.value = false;
};

const applyCustomerChanges = (action) => {
    showApplyChangesModal.value = false;
    form.customer_action = action;

    if (action === 'create') {
        form.customer = null;
        form.customer_id = null;
    }

    createOrder();
};

const formOptions = {
    preserveScroll: true,
    onSuccess: () => {
        closeModals();
        form.reset();
    }
}

const saveOrder = () => {
    if (order.value) {
        form.post(route('orders.update', { id: order.value.id }), formOptions);
    } else {
        if (customerDataIsDirty.value) {
            return showApplyChangesModal.value = true;
        }

        createOrder();
    }
};

const createOrder = () => {
    form.post(route('orders.store'), formOptions);
}

const deleteOrder = () => {
    form.delete(route('orders.delete', { id: order.value.id }), formOptions);
}
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <OrderTable
                @open="onOpenOrderFormModal"
                @history="onShowOrderHistory"
                @delete="onConfirmOrderModal"
                :orders="orders"
                :filters="filters"
            />

            <!-- Entity History Modal  -->
            <HistoryModal v-if="orderId"
                entity="order"
                :id="orderId"
                :num="orderNum"
                @close="closeModals"
            />

            <!-- Apply Customer Changes Modal -->
            <ApplyChangesModal
                v-if="customerDataIsDirty"
                :show="showApplyChangesModal"
                :customer-name="form.customer ? `${form.customer.first_name} ${form.customer.last_name}` : ''"
                @close="closeApplyChangesModal"
                @apply="applyCustomerChanges"
            />

            <!-- Delete Entity Confirmation Modal -->
            <DestroyModal
                entity="order"
                :show="showConfirmationModal"
                :processing="form.processing"
                @close="closeModals"
                @confirmed="deleteOrder"
            />

            <!-- Create/Update Order Modal -->
            <DialogModal :show="showFormModal" @close="closeModals">
                <template #title>{{ formTitle }}</template>
                <template #content>
                    <OrderForm v-model:form="form" @submitted="saveOrder" />
                </template>
                <template #footer>
                    <SecondaryButton @click="closeModals">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        @click="saveOrder"
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
    h(AppLayout, { title: 'Orders' }, {
        default: () => page
    }),
};
</script>
