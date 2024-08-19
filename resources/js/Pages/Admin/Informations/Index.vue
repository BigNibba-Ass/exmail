<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {ref} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";

import {PencilIcon, ArrowPathIcon} from "@heroicons/vue/20/solid/index.js";
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

const comparableHoldsAvailable = ref([
    {id: 0, name: 'DPD', discountValue: 0},
    {id: 1, name: 'MajorExpress', discountValue: 0},
    {id: 2, name: 'PonyExpress', discountValue: 0},
    {id: 3, name: 'КСЭ', discountValue: 0},
])

const serviceTypes = [
    {text: 'Экспресс', value: 0},
    {text: 'Сборный груз Т-Т', value: 1},
    {text: 'Сборный груз Д-Д', value: 2},
    {text: 'Сборный груз Т-Д', value: 3},
    {text: 'Паллетная доставка', value: 4},
]

const tabs = [
    {name: 'Exmail'},
    {name: 'Конкуренты'},
]

const currentTab = ref(0)
</script>

<template>
    <!--                    TODO: переделать это все-->

    <AdminLayout name="Основной экран">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    Информационная база
                </h2>
            </div>
            <ul class="flex col-span-6 flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                <li class="me-2" v-for="(tab, key) of tabs">
                    <a
                        :class="currentTab === key ? 'text-white bg-blue-600' : 'hover:text-gray-900 hover:bg-gray-300'"
                        href="#" @click.prevent="currentTab = key"
                        class="inline-block px-4 py-3 rounded-lg transition" aria-current="page">{{
                            tab.name
                        }}</a>
                </li>
            </ul>
            <div v-if="currentTab === 0" class="sm:col-span-2">
                <button
                    class="w-full justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <arrow-path-icon class="w-5 me-auto text-indigo-white"/>
                    <span class="me-auto">Телефонный справочник/Сроки доставки ExMail</span>
                </button>
                <button
                    class="w-full mt-5 justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <arrow-path-icon class="w-5 me-auto text-indigo-white"/>
                    <span class="me-auto">Шаблоны КП</span>
                </button>
            </div>
            <form class="sm:col-span-6" v-if="currentTab === 1">
                <div class="grid grid-cols-1 mt-5 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <h2 class="text-lg font-medium text-gray-900">
                            Тарифный справочник
                        </h2>
                    </div>
<!--                    TODO: убрать по компонентам-->
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <fieldset>
                            <div class="mt-4 space-y-4">
                                <div class="relative flex items-start" v-for="(param, key) of serviceTypes">
                                    <div class="flex items-center h-5">
<!--                                        v-model="selectedComparisonParams"-->
                                        <input :id="param.text + '_checkbox-param'" type="checkbox"
                                               :value="key"
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"/>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label :for="param.text + '_checkbox-param'" class="font-medium flex text-gray-700">{{
                                                param.text
                                            }}
                                            <pencil-icon class="w-5 ms-5 text-indigo-600 cursor-pointer"/>

                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
                <div class="grid grid-cols-1 mt-5 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <h2 class="text-lg font-medium text-gray-900">
                            Сроки доставки
                        </h2>
                    </div>
<!--                    TODO: убрать по компонентам-->
                    <div class="sm:col-span-6 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                        <fieldset>
                            <div class="mt-4 space-y-4">
                                <div class="relative flex items-start" v-for="(param, key) of serviceTypes">
                                    <div class="flex items-center h-5">
<!--                                        v-model="selectedComparisonParams"-->
                                        <input :id="param.text + '_checkbox-param'" type="checkbox"
                                               :value="key"
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"/>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label :for="param.text + '_checkbox-param'" class="font-medium flex text-gray-700">{{
                                                param.text
                                            }}
                                            <pencil-icon class="w-5 ms-5 text-indigo-600 cursor-pointer"/>

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

<style>
.icon-fix {
    width: 20px;
}
</style>
