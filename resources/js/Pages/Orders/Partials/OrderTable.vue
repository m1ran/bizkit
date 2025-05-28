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
    orders: {
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
    '#',
    'Customer',
    'Status',
    'Total Coast',
    'Total Price',
    'Notes',
    'Created',
    'Actions',
]);

const queryOptions = { preserveState: true, replace: true, only: ['orders'] };

const emit = defineEmits(['open', 'history', 'delete']);

const search = debounce ((value) => {
    router.get(route('orders.index'), { q: value }, queryOptions);
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
                    id="order-search"
                    v-model="q"
                    type="text"
                    class="block w-1/3 mt-1"
                    autofocus
                    placeholder="Search orders..."
                />

                <PrimaryButton @click="$emit('open')">
                    + Add Order
                </PrimaryButton>
            </div>

            <div class="bg-white shadow-sm rounded overflow-x-auto">
                <table class="min-w-full table-auto border">
                    <THead :headers="headers" />

                    <tbody>
                        <template v-if="orders.meta.total">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-100 even:bg-gray-50">
                                <td class="px-4 py-2 border">{{ order.num }}</td>
                                <td class="px-4 py-2 border">{{ order.customer_id }}</td>
                                <td class="px-4 py-2 border">{{ order.status_id }}</td>
                                <td class="px-4 py-2 border">{{ order.total_cost }}</td>
                                <td class="px-4 py-2 border">{{ order.total_price }}</td>
                                <td class="px-4 py-2 border">{{ order.notes }}</td>
                                <td class="px-4 py-2 border">{{ order.created_at }}</td>
                                <td class="px-4 py-2 border">
                                    <button
                                        class="text-sm text-blue-400 underline"
                                        @click="$emit('open', order)"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        class="ml-3 text-sm text-gray-400 underline"
                                        @click="$emit('history', order)"
                                    >
                                        History
                                    </button>

                                    <button
                                        class="cursor-pointer ml-3 text-sm text-red-500"
                                        @click="$emit('delete', order)"
                                    >
                                        Remove
                                    </button>
                                </td>
                                </tr>
                        </template>
                        <tr v-else>
                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">No orders found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="orders.meta.links" class="mt-4" />
        </div>
    </div>
</template>
