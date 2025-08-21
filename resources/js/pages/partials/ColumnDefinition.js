import { h } from 'vue'
import { useContextMenu } from "@/composables/useContextMenu.js";
import { router } from '@inertiajs/vue3';

//TenantList
export function createColumns(onSelectDomainCallback) {
    return [
        {
            accessorKey: 'name',
            header: 'Name',
            // cell: ({row}) => h(useSelectedCellRender(row,'name'))
        },
        {
            accessorKey: 'status',
            header: 'Status',
            // cell: ({row}) => h(useSelectedCellRender(row,'name'))
        },
        {
            accessorKey: 'expires_at',
            header: 'Läuft ab',
            cell: ({row}) => {
                return row.original.expires_at ?? '-'
            }
        },
        {
            accessorKey: ' ',
            header: () => h('div', { class: 'text-right' }, ''),
            cell: ({ row }) => {
                const { renderDropdownMenu } = useContextMenu();

                const customMenuItems = [
                    {
                        type: 'item',
                        label: `DNS`,
                        onClick: () => {
                            router.visit(route('tenant.dns.show', { domain: row.original.id }));
                        }
                    },
                    {
                        type: 'item',
                        label: `Select`,
                        onClick: () => {
                            // row.toggleSelected()
                            onSelectDomainCallback(row.original)
                        }
                    },
                    {
                        type: 'separator',
                    },
                    {
                        type: 'item',
                        label: `Löschen`,
                        onClick: () => {
                            // row.toggleSelected()
                            window.alert(`Löschen momentan nicht unterstützt - id: ${ row.original.id }`);
                        }
                    },
                ];

                return renderDropdownMenu(row, customMenuItems, 'text-right font-medium');
            }
        }
    ];
}
