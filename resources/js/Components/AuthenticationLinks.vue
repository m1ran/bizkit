<script setup>
import { Link } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    responsive: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div>
        <div v-if="$page.props.canLogin">
            <nav v-if="responsive" class="pt-4 pb-1 border-t border-gray-200">
                <ResponsiveNavLink :href="route('login')" :active="route().current('login')">
                    Log in
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    v-if="$page.props.canRegister"
                    :href="route('register')"
                    :active="route().current('register')">
                    Register
                </ResponsiveNavLink>
            </nav>
            <nav v-else class="-mx-3 flex flex-1 justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Log in
                </Link>

                <Link
                    v-if="$page.props.canRegister"
                    :href="route('register')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Register
                </Link>
            </nav>
        </div>
    </div>
</template>
