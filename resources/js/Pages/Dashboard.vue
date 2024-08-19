<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ref} from "vue";
import ComparisonParamField from "@/Components/Calculator/ComparisonParamField.vue";
import CustomSelect from "@/Components/CustomSelect.vue";
import Modal from "@/Components/Modal.vue";
import {PlusCircleIcon} from "@heroicons/vue/24/solid/index.js";
import ComparisonHoldField from "@/Components/Calculator/ComparisonHoldField.vue";

const modals = ref({
    comparisonParams: false,
    comparableHolds: false,
})

const serviceTypes = [
    {text: 'Экспресс', value: 0},
    {text: 'Сборный груз Т-Т', value: 1},
    {text: 'Сборный груз Д-Д', value: 2},
    {text: 'Сборный груз Т-Д', value: 3},
    {text: 'Паллетная доставка', value: 4},
]

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

const selectedComparableHolds = ref([])

const comparableHoldsAvailable = ref([
    {id: 0, name: 'DPD', discountValue: 0},
    {id: 1, name: 'MajorExpress', discountValue: 0},
    {id: 2, name: 'PonyExpress', discountValue: 0},
    {id: 3, name: 'КСЭ', discountValue: 0},
])
</script>

<template>
    <Head title="Главная"/>

    <AuthenticatedLayout>
        <Modal :show="modals.comparisonParams" @close="modals.comparisonParams = false">
            <div class="p-6">
                <h2 class="text-lg text-center font-medium text-gray-900">
                    Выберите параметры для сравнения
                </h2>

                <fieldset>
                    <div class="mt-4 space-y-4">
                        <div class="relative flex items-start" v-for="(param, key) of comparisonParams">
                            <div class="flex items-center h-5">
                                <input :id="param.name + '_checkbox-param'" type="checkbox"
                                       v-model="selectedComparisonParams"
                                       :value="key"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"/>
                            </div>
                            <div class="ml-3 text-sm">
                                <label :for="param.name + '_checkbox-param'" class="font-medium text-gray-700">{{
                                        param.label
                                    }}</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </div>
        </Modal>
        <Modal :show="modals.comparableHolds" @close="modals.comparableHolds = false">
            <div class="p-6">
                <h2 class="text-lg text-center font-medium text-gray-900">
                    Выберите конкурентов для сравнения
                </h2>

                <fieldset>
                    <div class="mt-4 space-y-4">
                        <div class="relative flex items-start" v-for="(param, key) of comparableHoldsAvailable">
                            <div class="flex items-center h-5">
                                <input :id="param.name + '_checkbox-hold'" type="checkbox"
                                       v-model="selectedComparableHolds"
                                       :value="key"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"/>
                            </div>
                            <div class="ml-3 text-sm">
                                <label :for="param.name + '_checkbox-hold'" class="font-medium text-gray-700">{{
                                        param.name
                                    }}</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </div>
        </Modal>

        <div class="py-4">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-full">
                    <div class="w-full flex">
                        <button type="button"
                                @click.prevent="modals.comparisonParams = true"
                                class="ms-auto inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-bl-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
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
                                            :values="serviceTypes"/>
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
                                <div class="sm:col-span-6"
                                     v-for="param of selectedComparableHolds">
                                    <comparison-hold-field
                                        :comparison-hold="comparableHoldsAvailable[param]"/>
                                </div>
                                <div class="sm:col-span-6 text-center">
                                    <div class="sm:col-span-1 flex items-center">
                                        <button @click.prevent="modals.comparableHolds = true">
                                            <plus-circle-icon class="w-8 h-8 text-indigo-600"/>
                                        </button>
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="nds_included" class="font-medium text-gray-700">
                                        С НДС
                                    </label>
                                    <input id="nds_included"
                                           type="checkbox"
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded ml-2"/>
                                </div>
                                <div class="sm:col-span-6">
                                    <button type="button"
                                            class="ms-auto inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Рассчитать
                                    </button>
                                </div>
                                <div class="sm:col-span-6">
                                    <div class="flex flex-col">
                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                <div
                                                    class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                        <tr>
                                                            <th rowspan="2" class="relative px-6 py-3">
                                                                Откуда
                                                            </th>
                                                            <th rowspan="2" class="relative px-6 py-3">
                                                                Куда
                                                            </th>
                                                            <th rowspan="2" class="relative px-6 py-3">
                                                                Вес
                                                            </th>
                                                            <th colspan="7" class="relative px-6 py-3 bg-green-200">
                                                                Тариф
                                                            </th>
                                                            <th colspan="4" class="relative px-6 py-3 bg-blue-200">
                                                                Срок
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-blue-500">
                                                                Маржа
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                ExMail
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                КСЭ/тт
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                Разница, %
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                Major/тт
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                Разница, %
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                DPD/тт
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                                Разница, %
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-blue-200">
                                                                ExMail
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-blue-200">
                                                                КСЭ
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-blue-200">
                                                                Major
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-blue-200">
                                                                DPD
                                                            </th>
                                                            <th rowspan="1" class="relative px-6 py-3 bg-blue-500">
                                                                ExMail
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr class="bg-white">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                                тест
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-500">
                                                                тест
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <button type="button"
                                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Выгрузить
                                    </button>
                                </div>
                                <div class="sm:col-span-3">
                                    <button type="button"
                                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Создать КП
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
