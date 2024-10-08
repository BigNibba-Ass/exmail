<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import {computed, ref, watch} from "vue";
import ComparisonParamField from "@/Components/Calculator/ComparisonParamField.vue";
import CustomSelect from "@/Components/CustomSelect.vue";
import Modal from "@/Components/Modal.vue";
import {PlusCircleIcon, PencilSquareIcon} from "@heroicons/vue/24/solid/index.js";
import ComparisonHoldField from "@/Components/Calculator/ComparisonHoldField.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import {Link} from "@inertiajs/vue3";
import {getElementByKey, prettifyNumber, priceValue} from "../Traits.js";
import CalculationItem from "@/Components/Calculator/CalculationItem.vue";

const form = ref({
    exmail_service_id: null,
    calculation_items: [],
    selected_comparable_services: [],
    nds_included: false,
})

const props = defineProps({
    exmail_services: Array,
    companies: Array,
    departure_points: Array,
    prices: Array
})


const modals = ref({
    comparisonParams: false,
    comparableHolds: false,
})

const selectedComparisonParams = ref([])

const selectedComparableHolds = ref([])

const comparisonParams = [
    {
        name: 'exmail_sale',
        label: 'Скидка ExMail, %',
        type: 'input',
        dependentlyDisabled: 'exmail_markup',
        attributes: {
            type: 'number',
            min: 0,
            step: 0.01,
        }
    },
    {
        name: 'exmail_markup',
        label: 'Маржа ExMail, %',
        type: 'input',
        dependentlyDisabled: 'exmail_sale',
        attributes: {
            type: 'number',
            min: 0,
            step: 0.01,
        },
    },
    {
        name: 'terms',
        label: 'Сроки',
        type: 'input',
        attributes: {
            value: 'Указаны',
            placeholder: 'Указаны',
            disabled: true,
        },
    },
]

const comparisonParamsHas = (name) => {
    for (const param of selectedComparisonParams.value) {
        if (comparisonParams[param].name === name) return true
    }
    return false
}

const calculate = () => {
    const formToSend = Object.assign({}, form.value)
    formToSend.selected_comparable_services = []
    for (const companyServices of form.value.selected_comparable_services) {
        if (companyServices) {
            formToSend.selected_comparable_services.push(companyServices[0])
        }
    }
    router.post(route('calculate'), formToSend, {
        onError: (err) => {
            alert(Object.values(err)[0])
        },
    })
}

const pushComparableService = (company, service) => {
    form.value.selected_comparable_services[company] = []
    form.value.selected_comparable_services[company].push(service)
}

const addCalculationItem = () => {
    form.value.calculation_items.push({
        exmail_sale: null,
        exmail_markup: null,
        terms: 'Указаны',
        where_from: 1,
        where_to: 1,
        weight: 0,
    })
}

const removeCalculationItem = (index) => {
    form.value.calculation_items.splice(index, 1); // 2nd parameter means remove one item only
}

