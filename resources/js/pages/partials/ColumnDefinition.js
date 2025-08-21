import { h } from 'vue'
import { useContextMenu } from "@/composables/useContextMenu.js";
import { router } from '@inertiajs/vue3';

//TenantList
export function createColumns(onSelectDomainCallback) {
    return [
        {
            accessorKey: 'order_id',
            header: 'Order ID',
            // cell: ({row}) => h(useSelectedCellRender(row,'name'))
        },
        {
            accessorKey: 'created_at',
            header: 'Created at',
            // cell: ({row}) => h(useSelectedCellRender(row,'name'))
        }
    ];
}
