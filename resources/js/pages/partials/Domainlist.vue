<script setup lang="ts">

import {
    Button
} from '@/components/ui/button'
import DataTable from '@/components/DataTable.vue';
import {createColumns} from './ColumnDefinition';
import { ref } from 'vue';

const selectedDomain = ref<App.Models.Domain | null>(null)
const selectDomainCallback = (domain: App.Models.Domain) => {
    selectedDomain.value = null
    selectedDomain.value = domain;
}

const columns = createColumns(selectDomainCallback)

defineProps<{
    domains: App.Models.Domain[]
}>()

</script>

<template>
    <div class="px-6 py-4">
        <DataTable class="px-3" :data="domains" :columns="columns"/>
        <Drawer v-if="selectedDomain" @release="selectedDomain = null" :open="selectedDomain !== null">
            <DrawerContent>
                <div class="mx-auto w-full max-w-sm">
                    <DrawerHeader>
                        <DrawerTitle>Zone: {{ selectedDomain.name }}</DrawerTitle>
                        <DrawerDescription>{{ selectedDomain.status }}</DrawerDescription>
                    </DrawerHeader>
                    <div class="p-4 pb-0 overflow-y-scroll">
                        <pre>{{selectedDomain}}</pre>
                    </div>
                    <DrawerFooter>
<!--                        <Button>Submit</Button>-->
                        <DrawerClose as-child>
                            <Button @click="selectedDomain = null">
                                Cancel
                            </Button>
                        </DrawerClose>
                    </DrawerFooter>
                </div>
            </DrawerContent>
        </Drawer>    </div>
</template>

<style scoped>

</style>
