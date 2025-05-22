<script setup>
import { ref, shallowRef } from 'vue';
import { defineProps, watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import THead from '@/Components/THead.vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            q: '',
        }),
    },
});

const headers = shallowRef([
    'ID',
    'Name',
    'SKU',
    'Price',
    'Quantity',
    'Description',
    'Actions',
]);

const queryOptions = { preserveState: true, replace: true, only: ['products'] };

const emit = defineEmits(['open', 'history', 'delete']);

const search = debounce ((value) => {
    router.get(route('products.index'), { q: value }, queryOptions);
}, 500);

const q = ref(props.filters.q);

watch(q, newValue => {
    search(newValue);
});
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="py-6 px-4 max-w-7xl mx-auto">
            <div class="mb-4 flex justify-between items-center">
                <TextInput
                    id="product-search"
                    v-model="q"
                    type="text"
                    class="block w-1/3 mt-1"
                    autofocus
                    placeholder="Search products..."
                />

                <PrimaryButton @click="$emit('open')">
                    + Add Product
                </PrimaryButton>
            </div>

            <div class="bg-white shadow-sm rounded overflow-x-auto">
                <table class="min-w-full table-auto border">
                    <THead :headers="headers" />

                    <tbody>
                        <template v-if="products.meta.total">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-100 even:bg-gray-50">
                                <td class="px-4 py-2 border">{{ product.id }}</td>
                                <td class="px-4 py-2 border">{{ product.name }}</td>
                                <td class="px-4 py-2 border">{{ product.sku }}</td>
                                <td class="px-4 py-2 border">{{ product.price }}</td>
                                <td class="px-4 py-2 border">{{ product.quantity }}</td>
                                <td class="px-4 py-2 border">{{ product.description }}</td>
                                <td class="px-4 py-2 border">
                                    <button
                                        class="text-sm text-blue-400 underline"
                                        @click="$emit('open', product)"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        class="ml-3 text-sm text-gray-400 underline"
                                        @click="$emit('history', product.id)"
                                    >
                                        History
                                    </button>

                                    <button
                                        class="cursor-pointer ml-3 text-sm text-red-500"
                                        @click="$emit('delete', product)"
                                    >
                                        Remove
                                    </button>
                                </td>
                                </tr>
                        </template>
                        <tr v-else>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="products.meta.links" class="mt-4" />
        </div>
    </div>
</template>
