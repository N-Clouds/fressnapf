<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef } from '@tanstack/vue-table'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

import {ref, watch} from 'vue'

import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
} from '@tanstack/vue-table'

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[]
    data: TData[]
}>()
const rowSelection = ref({})
const emit = defineEmits(['row:change'])


watch(rowSelection, (newSelection) => {
    emit('row:change', newSelection)
})

const table = useVueTable({
    get data() { return props.data },
    get columns() { return props.columns },
    getCoreRowModel: getCoreRowModel(),
    state: {
        get rowSelection() {
            return rowSelection.value
        },
    },
    enableRowSelection: true,
    enableMultiRowSelection: false,
    getRowId: row => row.id,
    onRowSelectionChange: updateOrValue => {
        rowSelection.value =
            typeof updateOrValue === 'function'
                ? updateOrValue(rowSelection.value)
                : updateOrValue
    },
})
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead
                    v-for="header in headerGroup.headers"
                    :key="header.id"
                    :style="header.column.columnDef.size ? `width: ${header.column.columnDef.size}px; max-width: ${header.column.columnDef.size}px;` : ''"
                    :class="[header.column.id === 'actions' ? 'text-right' : '', 'overflow-hidden whitespace-nowrap']"
                >
                    <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                </TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <template v-if="table.getRowModel().rows?.length">
                <template v-for="row in table.getRowModel().rows" :key="row.id">
                    <TableRow :data-state="row.getIsSelected() && 'selected'">
                        <TableCell
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                            :style="cell.column.columnDef.size ? `width: ${cell.column.columnDef.size}px; max-width: ${cell.column.columnDef.size}px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;` : ''"
                            :class="[cell.column.id === 'actions' ? 'text-right' : '', 'overflow-hidden whitespace-nowrap']"
                        >
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="row.getIsExpanded()">
                        <TableCell
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                            :style="cell.column.columnDef.size ? `width: ${cell.column.columnDef.size}px; max-width: ${cell.column.columnDef.size}px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;` : ''"
                            :class="[cell.column.id === 'actions' ? 'text-right' : '', 'overflow-hidden whitespace-nowrap']"
                        >
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </TableCell>
                    </TableRow>
                </template>
            </template>
            <TableRow v-else>
                <TableCell
                    :colspan="columns.length"
                    class="h-24 text-center"
                >
                    No results.
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
