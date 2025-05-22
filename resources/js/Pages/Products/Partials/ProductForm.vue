<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';

const { form } = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emits = defineEmits(['submitted']);
</script>

<template>
    <form @submit.prevent="$emit('submitted')">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="product-name"
                    v-model="form.name"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="sku" value="SKU #" />
                <TextInput
                    id="product-sku"
                    v-model="form.sku"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.sku" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="price" value="Price" />
                <TextInput
                    id="product-price"
                    v-model="form.price"
                    type="text"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.price" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="quantity" value="Quantity" />
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
