<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {computed, ref, watch} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";
import {router, usePage} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";
import {NoSymbolIcon, CheckIcon} from "@heroicons/vue/20/solid/index.js";
import {generateRandomString} from "@/Traits.js";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    offers: Object
})

const userSearchQuery = ref(null)

const applyPage = (val) => {
    const params = { page: val}
    if(userSearchQuery.value) {
        params.name = userSearchQuery.value
    }
    router.get(route('admin.offers.index', params))
}

watch(userSearchQuery, (val) => {
    router.get(route('admin.offers.index', {name: val, page: 1}), {}, {
        preserveScroll: true,
        preserveState: true,
    })
})
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
                            <input
                                v-model="userSearchQuery"
                                class="w-full rounded border-gray-200 shadow"
                                placeholder="Имя сотрудника"/>
                        </div>
                        <table class="w-full rounded divide-y divide-gray-200 shadow rounded">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="relative px-6 py-3">
                                    ID
                                </th>
                                <th class="relative px-6 py-3">
                                    ФИО сотрудника
                                </th>
                                <!--                                <th class="relative px-6 py-3">-->
                                <!--                                    Ссылка-->
                                <!--                                </th>-->
                                <th class="relative px-6 py-3">
                                    Дата создания
                                </th>
                            </tr>
                            <tr v-for="offer in props.offers.data">
                                <td class="relative px-6 py-3">
                                    {{ offer.id }}
                                </td>
                                <td class="relative px-6 py-3">
                                    {{ offer.user?.name }}
                                </td>
                                <!--                                <td class="relative px-6 py-3">-->
                                <!--                                    <a style="color: blue" :href="route('offers.show', offer.id)">-->
                                <!--                                        {{route('offers.show', offer.id)}}-->
                                <!--                                    </a>-->
                                <!--                                </td>-->
                                <td class="relative px-6 py-3">
                                    {{ offer.created_at_in_format }}
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <pagination
                            @switch_page="applyPage"
                            :current="props.offers.current_page"
                            :max="props.offers.last_page"/>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
