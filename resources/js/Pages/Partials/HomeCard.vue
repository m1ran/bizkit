<script setup>
import Welcome from '@/Pages/Partials/Welcome.vue';
import Features from '@/Pages/Partials/Features.vue';
</script>

<template>
    <div>
        <div class="flex flex-col lg:flex-row items-center gap-8 p-8 border-b border-gray-200">
            <div class="lg:w-1/3">
                <img
                    src="/storage/home-promo.png"
                    alt="Welcome to BizKit"
                    class="w-full max-w-md mx-auto"
                />
            </div>
            <div class="lg:w-2/3">
                <Welcome />
            </div>
        </div>
        <Features />
    </div>
</template>
