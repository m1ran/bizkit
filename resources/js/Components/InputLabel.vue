<script setup>
defineProps({
    value: String,
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700">
        <span v-if="value">{{ value }} <span v-if="required" class="text-sm text-red-500">*</span></span>
        <span v-else><slot /></span>
    </label>
</template>
