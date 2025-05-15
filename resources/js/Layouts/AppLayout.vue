<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import NavigationMenuPrimary from '@/Components/Navigation/NavigationMenuPrimary.vue';
import NavigationMenuResponsive from '@/Components/Navigation/NavigationMenuResponsive.vue';

defineProps({
    title: String,
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white" :class="{'shadow': !title, 'border-b border-gray-100': title}">
                <!-- Primary Navigation Menu -->
                <NavigationMenuPrimary
                    :switch-to-team="switchToTeam"
                    :logout="logout"
                    v-model:showing-navigation-dropdown="showingNavigationDropdown"
                />

                <!-- Responsive Navigation Menu -->
                <NavigationMenuResponsive
                    :switch-to-team="switchToTeam"
                    :logout="logout"
                    :showing-navigation-dropdown="showingNavigationDropdown"
                />
            </nav>

            <!-- Page Heading -->
            <header v-if="title" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight" v-text="title" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
