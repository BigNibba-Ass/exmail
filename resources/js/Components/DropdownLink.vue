<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 bg-indigo-800 hover:bg-indigo-600 text-white transition"
    >
        <slot />
    </Link>
</template>
