<script setup>
import { ref } from 'vue';
import { defineProps, watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import THead from '@/Components/THead.vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const HEADERS = [
    '#',
    'Name',
    'Email',
    'Phone',
    'Notes',
    'Actions',
];

const props = defineProps({
    customers: {
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

const queryOptions = { preserveState: true, replace: true, only: ['customers'] };

const emit = defineEmits(['open', 'history', 'delete']);

const search = debounce ((value) => {
    router.get(route('customers.index'), { q: value }, queryOptions);
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
                    id="customer-search"
                    v-model="q"
                    type="text"
                    class="block w-1/3 mt-1"
                    autofocus
                    placeholder="Search customers..."
                />

                <PrimaryButton @click="$emit('open')">
                    + Add Customer
                </PrimaryButton>
            </div>

            <div class="bg-white shadow-sm rounded overflow-x-auto">
                <table class="min-w-full table-auto border">
                    <THead :headers="HEADERS" />

                    <tbody>
                        <template v-if="customers.meta.total">
                            <tr v-for="(customer, key) in customers.data" :key="customer.id" class="hover:bg-gray-100 even:bg-gray-50">
                                <td class="px-4 py-2 border">{{ key + 1 }}</td>
                                <td class="px-4 py-2 border">{{ customer.first_name + ' ' + customer.last_name }}</td>
                                <td class="px-4 py-2 border">{{ customer.email }}</td>
                                <td class="px-4 py-2 border">{{ customer.phone }}</td>
                                <td class="px-4 py-2 border">{{ customer.notes }}</td>
                                <td class="px-4 py-2 border">
                                    <button
                                        class="text-sm text-blue-400 underline"
                                        @click="$emit('open', customer)"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        class="ml-3 text-sm text-gray-400 underline"
                                        @click="$emit('history', customer)"
                                    >
                                        History
                                    </button>

                                    <button
                                        class="cursor-pointer ml-3 text-sm text-red-500"
                                        @click="$emit('delete', customer)"
                                    >
                                        Remove
                                    </button>
                                </td>
                                </tr>
                        </template>
                        <tr v-else>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No customers found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="customers.meta.links" class="mt-4" />
        </div>
    </div>
</template>
