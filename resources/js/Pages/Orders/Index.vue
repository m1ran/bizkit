<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import NProgress from 'nprogress';
import OrderForm from './Partials/OrderForm.vue';
import OrderTable from './Partials/OrderTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import HistoryModal from '@/Components/Modals/HistoryModal.vue';
import DestroyModal from '@/Components/Modals/DestroyModal.vue';
import ApplyChangesModal from '@/Pages/Orders/Partials/ApplyChangesModal.vue';

const props = defineProps({
    orders: {
        type: Object
    },
    statuses: {
        type: Object
    },
    filters: {
        type: Object
    }
});

const form = useForm({
    id             : null,
    first_name     : '',
    last_name      : '',
    address        : '',
    phone          : '',
    items          : [],
    notes          : '',
    finished       : false,
    customer       : null,
    customer_id    : null,
    status_id      : null,
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
    return order.value ? `Update Order #${order.value.num}` : 'New Order';
});

const order = ref(null);
const showFormModal = ref(false);
const showHistoryModal = ref(false);
const showConfirmationModal = ref(false);
const showApplyChangesModal = ref(false);

const onOpenOrderFormModal = async (data = null) => {
    order.value = data;

    if (data?.id) {
        NProgress.start();

        try {
            form.id = data.id;
            const { data: orderData } = await axios.get(`/api/orders/${data.id}`);
            // Assume the response contains the order object
            Object.entries(orderData).forEach(([key, val]) => {
                if (key in form) form[key] = val;
            });
        } catch (error) {
            // TODO: handle error when fetching order details
            console.error('Error fetching order details:', error);
        } finally {
            NProgress.done();
        }
    } else {
        form.reset();
    }

    form.clearErrors();
    showFormModal.value = true;
};

const onShowOrderHistory = (data) => {
    order.value = data;
    showHistoryModal.value = true;
}

const onConfirmOrderModal = (data) => {
    order.value = data;
    showConfirmationModal.value = true;
}

const closeModals = () => {
    order.value = null;
    showFormModal.value = false;
    showHistoryModal.value = false;
    showConfirmationModal.value = false;
    showApplyChangesModal.value = false;
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

    if (order.value) {
        updateOrder();
    } else {
        createOrder();
    }
};

const formOptions = {
    preserveScroll: true,
    onSuccess: () => {
        closeModals();
        form.reset();
    }
}

const saveOrder = () => {
    if (customerDataIsDirty.value) {
        return showApplyChangesModal.value = true;
    }

    if (order.value) {
        updateOrder();
    } else {
        createOrder();
    }
};

const createOrder = () => {
    form.post(route('orders.store'), formOptions);
}

const updateOrder = () => {
    form.put(route('orders.update', { id: order.value.id }), formOptions);
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
                :statuses="statuses.data"
                :filters="filters"
            />

            <!-- Entity History Modal  -->
            <HistoryModal v-if="order && showHistoryModal"
                entity="order"
                :id="order.id"
                :num="order.num"
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
                    <OrderForm v-model:form="form" :statuses="statuses.data" @submitted="saveOrder" />
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
