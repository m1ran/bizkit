<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ProductForm from './Partials/ProductForm.vue';
import ProductTable from './Partials/ProductTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import HistoryModal from '@/Components/Modals/HistoryModal.vue';
import DestroyModal from '@/Components/Modals/DestroyModal.vue';
import ProductCategoriesModal from './Partials/ProductCategoriesModal.vue';
import { getItemById } from '@/helpers';

const props = defineProps({
    products: {
        type: Object
    },
    categories: {
        type: Object,
    },
    filters: {
        type: Object
    }
});

const form = useForm({
    name: '',
    sku: '',
    cost: 0.00,
    price: 0.01,
    quantity: 1,
    category_id: null,
    category: null,
    description: '',
});

const formTitle = computed(() => {
    return `${product.value ? 'Update' : 'New'} Product`;
});

const product = ref(null);
const productId = ref(0);
const showFormModal = ref(false);
const showCategoriesModal = ref(false);
const showConfirmationModal = ref(false);

const onOpenProductFormModal = (data = null) => {
    product.value = data;

    if (data) {
        Object.entries(data).forEach(([key, val]) => {
            if (key in form) form[key] = val
        });
        // set category record
        if (form.category_id) {
            form.category = getItemById(form.category_id, props.categories.data);
        }


    } else {
        form.reset();
    }

    form.clearErrors();
    showFormModal.value = true;
};

const onShowProductHistory = (data) => {
    productId.value = data.id;
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
    // set category_id from category object
    if (form.category) {
        form.category_id = form.category.id;
    } else {
        form.category_id = null;
    }
    // save product
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
                @open:categories="showCategoriesModal = true"
                :products="products"
                :filters="filters"
                :categories="categories.data"
            />

            <!-- Entity History Modal  -->
            <HistoryModal v-if="productId"
                entity="product"
                :id="productId"
                :num="product.sku"
                @close="closeModals"
            />

            <!-- Product Categories Modal -->
            <ProductCategoriesModal
                :categories="categories.data"
                :show="showCategoriesModal"
                @close="showCategoriesModal = false"
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
                    <ProductForm v-model:form="form" :categories="categories.data" @submitted="saveProduct" />
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
