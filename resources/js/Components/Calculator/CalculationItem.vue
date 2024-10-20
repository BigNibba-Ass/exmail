<script setup>
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import ComparisonParamField from "@/Components/Calculator/ComparisonParamField.vue";
import {watch} from "vue";
import {MinusCircleIcon} from "@heroicons/vue/24/solid/index.js";

const props = defineProps({
    departure_points: Array,
    selectedComparisonParams: Array,
    comparisonParams: Array,
})

const emit = defineEmits(['remove'])

const item = defineModel({
    default: {
        terms: 'Указаны',
        where_from: 1,
        where_to: 1,
        weight: 0,
    }
})

watch(() => props.selectedComparisonParams, value => {
    item.value.exmail_sale = null
    item.value.exmail_markup = null
}, {deep: true})

watch(() => item.value['exmail_sale'], value => {
    if (item.value['exmail_sale'] !== null) {
        setTimeout(() => {
            item.value['exmail_markup'] = null
        }, 50)
        return
    }
}, {deep: true})

watch(() => item.value['exmail_markup'], value => {
    if ( item.value['exmail_markup'] !== null) {
        setTimeout(() => {
            item.value['exmail_sale'] = null
        }, 50)
        return
    }
}, {deep: true})
</script>

<template>
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <div class="sm:col-span-1">
            <label for="where-from" class="block text-sm font-medium text-gray-700">
                Откуда </label>
            <div class="mt-1">
                <v-select
                    id="where-from"
                    v-model="item.where_from"
                    :reduce="elem => elem.value" label="text"
                    :clearable="false"
                    :options="props.departure_points"/>
            </div>
        </div>
        <div class="sm:col-span-1">
            <label for="where-to" class="block text-sm font-medium text-gray-700">
                Куда </label>
            <div class="mt-1">
                <v-select
                    id="where-to"
                    v-model="item.where_to"
                    :reduce="elem => elem.value" label="text"
                    :clearable="false"
                    :options="props.departure_points"/>
            </div>
        </div>
        <div class="sm:col-span-1">
            <label for="weight" class="block text-sm font-medium text-gray-700">
                Вес </label>
            <div class="mt-1">
                <input type="number" id="weight"
                       step="0.01"
                       v-model="item.weight"
                       class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
            </div>
        </div>
        <div class="sm:col-span-1"
             v-for="param of props.selectedComparisonParams">
            <comparison-param-field
                v-model="item[props.comparisonParams[param].name]"
                :comparison-param="props.comparisonParams[param]"/>
        </div>
        <div class="sm:col-span-1 flex items-center">
            <button type="button" class="mt-5" @click.prevent="emit('remove')">
                <minus-circle-icon class="w-8 h-8 text-red-600"/>
            </button>
        </div>
    </div>
</template>
