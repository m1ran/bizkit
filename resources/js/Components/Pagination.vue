<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  links: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <nav class="flex flex-wrap items-center justify-center gap-1 mt-6" aria-label="Pagination">
    <template v-for="(link, index) in links" :key="index">
      <Component :is="link.url ? Link : 'span'"
        :href="link.url"
        v-html="link.label"
        class="px-3 py-1 text-sm rounded border transition"
        :class="[
          link.active
            ? 'bg-gray-800 text-white border-gray-300 hover:bg-gray-700'
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
          !link.url ? 'pointer-events-none opacity-50' : ''
        ]"
      />
    </template>
  </nav>
</template>
