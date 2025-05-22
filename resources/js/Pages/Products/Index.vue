<script setup>
import { ref, shallowRef, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ProductForm from './Partials/ProductForm.vue';
import ProductTable from './Partials/ProductTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import HistoryModal from '@/Components/Modals/HistoryModal.vue';
import DestroyModal from '@/Components/Modals/DestroyModal.vue';

defineProps({
    products: {
        type: Object
    },
    filters: {
        type: Object
    }
})

const form = useForm({
    name: '',
    sku: '',
    price: 0.01,
    quantity: 1,
    description: '',
});

const formTitle = computed(() => {
    return `${product.value ? 'Update' : 'New'} Product`;
})

const product = ref(null);
const productId = shallowRef(0);
const showFormModal = shallowRef(false);
const showConfirmationModal = shallowRef(false);

const onOpenProductFormModal = (data = null) => {
    product.value = data;

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

const onShowProductHistory = (id) => {
    productId.value = id;
}

const onConfirmProductModal = (data) => {
    product.value = data;
    showConfirmationModal.value = true;
}

const closeModals = () => {
    productId.value = 0;
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

const saveProduct = () => {
    if (product.value) {
        form.post(route('products.update', { id: product.value.id }), formOptions);
    } else {
        form.post(route('products.store'), formOptions);
    }
};

const deleteProduct = () => {
    form.delete(route('products.delete', { id: product.value.id }), formOptions);
}
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <ProductTable
                @open="onOpenProductFormModal"
                @history="onShowProductHistory"
                @delete="onConfirmProductModal"
                :products="products"
                :filters="filters"
            />

            <!-- Entity History Modal  -->
            <HistoryModal v-if="productId"
                entity="product"
                :id="productId"
                @close="closeModals"
            />

            <!-- Delete Entity Confirmation Modal -->
            <DestroyModal
                entity="product"
                :show="showConfirmationModal"
                :processing="form.processing"
                @close="closeModals"
                @confirmed="deleteProduct"
            />

            <!-- Create/Update Product Modal -->
            <DialogModal :show="showFormModal" @close="closeModals">
                <template #title>{{ formTitle }}</template>
                <template #content>
                    <ProductForm v-model:form="form" @submitted="saveProduct" />
                </template>
                <template #footer>
                    <SecondaryButton @click="closeModals">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        @click="saveProduct"
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
    h(AppLayout, { title: 'Products' }, {
        default: () => page
    }),
};
</script>
