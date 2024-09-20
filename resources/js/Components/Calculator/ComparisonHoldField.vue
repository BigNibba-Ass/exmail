<script setup>

import CustomSelect from "@/Components/CustomSelect.vue";
import {ref, watch} from "vue";

const props = defineProps({
    comparisonHold: Object,
})

const emit = defineEmits(['update:modelValue'])

const instance = ref({
    sale: null,
    service: null
})

watch(instance, value => {
    emit('update:modelValue', value)
}, {deep: true})
</script>

<template>
    <div>
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="shadow p-3 text-center border rounded-md">
                {{ props.comparisonHold.name }}
            </div>
            <input
                :id="props.comparisonHold.name"
                v-model="instance.sale"
                placeholder="Скидка, %"
                min="0"
                max="100"
                type="number"
                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
            <custom-select
                v-model="instance.service"
                :placeholder="'Вид услуги ' + props.comparisonHold.name"
                :values="props.comparisonHold.services"/>
        </div>
    </div>
</template>
