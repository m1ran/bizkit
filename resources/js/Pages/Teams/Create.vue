<script setup>
import CreateTeamForm from '@/Pages/Teams/Partials/CreateTeamForm.vue';
</script>

<template>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <CreateTeamForm />
        </div>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  layout: (h, page) =>
    h(AppLayout, { title: 'Create Team' }, {
      default: () => page
    }),
};
</script>
