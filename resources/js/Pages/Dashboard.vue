<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import {computed, ref, watch} from "vue";
import ComparisonParamField from "@/Components/Calculator/ComparisonParamField.vue";
import CustomSelect from "@/Components/CustomSelect.vue";
import Modal from "@/Components/Modal.vue";
import {PlusCircleIcon, PencilSquareIcon, ArrowDownOnSquareIcon} from "@heroicons/vue/24/solid/index.js";
import ComparisonHoldField from "@/Components/Calculator/ComparisonHoldField.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import {Link} from "@inertiajs/vue3";
import {getElementByKey, prettifyNumber, priceValue} from "../Traits.js";
import CalculationItem from "@/Components/Calculator/CalculationItem.vue";
import {Switch} from "@headlessui/vue";
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import {CFB, read, utils, write} from 'xlsx'
import {
    saveAs
} from 'file-saver'
import * as XLS from "xlsx";
import {collect} from "collect.js";
import OfferComponent from "@/Components/OfferComponent.vue";
import NProgress from "nprogress";
import html2canvas from "html2canvas";
import jsPDF from "jspdf";

const form = ref({
    exmail_service_id: null,
    calculation_items: [],
    selected_comparable_services: [],
    nds_included: false,
    is_in_top_mode: false,
    top_where_from: 1,
    top_exmail_sale: null,
    top_exmail_markup: null,
})

const props = defineProps({
    exmail_services: Array,
    companies: Array,
    departure_points: Array,
    prices: Array,
    csrf_token: String,
    auth: Object,
})

const modals = ref({
    comparisonParams: false,
    comparableHolds: false,
    offer: false,
})

const selectedComparisonParams = ref([])

const selectedComparableHolds = ref([])

