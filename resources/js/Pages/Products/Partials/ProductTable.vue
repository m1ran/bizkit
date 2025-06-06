<script setup>
import { ref } from 'vue';
import { watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import THead from '@/Components/THead.vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { getItemById } from '@/helpers';
import Autocomplete from '@/Components/Autocomplete.vue';
import { useCategoryAutocomplete } from '../composables/useCategoryAutocomplete';

const HEADERS = [
    'SKU #',
    'Name',
    'Category',
    'Cost',
    'Price',
    'Quantity',
    'Description',
    'Actions',
];

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
        default: [],
    },
    filters: {
        type: Object,
        default: () => ({
            q: '',
        }),
    },
});

const queryOptions = { preserveState: true, replace: true, only: ['products'] };

const emit = defineEmits(['open', 'history', 'delete', 'open:categories']);

const searchDebounce = debounce (() => {
    updateFilters();
}, 500);

const q = ref(props.filters.q);

const {
    query: categoryQuery,
    selectedCategory,
    searchCategory,
    categoryDisplay
} = useCategoryAutocomplete(props.categories);

const selectedCategoryId = ref(props.filters.category || null);

// Set selectedCategory from filter on page load
if (props.filters.category) {
    const initialCategory = props.categories.find(
        c => c.id === Number(props.filters.category)
    );
    if (initialCategory) {
        selectedCategory.value = initialCategory;
    }
}

watch(q, newValue => {
    searchDebounce();
});

watch(selectedCategory, (cat) => {
    selectedCategoryId.value = cat ? cat.id : null;
    updateFilters();
});

const updateFilters = () => {
    router.get(route('products.index'), {
        q: q.value,
        category: selectedCategoryId.value,
    }, queryOptions);
}
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="py-6 px-4 max-w-7xl mx-auto">
            <div class="mb-4 flex justify-between items-center">
                <div class="flex gap-4 w-2/3">
                    <TextInput
                        id="product-search"
                        v-model="q"
                        type="text"
                        class="block w-1/3 mt-1"
                        autofocus
                        placeholder="Search products..."
                    />
                    <Autocomplete
                        id="product-category-filter"
                        :search-fn="searchCategory"
                        :display="categoryDisplay"
                        v-model="selectedCategory"
                        :default-suggestions="props.categories"
                        placeholder="Filter by category..."
                        class="mt-1 w-1/3"
                    />
                </div>
                <div class="flex">
                    <SecondaryButton @click="$emit('open:categories')">
                        <FontAwesomeIcon icon="th-list" class="mr-2" />
                        Categories
                    </SecondaryButton>

                    <PrimaryButton @click="$emit('open')"  class="ml-2">
                        +  Add Product
                    </PrimaryButton>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded overflow-x-auto">
                <table class="min-w-full table-auto border">
                    <THead :headers="HEADERS" />

                    <tbody>
                        <template v-if="products.meta.total">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-100 even:bg-gray-50">
                                <td class="px-4 py-2 border">{{ product.sku }}</td>
                                <td class="px-4 py-2 border">{{ product.name }}</td>
                                <td class="px-4 py-2 border">
                                    {{ getItemById(product.category_id, props.categories, 'name') }}
                                </td>
                                <td class="px-4 py-2 border">{{ ( product.cost ?? 0) }}</td>
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
                                        @click="$emit('history', product)"
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
                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="products.meta.links" class="mt-4" />
        </div>
    </div>
</template>
