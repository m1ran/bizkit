<script setup>
import { shallowRef, watch } from 'vue'
import { debounce } from 'lodash';

/**
 * props:
 * - modelValue: the selected item object
 * - searchFn: async function accepting a query string and returning an array of items
 * - display: function to render an item to string
 * - placeholder: input placeholder
 * - id: optional input id
 * - customClass: optional additional classes for input
 */
const props = defineProps({
    modelValue: {
        type: null,
        default: null,
    },
    searchFn: {
        type: Function,
        required: true,
    },
    display: {
        type: Function,
        required: true,
    },
    placeholder: {
        type: String,
        default: '',
    },
    id: {
        type: String,
        default: null,
    },
    customClass: {
        type: String,
        default: '',
    },
    extra: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(['update:modelValue', 'select']);

const inputValue = shallowRef('');
const suggestions = shallowRef([]);
const showList = shallowRef(false);
const highlightedIndex = shallowRef(-1);

// Sync inputValue when modelValue changes
watch(
    () => props.modelValue,
    val => inputValue.value = val ? props.display(val) : '',
    { immediate: true }
);

// Debounced search
const doSearch = debounce(async (q) => {
    if (!q) {
        suggestions.value = [];
        return;
    }
    const results = await props.searchFn(q);
    suggestions.value = results;
    highlightedIndex.value = 0;
}, 500);

function onInput(e) {
    const q = inputValue.value;
    // clear selection
    emit('update:modelValue', null);
    doSearch(q);
    showList.value = true;
}

function openList() {
    if (suggestions.value.length) {
        showList.value = true;
    }
}

function closeList() {
    showList.value = false;
}

function onBlur() {
  // delay to allow click
    setTimeout(() => {
        closeList();
    }, 200);
}

function select(item) {
    emit('update:modelValue', item);
    emit('select', item);
    showList.value = false;
    suggestions.value = [];
}

function highlightNext() {
    if (highlightedIndex.value < suggestions.value.length - 1) {
        highlightedIndex.value++;
    }
}

function highlightPrev() {
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    }
}

function selectHighlighted() {
    if (suggestions.value[highlightedIndex.value]) {
        select(suggestions.value[highlightedIndex.value]);
    }
}
</script>

<template>
    <div class="relative" @keydown.escape="closeList">
        <input
            :id="id"
            type="text"
            v-model="inputValue"
            :placeholder="placeholder"
            :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500', customClass]"
            @input="onInput"
            @focus="openList"
            @blur="onBlur"
            @keydown.down.prevent="highlightNext"
            @keydown.up.prevent="highlightPrev"
            @keydown.enter.prevent="selectHighlighted"
        />
        <ul
            v-if="showList && suggestions.length"
            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        >
            <li v-for="(item, idx) in suggestions"
                :key="idx"
                :class="['cursor-pointer px-4 py-2 hover:bg-gray-100', idx === highlightedIndex ? 'bg-indigo-100' : '']"
                @mousedown.prevent="() => select(item)"
            >
                <div class="flex flex-col space-y-1">
                    <span class="truncate font-medium">{{ display(item) }}</span>
                    <!-- Display extra fields if provided -->
                    <template v-if="extra.length">
                        <template v-for="(value, key) in extra" :key="key">
                            <span v-if="item[value] && item[value].length" class="text-sm text-gray-500">
                                {{ item[value] }}
                            </span>
                        </template>
                    </template>
                </div>
            </li>
        </ul>
    </div>
</template>
