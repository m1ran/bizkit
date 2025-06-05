<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import Autocomplete from '@/Components/Autocomplete.vue';
import OrderProducts from './OrderProducts.vue';
import { computed, onMounted, ref, watch } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import { useStatusAutocomplete } from '../composables/useStatusAutocomplete';

const { form, statuses } = defineProps({
    form: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Array,
        required: true,
    },
});

const allowedStatuses = computed(() => {
    const currentStatusOrder = form.status_id ? statuses.find(s => s.id === form.status_id)?.sort_order : 0;
    return statuses.filter(s => s.sort_order >= currentStatusOrder);
});

const {
    query: statusQuery,
    selectedStatus,
    searchStatus,
    statusDisplay
} = useStatusAutocomplete(allowedStatuses.value);

// Sync selectedStatus with form.status
watch(
    () => form.status_id,
    (val) => {
        selectedStatus.value = statuses.find(s => s.id === val);
    },
    { immediate: true }
);

watch(selectedStatus, (s) => {
    if (s) {
        form.status_id = s.id;
        form.status = s;
    } else {
        form.status_id = null;
        form.status = null;
    }
});

const emits = defineEmits(['submitted']);

const selectedCustomer = ref(null);

// Fetch customers for autocomplete
const searchCustomers = async (query) => {
    const { data } = await axios.get('/api/customers', { params: { q: query } });
    return data;
}

const customerDisplay = (c) => c.first_name + ' ' + c.last_name;

// When customer selected, fill form fields
watch(selectedCustomer, c => {
    if (c) {
        form.customer    = c;
        form.customer_id = c.id;
        form.first_name  = c.first_name;
        form.last_name   = c.last_name;
        form.address     = c.address;
        form.phone       = c.phone;
    }
});

const onPhoneFocus = () => {
    if (!form.phone) {
        form.phone = '+';
    }
};

const onPhoneBlur = () => {
    if (form.phone === '+') {
        form.phone = '';
    }
};

onMounted(() => {
    if (form.customer?.id) {
        let c =  form.customer;
        selectedCustomer.value = {
            id: c.id,
            email: c.email,
            // Get contact information from order otherwise from customer
            phone: form.phone || c.phone,
            address: form.address || c.address,
            last_name: form.last_name || c.last_name,
            first_name: form.first_name || c.first_name,
        };
    }
});
</script>

<template>
    <form @submit.prevent="$emit('submitted')">
        <!-- Customer Autocomplete -->
        <div class="mb-6">
            <InputLabel for="customer" value="Customer" />
            <Autocomplete
                id="customer"
                :search-fn="searchCustomers"
                :display="customerDisplay"
                :extra="['phone', 'email']"
                v-model="selectedCustomer"
                placeholder="Search customers..."
                class="mt-1"
            />
            <InputError :message="form.errors.customer_id" class="mt-2" />
        </div>

        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="customer-first-name"
                    v-model="form.first_name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="customer-last-name"
                    v-model="form.last_name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div>

            <div v-if="$page.props.locale !== 'en'" class="col-span-6 sm:col-span-3">
                <InputLabel for="patronymic_name" value="Patronymic Name" />
                <TextInput
                    id="customer-patronymic-name"
                    v-model="form.patronymic_name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.patronymic_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="phone" value="Phone" />
                <TextInput
                    id="customer-phone"
                    v-model="form.phone"
                    type="text"
                    class="block w-full mt-1"
                    @focus="onPhoneFocus"
                    @blur="onPhoneBlur"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="name" value="Address" />
                <TextInput
                    id="customer-address"
                    v-model="form.address"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.address" class="mt-2" />
            </div>

            <div v-if="form.id" class="col-span-6 sm:col-span-3">
                <InputLabel for="order-status-autocomplete" value="Status" />
                <Autocomplete
                    id="order-status-autocomplete"
                    :search-fn="searchStatus"
                    :display="statusDisplay"
                    v-model="selectedStatus"
                    :default-suggestions="allowedStatuses"
                    placeholder="Choose status..."
                    class="mt-1"
                />
                <InputError :message="form.errors.status_id" class="mt-2" />
            </div>

            <OrderProducts v-bind:form="form" class="col-span-6 sm:col-span-6" />

            <div v-if="!form.id" class="col-span-6 sm:col-span-3">
                <label  class="flex items-center">
                    <Checkbox v-model:checked="form.finished" name="finished" />
                    <span class="ms-2 text-sm text-gray-600">Mark as finished</span>
                </label>
            </div>

            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="notes" value="Notes" />
                <TextAreaInput
                    id="customer-notes"
                    v-model="form.notes"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.notes" class="mt-2" />
            </div>
        </div>

        <button type="submit" class="hidden" />
    </form>
</template>
