<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from '@inertiajs/vue3';
import {
    BriefcaseIcon,
    CircleStackIcon,
    DeviceTabletIcon,
    UserCircleIcon,
} from "@heroicons/vue/20/solid/index.js";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    name: String
})

const pages = [
    {name: 'Профили', icon: UserCircleIcon, route: 'admin.users.index'},
    {name: 'Экран 1', icon: DeviceTabletIcon, route: 'admin.main-screen'},

    {name: 'Инф. база', icon: CircleStackIcon, route: 'admin.informations.index'},

    {name: 'База КП', icon: BriefcaseIcon, route: 'admin.offers.index'},
]
</script>

<template>
    <Head :title="props.name"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 w-2/12">
                <Link
                    v-for="(page, key) of pages"
                    :href="route(page.route)"
                    :class="(route().current(page.route) === true ? 'bg-blue-700 text-white' : 'bg-white text-black hover:bg-gray-300')"
                    class="inline-flex items-center px-4 py-3 rounded-lg active w-full transition"
                    aria-current="page">
                    <component :is="page.icon" class="w-5 me-2"/>
                    {{ page.name }}
                </Link>
            </div>
        </template>
        <slot/>
    </AuthenticatedLayout>
</template>