const comparisonParams = [
    {
        name: 'exmail_sale',
        label: 'Скидка ExMail, %',
        type: 'input',
        attributes: {
            type: 'number',
            min: 0,
            step: 0.01,
        },
    },
    {
        name: 'exmail_markup',
        label: 'Маржа ExMail, %',
        type: 'input',
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
    let formToSend = Object.assign({}, form.value)
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

const page = usePage()

const exportTable = () => {
    let wb = {
        Sheets: [],
        SheetNames: [],
        Props: {
            raw: true
        },

    }
    utils.book_append_sheet(wb, utils.table_to_sheet(document.querySelector('#main-table'), {
        raw: true
    }), 'Основной Расчет')
    if (page.props.flash.data?.top?.length) {
        utils.book_append_sheet(wb, utils.table_to_sheet(document.querySelector('#top-table'), {
            raw: true
        }), 'Топ 100')
    }
    let wbout = write(wb, {
        bookType: 'xlsx',
        bookSST: true,
        type: 'binary'
    })

    function s2ab(s) {

        let buf = new ArrayBuffer(s.length)
        let view = new Uint8Array(buf)
        for (let i = 0; i != s.length; i++) view[i] = s.charCodeAt(i) & 0xFF
        return buf
    }

    saveAs(new Blob([s2ab(wbout)], {
        type: "text/plain;charset=utf-8"
    }), 'calculations.xlsx')
}

watch(selectedComparisonParams, value => {
    form.value.top_exmail_sale = null
    form.value.top_exmail_markup = null
}, {deep: true})

watch(() => form.value.top_exmail_sale, value => {
    if (form.value.top_exmail_sale !== null) {
        setTimeout(() => {
            form.value.top_exmail_markup = null
        }, 50)
        return
    }
}, {deep: true})

watch(() => form.value.top_exmail_markup, value => {
    if (form.value.top_exmail_markup !== null) {
        setTimeout(() => {
            form.value.top_exmail_sale = null
        }, 50)
        return
    }
}, {deep: true})

const currentOffer = ref(null)

const storeOffer = () => {
    router.post(route('offers.store'), {
        data: page.props.flash,
    }, {
        onSuccess: () => {
            currentOffer.value = page.props.flash.offer.data
            console.log(currentOffer.value)
            modals.value.offer = true
        },
        onError: (err) => {
            console.log(err)
        }
    })
}
const pageIsLoading = ref(false)

const download = async () => {
    pageIsLoading.value = true
    NProgress.start()
    const canvases = []
    for (let elem of document.querySelectorAll("[id*='pf']")) {
        const canvas = await html2canvas(elem, {
            scale: 4,
        })
        canvases.push(canvas)
    }
    let pdf = new jsPDF('p', 'px', [1788, 2528]);
    let i = 0
    for (const elem of canvases) {
        let imgData = elem.toDataURL("image/jpeg", 1.0);
        pdf.addImage(imgData, 'JPEG', 0, 0, 1788, 2528, "alias" + i)
        if(i < canvases.length - 1) {
            pdf.addPage()
        }
        i++
    }
    pdf.save()
    pageIsLoading.value = false
    NProgress.done()
    NProgress.remove()
}


const uploadFile = (event) => {
    let reader = new FileReader();
    reader.onload = function () {
        let arrayBuffer = this.result,
            array = new Uint8Array(arrayBuffer),
            binaryString = String.fromCharCode.apply(null, array);
        let workbook = read(binaryString, {
            type: "binary"
        });
        let first_sheet_name = workbook.SheetNames[0];
        let worksheet = workbook.Sheets[first_sheet_name];
        for (const elem of utils.sheet_to_json(worksheet, {
            raw: true
        })) {
            const whereFrom = collect(props.departure_points).where('text', '==', elem['Откуда']).first().value
            const whereTo = collect(props.departure_points).where('text', '==', elem['Куда']).first().value
            form.value.calculation_items.push({
                exmail_sale: null,
                exmail_markup: null,
                terms: 'Указаны',
                where_from: whereFrom,
                where_to: whereTo,
                weight: String(elem['Вес']).replace(' кг', ''),
            })
        }
    }
    reader.readAsArrayBuffer(event.target.files[0])
}
</script>

<template>
    <Head title="Главная"/>

    <AuthenticatedLayout v-model="pageIsLoading">
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
        <Modal :show="modals.offer" @close="modals.offer = false">
            <div class="p-6">
                <offer-component :user="props.auth.user" :offer="currentOffer" @download="download"/>
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
                    <div class="sm:col-span-6 text-center mt-5 gap-y-3 flex justify-center flex-col">
                        <div class="flex items-center justify-center gap-x-4">
                            <h2 class="text-xl font-bold">
                                Параметры для сравнения
                            </h2>
                            <button type="button" @click.prevent="modals.comparisonParams = true">
                                <pencil-square-icon class="w-5 h-5 text-indigo-600"/>
                            </button>
                            <button type="button" @click.prevent="$refs.uploadInput.click()">
                                <arrow-down-on-square-icon class="w-5 h-5 text-indigo-600"/>
                            </button>
                            <input
                                hidden
                                ref="uploadInput"
                                type="file"
                                @change="uploadFile($event)"
                            />
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

                    <div class="flex items-center justify-center gap-x-4 sm:col-span-6">
                        <Switch v-model="form.is_in_top_mode"
                                class="flex-shrink-0 group relative rounded-full inline-flex items-center justify-center h-5 w-10 cursor-pointer">
                            <span class="sr-only">Use setting</span>
                            <span aria-hidden="true"
                                  class="pointer-events-none absolute bg-white w-full h-full rounded-md"/>
                            <span aria-hidden="true"
                                  :class="[form.is_in_top_mode ? 'bg-indigo-600' : 'bg-gray-200', 'pointer-events-none absolute h-4 w-9 mx-auto rounded-full transition-colors ease-in-out duration-200']"/>
                            <span aria-hidden="true"
                                  :class="[form.is_in_top_mode ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none absolute left-0 inline-block h-5 w-5 border border-gray-200 rounded-full bg-white shadow transform ring-0 transition-transform ease-in-out duration-200']"/>
                        </Switch>
                        <p class="text-sm font-bold" v-if="form.is_in_top_mode">Вкл. "Топ 100"</p>
                        <p class="text-sm font-bold" v-else>Выкл. "Топ 100"</p>
                    </div>

                    <div class="sm:col-span-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"
                         v-if="form.is_in_top_mode">
                        <div class="sm:col-span-1">
                            <label for="where-from" class="block text-sm font-medium text-gray-700">
                                Откуда </label>
                            <div class="mt-1">
                                <v-select
                                    id="where-from"
                                    v-model="form.top_where_from"
                                    :reduce="elem => elem.value" label="text"
                                    :clearable="false"
                                    :options="props.departure_points"/>
                            </div>
                        </div>
                        <div class="sm:col-span-1"
                             v-for="param of selectedComparisonParams">
                            <comparison-param-field
                                v-model="form['top_'+comparisonParams[param].name]"
                                :comparison-param="comparisonParams[param]"/>
                        </div>
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
                                        <tr class="bg-white" v-for="item of $page.props.flash.data?.data">
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
                                                {{
                                                    priceValue(item?.exmail?.price) || 'Не рассчитано'
                                                }}
                                            </td>
                                            <template v-for="company of selectedComparableHolds">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                    {{
                                                        priceValue(item?.[company]?.price) || 'Не рассчитано'
                                                    }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center bg-green-200">
                                                    {{
                                                        prettifyNumber(((item?.exmail?.price -
                                                                item?.[company]?.price) /
                                                            item?.exmail?.price) * 100)
                                                    }}
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
                                    <table hidden id="main-table">
                                        <tr>
                                            <th>Откуда</th>
                                            <th>Куда</th>
                                            <th>Вес</th>
                                            <th>Тариф</th>
                                            <th v-if="comparisonParamsHas('terms')">Сроки</th>
                                        </tr>
                                        <tr v-if="$page.props.flash.data?.data?.length"
                                            v-for="item of $page.props.flash.data?.data">
                                            <td>{{ item.misc.where_from }}</td>
                                            <td>{{ item.misc.where_to }}</td>
                                            <td>{{ item.misc.weight }} кг</td>
                                            <td>{{ priceValue(item?.exmail?.price) }}</td>
                                            <td v-if="comparisonParamsHas('terms')">
                                                {{
                                                    item?.exmail?.terms ? item?.exmail?.terms + " д." : 'Не указано'
                                                }}
                                            </td>
                                        </tr>
                                    </table>
                                    <table hidden id="top-table">
                                        <tr>
                                            <th>Откуда</th>
                                            <th>Куда</th>
                                            <th>Вес до 0.25</th>
                                            <th>Вес до 0.5</th>
                                            <th>Вес до 1</th>
                                            <th>Каждый последующий кг</th>
                                        </tr>
                                        <template v-for="top in $page.props.flash.data?.top">
                                            <tr v-if="$page.props.flash.data?.top?.length">
                                                <td>{{ top['where_from'] }}</td>
                                                <td>{{ top['where_to'] }}</td>
                                                <td>{{ top['weight_0.24'] }}</td>
                                                <td>{{ top['weight_0.49'] }}</td>
                                                <td>{{ top['weight_0.99'] }}</td>
                                                <td>{{ top['additional_weight'] }}</td>
                                            </tr>
                                        </template>
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
                            @click.prevent="exportTable"
                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Выгрузить
                    </button>
                </div>
                <div class="sm:col-span-3">
                    <button type="button"
                            @click.prevent="storeOffer"
                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Создать КП
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
