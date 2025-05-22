<script setup>
import { computed } from 'vue';
import ConfirmationModal from '../ConfirmationModal.vue';
import DangerButton from '../DangerButton.vue';
import SecondaryButton from '../SecondaryButton.vue';
import { firstToUpper } from '@/helpers';

const emit = defineEmits(['close', 'confirmed']);

const props = defineProps({
    entity: {
        type: String,
        required: true,
    },
    show: {
        type: Boolean,
        default: false,
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const capitalized = computed(() => {
    return firstToUpper(props.entity);
});
</script>

<template>
    <ConfirmationModal :show="show" @close="$emit('close')">
        <template #title>
            Delete {{ capitalized }}
        </template>

        <template #content>
            Are you sure you want to delete this {{ entity }}?
            Once a {{ entity }} is deleted, all of its resources and data will be permanently deleted.
        </template>

        <template #footer>
            <SecondaryButton @click="$emit('close')">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ms-3"
                :class="{ 'opacity-25': processing }"
                :disabled="processing"
                @click="$emit('confirmed')"
            >
                Delete {{ capitalized }}
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
