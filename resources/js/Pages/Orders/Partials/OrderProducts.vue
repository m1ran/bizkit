<script setup>
import { computed } from 'vue';
import Autocomplete from '@/Components/Autocomplete.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    }
});

async function searchProducts(q) {
    const exclude = [];
    props.form.items.forEach(item => {
        if (item.product_id) exclude.push(item.product_id);
    });
    const { data } = await axios.get('/api/products', { params: { q, 'exclude': exclude.join(',') } });
    return data;
}

function productDisplay(p) {
    return p.name;
}

function onProductSelect(idx, p) {
    const item = props.form.items[idx];
    item.product_id = p.id;
    item.unit_price = p.price;
    // item.limit = p.quantity;
    updateLineTotal(idx);
}

function addItem() {
    props.form.items.push({
        product: null,
        product_id: null,
        quantity: 1,
        unit_cost: 0,
        line_cost: 0,
        unit_price: 0,
        line_price: 0,
    });
}

function removeItem(idx) {
    props.form.items.splice(idx, 1);
}

function updateLineTotal(idx) {
    const item = props.form.items[idx];
    // prevent excess the limit quantity
    if (item.product && item.quantity > item.product.quantity) {
        item.quantity = item.product.quantity;
    }
    item.line_cost = item.quantity * item.unit_cost;
    item.line_price = item.quantity * item.unit_price;
}

const orderTotal = computed(() => props.form.items.reduce((sum, i) => sum + i.line_price, 0));

function formatCurrency(val) {
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(val);
}
</script>

<template>
    <div id="order-products-wrapper">
        <div>
            <InputLabel value="Order Items" />
            <table class="min-w-full table-auto border-collapse mt-2">
                <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-center">Qty</th>
                    <th class="px-4 py-2 text-right">Unit Price</th>
                    <th class="px-4 py-2 text-right">Sub Total</th>
                    <th class="px-4 py-2"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, idx) in form.items" :key="idx" class="border-t">
                    <td class="px-4 py-2">
                    <Autocomplete
                        :search-fn="searchProducts"
                        :display="productDisplay"
                        v-model="item.product"
                        placeholder="Search products"
                        @select="product => onProductSelect(idx, product)"
                    />
                    <InputError :message="form.errors[`items.${idx}.product_id`]" class="mt-1 text-red-500" />
                    </td>
                    <td class="px-4 py-2 text-center">
                    <TextInput
                        type="number"
                        v-model.number="item.quantity"
                        @input="updateLineTotal(idx)"
                        class="w-20 mx-auto"
                        min="1"
                        :max="item.product ? item.product.quantity : null"
                    />
                    <InputError :message="form.errors[`items.${idx}.quantity`]" class="mt-1 text-red-500" />
                    </td>
                    <td class="px-4 py-2 text-right">
                    {{ formatCurrency(item.unit_price) }}
                    </td>
                    <td class="px-4 py-2 text-right">
                    {{ formatCurrency(item.line_price) }}
                    </td>
                    <td class="px-4 py-2 text-center">
                    <button type="button" @click="removeItem(idx)" class="text-red-500">
                        Remove
                    </button>
                    </td>
                </tr>
                <tr v-if="!form.items.length">
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">No items added.</td>
                </tr>
                </tbody>
            </table>
            <PrimaryButton type="button" @click="addItem" class="mt-2">
                + Add Item
            </PrimaryButton>
            <InputError :message="form.errors.items" class="mt-2 text-red-500" />
        </div>

        <!-- Total Price -->
        <div class="flex justify-end text-xl font-semibold">
            Total: {{ formatCurrency(orderTotal) }}
        </div>
    </div>
</template>
