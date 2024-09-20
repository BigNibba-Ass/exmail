<script setup>
import CustomSelect from "@/Components/CustomSelect.vue";

const props = defineProps({
    comparisonParam: Object
})
</script>

<template>
    <div>
        <label :for="props.comparisonParam.name" class="block text-sm font-medium text-gray-700">
            {{ props.comparisonParam.label }} </label>
        <div class="mt-1">
            <input
                :id="props.comparisonParam.name"
                v-if="comparisonParam.type === 'input'"
                v-bind="comparisonParam.attributes"
                v-on="comparisonParam.events"
                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
            <custom-select
                v-on="comparisonParam.events"
                :id="props.comparisonParam.name"
                v-else-if="comparisonParam.type === 'select'"
                v-bind="comparisonParam.attributes"/>
        </div>
    </div>
</template>
