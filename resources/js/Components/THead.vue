<script setup>
defineProps({
    headers: {
        type: Array
    }
});
</script>

<template>
    <thead class="bg-gray-100">
        <tr>
            <th v-for="(title, key) in headers" :key="`thead-th-${key}`" class="px-4 py-2 border text-left">
                {{ title }}
            </th>
        </tr>
    </thead>
</template>
