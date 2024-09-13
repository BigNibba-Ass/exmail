<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {ref, watch} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";

import {PencilIcon} from "@heroicons/vue/20/solid/index.js";
import Modal from "@/Components/Modal.vue";
import CustomSelect from "@/Components/CustomSelect.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    services: Array,
    companies_select: Array,
    companies: Array
})
const modals = ref({
    serviceModal: {
        shown: false,
        service: {
            id: null,
            name: "",
            company_id: 1
        }
    },
    companyModal: {
        shown: false,
        company: {
            id: null,
            name: "",
        }
    },
})

watch(modals, (v) => {
}, {deep: true})

const addService = () => {
    modals.value.serviceModal.shown = true
    modals.value.serviceModal.service = {
        id: null,
        name: "",
        company_id: 1
    }
}

const addCompany = () => {
    modals.value.companyModal.shown = true
    modals.value.companyModal.company = {
        id: null,
        name: "",
    }
}

const submitService = () => {
    const options = {
        onSuccess: () => {
            modals.value.serviceModal.shown = false
        },
        onError: (err) => {
            console.log(err)
        }
    }
    if (modals.value.serviceModal.service.id) {
        router.patch(route('admin.services.update', modals.value.serviceModal.service.id), modals.value.serviceModal.service, options)
    } else {
        router.post(route('admin.services.store'), modals.value.serviceModal.service, options)
    }
}

const submitCompany = () => {
    const options = {
        onSuccess: () => {
            modals.value.companyModal.shown = false
        },
        onError: (err) => {
            console.log(err)
        }
    }
    if (modals.value.companyModal.company.id) {
        router.patch(route('admin.companies.update', modals.value.companyModal.company.id), modals.value.companyModal.company, options)
    } else {
        router.post(route('admin.companies.store'), modals.value.companyModal.company, options)
    }
}

const showService = (service) => {
    modals.value.serviceModal.service = service
    modals.value.serviceModal.shown = true
}

const showCompany = (company) => {
    modals.value.companyModal.company = company
    modals.value.companyModal.shown = true
}
</script>

<template>
    <AdminLayout name="Основной экран">
        <Modal :show="modals.serviceModal.shown" @close="modals.serviceModal.shown = false">
            <div class="p-6">
                <h2 class="text-lg text-center font-medium text-gray-900">
                    Услуга
                </h2>

                <form @submit.prevent="submitService()">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Название </label>
                        <div class="mt-1">
                            <input v-model="modals.serviceModal.service.name"
                                   type="text" id="name"
                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="service-type" class="block text-sm font-medium text-gray-700">
                            Тип </label>
                        <div class="mt-1">
                            <custom-select
                                id="service-type"
                                v-model="modals.serviceModal.service.company_id"
                                :values="props.companies_select"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button
                            type="submit"
                            class="w-full transition justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-green-700">
                            <pencil-icon class="w-5 me-auto text-indigo-white"/>
                            <span class="me-auto">Сохранить</span>
                        </button>
                    </div>
                </form>

            </div>
        </Modal>
        <Modal :show="modals.companyModal.shown" @close="modals.companyModal.shown = false">
            <div class="p-6">
                <h2 class="text-lg text-center font-medium text-gray-900">
                    Компания
                </h2>

                <form @submit.prevent="submitCompany()">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Название </label>
                        <div class="mt-1">
                            <input v-model="modals.companyModal.company.name"
                                   type="text" id="name"
                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button
                            type="submit"
                            class="w-full transition justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-green-700">
                            <pencil-icon class="w-5 me-auto text-indigo-white"/>
                            <span class="me-auto">Сохранить</span>
                        </button>
                    </div>
                </form>

            </div>
        </Modal>
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    Основной экран
                </h2>
            </div>
            <form class="sm:col-span-6">
                <div class="grid grid-cols-1 mt-5 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <h2 class="text-lg font-medium text-gray-900">
                            Виды услуг
                            <button
                                type="button"
                                class="px-2 ms-3 justify-center inline-flex items-center p-1 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                                @click.prevent="addService">
                                Добавить
                            </button>
                        </h2>
                    </div>
                    <!--                    TODO: убрать по компонентам-->
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <fieldset>
                            <div class="mt-4 space-y-4">
                                <div class="relative flex items-start" v-for="(param, key) of props.services">
                                    <div class="ml-3 text-sm">
                                        <label :for="param.name + '_checkbox-param'"
                                               class="font-medium flex text-gray-700">{{
                                                param.name
                                            }}
                                            <pencil-icon class="w-5 ms-5 text-indigo-600 cursor-pointer"
                                                         @click.prevent="showService(param)"/>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <h2 class="text-lg font-medium text-gray-900">
                            Конкуренты
                            <button
                                type="button"
                                class="px-2 ms-3 justify-center inline-flex items-center p-1 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                                @click.prevent="addCompany">
                                Добавить
                            </button>
                        </h2>
                    </div>
                    <!--                    TODO: убрать по компонентам-->
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <fieldset>
                            <div class="mt-4 space-y-4">
                                <div class="relative flex items-start" v-for="(param, key) of props.companies">
                                    <div class="ml-3 text-sm">
                                        <label :for="param.name + '_checkbox-param'"
                                               class="font-medium flex text-gray-700">{{
                                                param.name
                                            }}
                                            <pencil-icon class="w-5 ms-5 text-indigo-600 cursor-pointer"
                                                         @click.prevent="showCompany(param)"/>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
