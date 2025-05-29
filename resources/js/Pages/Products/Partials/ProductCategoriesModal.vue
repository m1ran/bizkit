<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { reactive, ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => []
    },
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const processing = ref(false);

const category = reactive({
    id: null,
    name: '',
    errors: ''
});

const saveCategory = async () => {
    if (processing.value) return;

    if (!category.name || category.name.trim().length < 2) {
        category.errors = 'The Name is required and should be at least 2 characters long.';
        return;
    }

    processing.value = true;
    const url  = '/api/product-categories' + (category.id ? `/${category.id}` : '');

    await axios.post(url, {
        name: category.name,
    }).then(response => {
        let newCategory = response.data.data;
        // push new category to the top of list
        if (category.id) {
            const index = props.categories.findIndex(c => c.id === category.id);
            if (index !== -1) {
                props.categories[index] = newCategory;
            }
        } else {
            props.categories.unshift(newCategory);
        }
        resetCategory();
    })
    .catch(error => {
        category.errors = error.response?.data?.message || 'An unexpected error occurred.';        })
    .finally(() => {
        processing.value = false;
    });
}

const deleteCategory = async (item) => {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) return;

    if (processing.value) return;

    if (!item.id) {
        category.errors = 'An unexpected error occurred.';
        return;
    }

    processing.value = true;
    const url  = `/api/product-categories/${item.id}`;

    await axios.delete(url).then(response => {
        const index = props.categories.findIndex(c => c.id === item.id);
        if (index !== -1) {
            props.categories.splice(index, 1);
        }
        resetCategory();
    })
    .catch(error => {
        category.errors = error.response?.data?.message || 'An unexpected error occurred.';        })
    .finally(() => {
        processing.value = false;
    });
}

const fillCategory = (item) => {
    category.id = item.id;
    category.name = item.name;
    category.errors = '';
}

const resetCategory = () => {
    category.id = null;
    category.name = '';
    category.errors = '';
}
</script>

<template>
    <DialogModal :show="show" @close="$emit('close')">
        <template #title>Categories</template>
        <template #content>
            <div id="product-categories-wrapper">
                <div>
                    <form @submit.prevent="saveCategory">
                        <div class="flex items-center">
                            <TextInput
                                id="category-name"
                                v-model="category.name"
                                type="text"
                                class="block w-1/3"
                                placeholder="Category name"
                            />
                            <div class="ml-2">
                                <PrimaryButton type="submit" @click="saveCategory" :disabled="processing">
                                    {{ category.id ? 'Update' : '+ Add' }} category
                                </PrimaryButton>

                                <SecondaryButton v-if="category.id" @click="resetCategory" class="ml-2"  :disabled="processing">
                                    Cancel
                                </SecondaryButton>
                            </div>
                        </div>
                        <div>
                            <InputError :message="category.errors" class="mt-2" />
                        </div>
                    </form>

                    <table class="min-w-full table-auto border-collapse mt-2">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, idx) in categories" :key="idx" class="border-t">
                                <td class="px-4 py-2">
                                    {{ item.name }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button
                                        class="text-sm text-blue-400 underline"
                                        @click="fillCategory(item)"
                                    >
                                        Edit
                                    </button>

                                    <button type="button" @click="deleteCategory(item)" class="text-red-500 ml-3">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!categories.length">
                                <td colspan="2" class="px-4 py-2 text-center text-gray-500">No categories added.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template #footer>
            <SecondaryButton @click="$emit('close')">
                Close
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
