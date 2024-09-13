<script setup>

import CustomSelect from "@/Components/CustomSelect.vue";

const props = defineProps({
    comparisonHold: Object,
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div>
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="shadow p-3 text-center border rounded-md">
                {{ props.comparisonHold.name }}
            </div>
            <input
                :id="props.comparisonHold.name"
                placeholder="Скидка, %"
                min="1"
                max="100"
                type="number"
                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
            <custom-select
                @update:modelValue="emit('update:modelValue', $event)"
                :placeholder="'Вид услуги ' + props.comparisonHold.name"
                :values="props.comparisonHold.services"/>
        </div>
    </div>
</template>
