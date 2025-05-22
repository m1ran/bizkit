<script setup>
import { computed, onMounted, shallowRef } from 'vue';
import DialogModal from '../DialogModal.vue';
import SecondaryButton from '../SecondaryButton.vue';
import { firstToUpper } from '@/helpers';

const show = shallowRef(false);
const history = shallowRef([]);

const emit = defineEmits(['close']);

const props = defineProps({
    entity: {
        type: String,
        required: true,
        validator: value => ['customer', 'product', 'order'].includes(value),
    },
    id: {
        type: Number,
        required: true,
        validator: value => Number.isInteger(value),
    }
});

const title = computed(() => {
    return `${capitalized.value} #${props.id} history`;
});

const capitalized = computed(() => {
    return firstToUpper(props.entity);
});

const close = () => {
    emit('close');
};

const hasChanged = (i, key) => {
  const curr = history.value[i]?.data?.[key];
  const prev = history.value[i + 1]?.data?.[key];

  return prev !== undefined && curr !== prev;
}

onMounted(async () => {
    try {
        const { data } = await router.get(`/api/history/${props.entity}/${props.id}`)
        history.value = data.history;
        show.value = true;
    } catch (e) {
        console.error('Cannot fetch history data', e)
    }
});
</script>

<template>
    <DialogModal :show="show" @close="close">
        <template #title>{{ title }}</template>
        <template #content>
            <ul class="space-y-4 max-h-96 overflow-auto">
                <li
                    v-for="(record, i) in history"
                    :key="record.id"
                    class="p-4 bg-white rounded-lg shadow-sm border"
                >
                    <!-- Time and event -->
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs text-gray-500">
                        {{ new Date(record.created_at).toLocaleString() }}
                        </span>
                        <span
                        class="text-sm font-semibold px-2 py-0.5 rounded"
                        :class="{
                            'bg-green-100 text-green-800': record.event === 'created',
                            'bg-blue-100 text-blue-800':  record.event === 'updated',
                            'bg-red-100 text-red-800':   record.event === 'deleted',
                        }"
                        >
                        {{ record.event.toUpperCase() }}
                        </span>
                    </div>

                    <!-- Data -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                        <div
                            v-for="(value, key) in record.data"
                            :key="key"
                            class="flex items-baseline"
                        >
                            <span class="font-medium mr-1 capitalize">{{ key }}:</span>
                            <span
                                :class="hasChanged(i, key) ? 'bg-yellow-100 px-1 rounded' : ''"
                                class="truncate"
                            >
                                {{ value ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <!-- Editor -->
                    <div class="text-xs text-gray-600">
                        By: <span class="font-medium">{{ record.user?.name || '—' }}</span>
                    </div>
                </li>
                <li v-if="!history.length" class="text-center text-gray-500 py-8">
                    No history found.
                </li>
            </ul>
        </template>
        <template #footer>
            <SecondaryButton @click="close">
                Close
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
