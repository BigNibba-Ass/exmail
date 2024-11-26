<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {ref} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";

import {PencilIcon, ArrowPathIcon} from "@heroicons/vue/20/solid/index.js";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    companies: Array
})

const selectedCompany = ref(0)
const selectedServiceId = ref(0)
const file = ref(null)

const submit = () => {
    router.post(route('admin.upload-data'), {
        service_id: selectedServiceId.value,
        file: file.value,
    })
}
</script>

<template>
    <AdminLayout name="Основной экран">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 w-full">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    Информационная база
                </h2>
            </div>
            <form @submit.prevent="submit" class="sm:col-span-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <h2 class="text-lg font-medium text-gray-900">
                            Загрузить файл (.xlsx) к услуге
                        </h2>
                    </div>
                    <div class="rounded sm:col-span-6 grid grid-cols-1 gap-y-6 gap-x-4">
                        <select
                            v-model="selectedCompany"
                            class="shadow-sm sm:col-span-6 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option :value="0" disabled selected>Компания</option>
                            <option :value="company" v-for="company of props.companies">{{ company.name }}</option>
                        </select>
                        <select
                            v-if="selectedCompany?.id"
                            v-model="selectedServiceId"
                            class="shadow-sm sm:col-span-6 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option :value="0" disabled selected>Услуга</option>
                            <option :value="service.id" v-for="service of selectedCompany.services">{{
                                    service.name
                                }}
                            </option>
                        </select>
                        <template v-if="selectedServiceId">
                            <input
                                @change="file = $event.target.files[0]"
                                type="file"
                                class="sm:col-span-6"/>
                            <button
                                type="submit"
                                class="w-full sm:col-span-6 justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                <user-icon class="w-5 me-auto text-indigo-white"/>
                                <span class="me-auto">Создать</span>
                            </button>
                        </template>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style>
.icon-fix {
    width: 20px;
}
</style>
