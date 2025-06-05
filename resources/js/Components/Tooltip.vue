<!-- resources/js/Components/Tooltip.vue -->
<template>
    <!-- Wrapper with mouse/keyboard triggers -->
    <span
      class="relative inline-block"
      @mouseenter="show"
      @mouseleave="hide"
      @focus="show"
      @blur="hide"
    >
      <!-- Slot for the element that triggers the tooltip -->
      <slot />

      <!-- Tooltip body -->
      <transition name="fade">
        <span
          v-show="visible"
          :class="[
            commonClasses,
            placementClasses[placement] || placementClasses.top,
          ]"
          role="tooltip"
        >
          {{ text }}
        </span>
      </transition>
    </span>
  </template>

  <script setup>
  import { ref } from 'vue'

  /**
   * Props
   * @prop {String} text       Tooltip content
   * @prop {String} placement  Tooltip position: top | bottom | left | right
   */
  const props = defineProps({
    text:      { type: String, required: true },
    placement: { type: String, default: 'top' },
  })

  /* Reactive state */
  const visible = ref(false)
  const show    = () => (visible.value = true)
  const hide    = () => (visible.value = false)

  /* Base classes applied to every tooltip */
  const commonClasses =
    'absolute z-50 whitespace-nowrap rounded bg-gray-800 px-2 py-1 text-xs text-white shadow-lg'

  /* Tailwind-based positioning for four placements */
  const placementClasses = {
    top:    'bottom-full mb-2 left-1/2 -translate-x-1/2',
    bottom: 'top-full   mt-2 left-1/2 -translate-x-1/2',
    left:   'right-full mr-2 top-1/2 -translate-y-1/2',
    right:  'left-full  ml-2 top-1/2 -translate-y-1/2',
  }
  </script>

  <style scoped>
  /* Simple fade + slight slide transition */
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.15s ease-out, transform 0.15s ease-out;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
    transform: translateY(4px);
  }
  </style>
