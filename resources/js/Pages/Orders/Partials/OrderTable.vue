<script setup>
import { ref } from 'vue';
import { watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import THead from '@/Components/THead.vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { getItemById } from '@/helpers';
import { useStatusAutocomplete } from '../composables/useStatusAutocomplete';
import Autocomplete from '@/Components/Autocomplete.vue';

const HEADERS = [
    'Num #',
    'Customer',
    'Status',
    'Total Coast',
    'Total Price',
    'Notes',
    'Created',
    'Updated',
    'Actions',
];

const COLOR_MAP = {
  draft    : 'bg-gray-100   text-gray-800',
  pending  : 'bg-yellow-100 text-yellow-800',
  paid     : 'bg-blue-100   text-blue-800',
  shipped  : 'bg-indigo-100 text-indigo-800',
  finished : 'bg-green-100  text-green-800',
  cancelled: 'bg-red-100    text-red-800',
}

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            q: '',
        }),
    },
});

const queryOptions = { preserveState: true, replace: true, only: ['orders'] };

const emit = defineEmits(['open', 'history', 'delete']);

const searchDebounce = debounce ((value) => {
    updateFilters();
}, 500);

const statusLabel = (statusId) => {
    return getItemById(statusId, props.statuses, 'label');
};

const statusClasses = (statusId) => {
    const statusName = getItemById(statusId, props.statuses, 'name');
    return COLOR_MAP[statusName] || 'bg-gray-100 text-gray-800'
}

const q = ref(props.filters.q);
const selectedStatusId = ref(props.filters.status || null);


const {
    query: statusQuery,
    selectedStatus,
    searchStatus,
    statusDisplay
} = useStatusAutocomplete(props.statuses);

// Set selectedStatusId from filter on page load
if (props.filters.status) {
    const initialStatus = props.statuses.find(
        s => s.id === Number(props.filters.status)
    );
    if (initialStatus) {
        selectedStatus.value = initialStatus;
    }
}

watch(q, newValue => {
    searchDebounce(newValue);
});

watch(selectedStatus, (status) => {
    selectedStatusId.value = status ? status.id : null;
    updateFilters();
});

const updateFilters = () => {
    router.get(route('orders.index'), {
        q: q.value,
        status: selectedStatusId.value,
    }, queryOptions);
}
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="py-6 px-4 max-w-7xl mx-auto">
            <div class="mb-4 flex justify-between items-center">
                <div class="flex gap-4 w-2/3">
                    <TextInput
                        id="order-search"
                        v-model="q"
                        type="text"
                        class="block w-1/3 mt-1"
                        autofocus
                        placeholder="Search orders..."
                    />
                    <Autocomplete
                        id="order-status-filter"
                        :search-fn="searchStatus"
                        :display="statusDisplay"
                        v-model="selectedStatus"
                        :default-suggestions="props.statuses"
                        placeholder="Filter by status..."
                        class="mt-1 w-1/3"
                    />
                </div>

                <PrimaryButton @click="$emit('open')" >
                    + Add Order
                </PrimaryButton>
            </div>

            <div class="bg-white shadow-sm rounded overflow-x-auto">
                <table class="min-w-full table-auto border">
                    <THead :headers="HEADERS" />

                    <tbody>
                        <template v-if="orders.meta.total">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-100 even:bg-gray-50">
                                <td class="px-4 py-2 border">{{ order.num }}</td>
                                <td class="px-4 py-2 border">{{ order.customer_id }}</td>
                                <td class="px-4 py-2 border">
                                    <span
                                        class="text-sm font-semibold px-2 py-0.5 rounded"
                                        :class="statusClasses(order.status_id)"
                                    >
                                        {{ statusLabel(order.status_id) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">{{ order.total_cost }}</td>
                                <td class="px-4 py-2 border">{{ order.total_price }}</td>
                                <td class="px-4 py-2 border">{{ order.notes }}</td>
                                <td class="px-4 py-2 border">
                                    {{ $dayjs(order.created_at).tz('Europe/Kyiv').format('DD.MM.YYYY HH:mm') }}
                                </td>
                                <td class="px-4 py-2 border">
                                    {{ $dayjs(order.updated_at).tz('Europe/Kyiv').format('DD.MM.YYYY HH:mm') }}
                                </td>
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
