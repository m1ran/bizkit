<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import Autocomplete from '@/Components/Autocomplete.vue';
import { watch } from 'vue';
import { useCategoryAutocomplete } from '../composables/useCategoryAutocomplete';

const { form, categories } = defineProps({
    form: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const {
    query: categoryQuery,
    selectedCategory,
    searchCategory,
    categoryDisplay
} = useCategoryAutocomplete(categories);

// Sync selectedCategory with form.category
watch(
    () => form.category,
    (val) => {
        selectedCategory.value = val;
    },
    { immediate: true }
);

watch(selectedCategory, (c) => {
    if (c) {
        form.category_id = c.id;
        form.category = c;
    } else {
        form.category_id = null;
        form.category = null;
    }
});

const emits = defineEmits(['submitted']);
</script>

<template>
    <form @submit.prevent="$emit('submitted')">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="name" value="Name" required />
                <TextInput
                    id="product-name"
                    v-model="form.name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="sku" value="SKU #" required />
                <TextInput
                    id="product-sku"
                    v-model="form.sku"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.sku" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="product-category-autocomplete" value="Category" />
                <Autocomplete
                    id="product-category-autocomplete"
                    :search-fn="searchCategory"
                    :display="categoryDisplay"
                    v-model="selectedCategory"
                    :default-suggestions="categories"
                    placeholder="Choose category..."
                    class="mt-1"
                />
                <InputError :message="form.errors.category_id" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="cost" value="Cost" required />
                <TextInput
                    id="product-cost"
                    v-model="form.cost"
                    type="number"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.cost" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="price" value="Price" required />
                <TextInput
                    id="product-price"
                    v-model="form.price"
                    type="number"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.price" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="quantity" value="Quantity" required />
                <TextInput
                    id="product-quantity"
                    v-model="form.quantity"
                    type="number"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.quantity" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="description" value="Description" />
                <TextAreaInput
                    id="product-description"
                    v-model="form.description"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.description" class="mt-2" />
            </div>
        </div>

        <button type="submit" class="hidden" />
    </form>
</template>
