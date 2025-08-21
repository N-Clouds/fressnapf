<script setup lang="ts">

import {
    Button
} from '@/components/ui/button'
import DataTable from '@/components/DataTable.vue';
import {createColumns} from './ColumnDefinition';
import { ref } from 'vue';

// const selectedDomain = ref<App.Models.Domain | null>(null)
// const selectDomainCallback = (domain: App.Models.Domain) => {
//     selectedDomain.value = null
//     selectedDomain.value = domain;
// }

const columns = createColumns()

defineProps({
    orders: Array
})

</script>

<template>
    <div class="px-6 py-4">
        <DataTable class="px-3" :data="orders ?? []" :columns="columns"/>
    </div>
</template>

<style scoped>

</style>
