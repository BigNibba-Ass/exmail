<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {computed, ref, watch} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";
import {router, usePage} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";
import {NoSymbolIcon, CheckIcon} from "@heroicons/vue/20/solid/index.js";
import {generateRandomString} from "@/Traits.js";

</script>

<template>
    <AdminLayout name="База КП">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 w-full">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    База КП
                </h2>
            </div>
            <form class="sm:col-span-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6 p-5 rounded grid grid-cols-1 gap-y-6 gap-x-4 rounded">
                        <div>
                            <input class="w-full rounded border-gray-200 shadow"
                                   placeholder="Поиск"/>
                        </div>
                        <table class="w-full rounded divide-y divide-gray-200 shadow rounded">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="relative px-6 py-3">
                                    Имя
                                </th>
                                <th class="relative px-6 py-3">
                                    ФИО сотрудника
                                </th>
                                <th class="relative px-6 py-3">
                                    Дата создания
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
