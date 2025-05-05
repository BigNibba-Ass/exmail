<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {ref} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";

import {PencilIcon, ArrowPathIcon} from "@heroicons/vue/20/solid/index.js";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    cities: Array
})

const remove = (id) => {
    if (confirm('Удалить?')) {
        router.delete(route('admin.cities.destroy', id))
    }
}
</script>

<template>
    <AdminLayout name="Основной экран">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 w-full">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    Города
                </h2>
            </div>
            <div class="sm:col-span-6">
                <table class="w-full rounded divide-y divide-gray-200 shadow rounded">
                    <thead class="bg-gray-50">
                    <tr style="text-align: left">
                        <th class="relative px-6 py-3">
                            ID
                        </th>
                        <th class="relative px-6 py-3">
                            Название
                        </th>
                        <th class="relative px-6 py-3">
                            Удалить
                        </th>
                    </tr>
                    <tr v-for="city in props.cities">
                        <td class="relative px-6 py-3">
                            {{ city.id }}
                        </td>
                        <td class="relative px-6 py-3">
                            {{ city.name }}
                        </td>
                        <td class="relative px-6 py-3">
                            <button
                                @click.prevent="remove(city.id)"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Удалить
                            </button>
                        </td>
                        <!--                        <td class="relative px-6 py-3">-->
                        <!--                            {{ offer.user?.name }}-->
                        <!--                        </td>-->
                        <!--                        <td class="relative px-6 py-3">-->
                        <!--                            <a style="color: blue" :href="route('offers.show', offer.id)">-->
                        <!--                                {{ route('offers.show', offer.id) }}-->
                        <!--                            </a>-->
                        <!--                        </td>-->
                        <!--                        <td class="relative px-6 py-3">-->
                        <!--                            {{ offer.created_at_in_format }}-->
                        <!--                        </td>-->
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
