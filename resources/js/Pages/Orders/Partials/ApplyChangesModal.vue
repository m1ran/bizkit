<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const emit = defineEmits(['close', 'apply']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    customerName: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Modal :show="show" closeable @close="$emit('close')">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex items-center justify-center size-12 rounded-full bg-blue-100 sm:mx-0 sm:size-10">
                    <svg class="size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 4h.01" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-start">
                    <h3 class="text-lg font-medium text-gray-900">
                        Update customer?
                    </h3>
                    <div class="mt-4 text-sm text-gray-600">
                        You selected <span class="font-bold">{{ customerName }}</span> but have changed their details.<br>
                        What would you like to do?
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end space-x-2">
            <SecondaryButton @click="$emit('close')">
                Cancel
            </SecondaryButton>
            <PrimaryButton @click="$emit('apply', 'keep')">
                Leave as is
            </PrimaryButton>
            <PrimaryButton @click="$emit('apply', 'update')">
                Update customer
            </PrimaryButton>
            <PrimaryButton @click="$emit('apply', 'create')">
                Create new customer
            </PrimaryButton>
        </div>
    </Modal>
</template>
