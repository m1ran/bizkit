<script setup>
import { computed, onMounted } from 'vue';
import Autocomplete from '@/Components/Autocomplete.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Tooltip from '@/Components/Tooltip.vue';

const { form } = defineProps({
    form: {
        type: Object,
        required: true,
    }
});

const searchProducts = async (q) => {
    const { data } = await axios.get('/api/products', { params: { q } });
    // filter data from selected products in items
    return data.filter(c => !form.items.some(i => i.product_id === c.id));
}

const productDisplay = (p) => p.name;

const onProductSelect = (idx, p) => {
    const item = form.items[idx];
    item.product_id = p.id;
    item.unit_price = p.price;
    updateLineTotal(idx);
}

const addItem = () => {
    form.items.push({
        product: null,
        product_id: null,
        quantity: 1,
        unit_price: 0,
        line_price: 0,
    });
}

const removeItem = (idx) => {
    form.items.splice(idx, 1);
};

const updateLineTotal = (idx) => {
    const item = form.items[idx];
    let price = item.unit_price;

    if (item.product) {
        // prevent excess the limit quantity
        if (item.quantity > item.product.quantity) {
            item.quantity = item.product.quantity;
        }
        // get price from product
        price = item.product.price ?? price;
        item.unit_price = price;
    }
    item.line_price = item.quantity * price;
}

const formatCurrency = (val) => new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(val);

const orderTotal = computed(() => form.items.reduce((sum, i) => sum + i.line_price * 1, 0));

const getMaxInStock = (item) => item.product.quantity + item.quantity;

const showWarning = (item) => {
    return item.product && item.product.price != item.unit_price;
}

onMounted(() => {
    if (form.id) {
        // put order items in stock limit
        form.items.forEach((item) => {
            if (item.product) {
                item.product.quantity = getMaxInStock(item);
            }
        });
    }
});
</script>

<template>
    <div id="order-products-wrapper">
        <div>
            <InputLabel value="Order Items" />
            <table class="min-w-full table-auto border-collapse mt-2">
                <thead>
                <tr class="bg-gray-100">
                    <th class="pl-3 py-2"></th>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-center">Qty</th>
                    <th class="px-4 py-2 text-right">Unit Price</th>
                    <th class="px-4 py-2 text-right">Sub Total</th>
                    <th class="px-4 py-2"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, idx) in form.items" :key="idx" class="border-t">
                    <td class="pl-3 py-2 text-center">
                        <Tooltip text="The unit price of the product has changed." placement="right">
                            <FontAwesomeIcon v-if="showWarning(item)"
                                icon="exclamation-circle"
                                class="text-red-500"
                            />
                        </Tooltip>
                    </td>
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
                        :max="(item.product ? item.product.quantity : null)"
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
