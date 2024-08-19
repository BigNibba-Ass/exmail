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
    {name: 'Экран 1', icon: DeviceTabletIcon, route: 'admin.main_screen.index'},
    {name: 'Инф. база', icon: CircleStackIcon, route: 'admin.informations.index'},
    {name: 'База КП', icon: BriefcaseIcon, route: 'dashboard'},
]
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="props.name"/>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden sm:rounded-lg w-full">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-10">
                        <div class="flex flex-col sm:col-span-1 rounded gap-3">
                            <Link
                                v-for="(page, key) of pages"
                                :href="route(page.route)"
                                :class="(route().current(page.route) === true ? 'bg-blue-700 text-white' : 'bg-white text-black hover:bg-gray-300')"
                                class="inline-flex items-center px-4 py-3 rounded-lg active w-full transition"
                                aria-current="page">
                                <component :is="page.icon" class="w-5 me-2"/>
                                <span>{{ page.name }}</span>
                            </Link>
                        </div>
                        <div class="sm:col-span-9 bg-white rounded">
                            <div class="p-5">
                                <slot/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