watch(form, value => {
    // console.log(form)
}, {deep: true})
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
                                       :disabled="param.dependentlyDisabled ? comparisonParamsHas(param.dependentlyDisabled) : false"
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
                        <div class="relative flex items-start" v-for="(param, key) of props.companies">
                            <div class="flex items-center h-5">
                                <input :id="param.name + '_checkbox-hold'" type="checkbox"
                                       v-model="selectedComparableHolds"
                                       :value="param.id"
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
        <div class="w-full">
            <form @submit.prevent="calculate">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6 text-center">
                        <h2 class="text-2xl font-bold">
                            Расчет
                        </h2>
                    </div>
                    <div class="sm:col-span-3">
                        <label for="service-type" class="block text-sm font-medium text-gray-700">
                            Вид услуги ExMail
                        </label>
                        <div class="mt-1">
                            <custom-select
                                id="service-type"
                                placeholder="Услуга"
                                v-model="form.exmail_service_id"
                                :values="props.exmail_services"/>
                        </div>
                    </div>
                    <div class="sm:col-span-6 text-center mt-5">
                        <div class="flex items-center justify-center gap-x-4">
                            <h2 class="text-xl font-bold">
                                Конкурент для сравнения
                            </h2>
                            <button type="button" @click.prevent="modals.comparableHolds = true">
                                <pencil-square-icon class="w-5 h-5 text-indigo-600"/>
                            </button>
                        </div>
                    </div>
                    <div class="sm:col-span-6"
                         v-for="param of selectedComparableHolds">
                        <comparison-hold-field
                            @update:modelValue="pushComparableService(param, $event)"
                            :comparison-hold="getElementByKey(props.companies, param, 'id')"/>
                    </div>
                    <div class="sm:col-span-6 text-center mt-5">
                        <div class="flex items-center justify-center gap-x-4">
                            <h2 class="text-xl font-bold">
                                Параметры для сравнения
                            </h2>
                            <button type="button" @click.prevent="modals.comparisonParams = true">
                                <pencil-square-icon class="w-5 h-5 text-indigo-600"/>
                            </button>
                        </div>
                    </div>

                    <calculation-item
                        v-for="(item, i) of form.calculation_items"
                        v-model="form.calculation_items[i]"
                        @remove="removeCalculationItem(i)"
                        class="sm:col-span-6"
                        :departure_points="props.departure_points"
                        :selected-comparison-params="selectedComparisonParams"
                        :comparison-params="comparisonParams"
                    />


                    <div class="sm:col-span-1 flex items-center">
                        <button type="button" class="mt-5" @click.prevent="addCalculationItem">
                            <plus-circle-icon class="w-8 h-8 text-indigo-600"/>
                        </button>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="nds_included" class="font-medium text-gray-700">
                            С НДС
                        </label>
                        <input id="nds_included"
                               :checked="form.nds_included" @change="form.nds_included = $event.target.checked"
                               type="checkbox"
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded ml-2"/>
                    </div>
                    <div class="sm:col-span-6">
                        <button type="submit"
                                class="ms-auto inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            Рассчитать
                        </button>
                    </div>
                </div>
                <div class="sm:col-span-6 mt-5">
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
                                            <th :colspan="1 + selectedComparableHolds.length * 2"
                                                class="relative px-6 py-3 bg-green-200">
                                                Тариф
                                            </th>
                                            <th v-if="comparisonParamsHas('terms')"
                                                :colspan="1 + selectedComparableHolds.length"
                                                class="relative px-6 py-3 bg-blue-200">
                                                Сроки
                                            </th>
                                            <th v-if="comparisonParamsHas('exmail_markup')" rowspan="1"
                                                class="relative px-6 py-3 bg-blue-500">
                                                Маржа
                                            </th>
                                        </tr>
                                        <tr>
                                            <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                ExMail
                                            </th>
                                            <template v-for="company of selectedComparableHolds">
                                                <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                    {{ getElementByKey(props.companies, company, 'id').name }}
                                                </th>
                                                <th rowspan="1" class="relative px-6 py-3 bg-green-200">
                                                    Разница, %
                                                </th>
                                            </template>
                                            <template v-if="comparisonParamsHas('terms')">
                                                <th rowspan="1"
                                                    class="relative px-6 py-3 bg-blue-200">
                                                    ExMail
                                                </th>
                                                <td v-for="company of selectedComparableHolds"
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                    {{ getElementByKey(props.companies, company, 'id').name }}
                                                </td>
                                            </template>
                                            <th rowspan="1" v-if="comparisonParamsHas('exmail_markup')"
                                                class="relative px-6 py-3 bg-blue-500">
                                                ExMail
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="bg-white" v-for="item of $page.props.flash.data">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                {{
                                                    item.misc.where_from
                                                }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                {{
                                                    item.misc.where_to
                                                }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                {{ item.misc.weight }} кг
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                Цена: {{
                                                    priceValue(item?.exmail?.price) || 'Не рассчитано'
                                                }}
                                                <template v-if="comparisonParamsHas('exmail_sale')">
                                                    <br/>
                                                    Цена со скидкой: {{
                                                        priceValue(item?.exmail?.price_with_sale) || 'Не рассчитано'
                                                    }}
                                                </template>
                                                <template v-if="comparisonParamsHas('exmail_markup')">
                                                    <br/>
                                                    Цена при марже: {{
                                                        priceValue(item?.exmail?.price_with_markup) || 'Не рассчитано'
                                                    }}
                                                </template>
                                            </td>
                                            <template v-for="company of selectedComparableHolds">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                    {{
                                                        priceValue(item?.[company]?.price) || 'Не рассчитано'
                                                    }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                    Цены: {{
                                                        prettifyNumber(((item?.exmail?.price -
                                                                item?.[company]?.price) /
                                                            item?.exmail?.price) * 100)
                                                    }} %
                                                    <template v-if="comparisonParamsHas('exmail_sale')">
                                                        <br/>
                                                        Цены со скидкой: {{
                                                            prettifyNumber(((item?.exmail?.price_with_sale -
                                                                    item?.[company]?.price) /
                                                                item?.exmail?.price_with_sale) * 100)
                                                        }} %
                                                    </template>
                                                    <template v-if="comparisonParamsHas('exmail_markup')">
                                                        <br/>
                                                        Цены с маржой: {{
                                                            prettifyNumber(((item?.exmail?.price_with_markup -
                                                                    item?.[company]?.price) /
                                                                item?.exmail?.price_with_markup) * 100)
                                                        }} %
                                                    </template>
                                                </td>
                                            </template>
                                            <template v-if="comparisonParamsHas('terms')">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                    {{
                                                        item?.exmail?.terms ? item?.exmail?.terms + " д." : 'Не указано'
                                                    }}
                                                </td>
                                                <td v-for="company of selectedComparableHolds"
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-200">
                                                    {{
                                                        item?.[company]?.terms ? item?.[company]?.terms + " д." : 'Не указано'
                                                    }}
                                                </td>
                                            </template>
                                            <td v-if="comparisonParamsHas('exmail_markup')"
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-blue-500">
                                                {{
                                                    prettifyNumber(item?.exmail?.markup) + ' %' || 'Невозможно расчитать'
                                                }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mt-5">
                <div class="sm:col-span-3">
                    <button type="button"
                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Выгрузить
                    </button>
                </div>
                <div class="sm:col-span-3">
                    <Link type="button"
                          :href="route('offer-test')"
                          class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Создать КП
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
