<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ref} from "vue";
import ComparisonParamField from "@/Components/Calculator/ComparisonParamField.vue";
import CustomSelect from "@/Components/CustomSelect.vue";
import Modal from "@/Components/Modal.vue";
import {PlusCircleIcon} from "@heroicons/vue/24/solid/index.js";

const modals = ref({
    comparisonParams: false,
})

const selectedComparisonParams = ref([])

const comparisonParams = [
    {
        name: 'weight',
        label: 'Вес, кг',
        type: 'input',
        attributes: {
            type: 'number',
            min: 0,
        }
    },
    {
        name: 'discount',
        label: 'Скидка, %',
        type: 'input',
        attributes: {
            type: 'number',
            min: 0,
        }
    },
    {
        name: 'markup',
        label: 'Маржа, %',
        type: 'input',
        attributes: {
            type: 'number',
            min: 0,
        },
    },
    // TODO: добавить/не добавлять (узнать что из этого) пункт параметров для сравнения
    {
        name: 'price_option',
        label: 'Тариф',
        type: 'select',
        attributes: {
            values: [],
        },
    },
    {
        name: 'area',
        label: 'Зона',
        type: 'select',
        attributes: {
            values: [],
        },
    },
    {
        name: 'terms',
        label: 'Сроки',
        type: 'select',
        attributes: {
            values: [
                {text: 'ExMail', value: 0},
                {text: 'Срок конкурента', value: 1},
            ],
        },
    },
]
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        <Modal :show="modals.comparisonParams" @close="modals.comparisonParams = false">
            <div class="p-6">
                <h2 class="text-lg text-center font-medium text-gray-900">
                    Выберите параметры для сравнения
                </h2>

                <fieldset>
                    <div class="mt-4 space-y-4">
                        <div class="relative flex items-start" v-for="(param, key) of comparisonParams">
                            <div class="flex items-center h-5">
                                <input :id="param.name + '_checkbox'" type="checkbox" v-model="selectedComparisonParams"
                                       :value="key"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"/>
                            </div>
                            <div class="ml-3 text-sm">
                                <label :for="param.name + '_checkbox'" class="font-medium text-gray-700">{{
                                        param.label
                                    }}</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </div>
        </Modal>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-full">
                    <div class="w-full flex">
                        <button type="button"
                                @click.prevent="modals.comparisonParams = true"
                                class="ms-auto inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-b-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            Параметры для сравнения
                        </button>
                    </div>
                    <div class="w-full flex p-5">
                        <form class="w-full">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-6 text-center">
                                    <h2 class="text-2xl font-bold">
                                        Расчет
                                    </h2>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="service-type" class="block text-sm font-medium text-gray-700">
                                        Вид услуги </label>
                                    <div class="mt-1">
                                        <custom-select
                                            id="service-type"
                                            :values="[
                                            {text: 'Экспресс', value: 0},
                                            {text: 'Сборный груз Т-Т', value: 1},
                                            {text: 'Сборный груз Д-Д', value: 2},
                                            {text: 'Сборный груз Т-Д', value: 3},
                                            {text: 'Паллетная доставка', value: 4},
                                            ]"/>
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Загрузить файл (какой?) </label>
                                    <div class="mt-1">
                                        <input type="file">
                                    </div>
                                </div>
                                <div class="sm:col-span-6 text-center mt-5">
                                    <h2 class="text-xl font-bold">
                                        Параметры для сравнения
                                    </h2>
                                </div>
                                <div class="sm:col-span-1">
                                    <label for="where-from" class="block text-sm font-medium text-gray-700">
                                        Откуда </label>
                                    <div class="mt-1">
                                        <input type="text" id="where-from"
                                               class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                                    </div>
                                </div>
                                <div class="sm:col-span-1">
                                    <label for="where-to" class="block text-sm font-medium text-gray-700">
                                        Куда </label>
                                    <div class="mt-1">
                                        <input type="text" id="where-to"
                                               class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                                    </div>
                                </div>
                                <div class="sm:col-span-1"
                                     v-for="param of selectedComparisonParams">
                                    <comparison-param-field
                                        :comparison-param="comparisonParams[param]"/>
                                </div>
                                <div class="sm:col-span-1 flex items-center">
                                   <button class="mt-5" @click.prevent="modals.comparisonParams = true">
                                       <plus-circle-icon class="w-8 h-8 text-indigo-600"/>
                                   </button>
                                </div>
                                <div class="sm:col-span-6 text-center mt-5">
                                    <h2 class="text-xl font-bold">
                                        Конкурент для сравнения
                                    </h2>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
