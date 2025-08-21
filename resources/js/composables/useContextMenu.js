// useContextMenu.js
import {h} from 'vue';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSub,
    DropdownMenuSubTrigger,
    DropdownMenuSubContent,
    DropdownMenuSeparator,
    DropdownMenuCheckboxItem,
    DropdownMenuLabel,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuShortcut,
} from '@/components/ui/dropdown-menu'

import { MouseIcon } from "lucide-vue-next";


// Standardmenüitems, die du ggf. anhand der row-Daten anpassen kannst
const getDefaultMenuItems = (row) => [
    {
        type: 'item',
        label: `Edit ${row.original.name || ''}`,
        onClick: () => console.log('Edit clicked for row:', row)
    },
    {
        type: 'item',
        label: 'Delete',
        onClick: () => console.log('Delete clicked for row:', row)
    }
];

export function useContextMenu() {
    // Dynamische Render-Funktion für einzelne Menü-Elemente

    const renderDropdownItem = (item) => {
        switch (item.type) {
            case 'item':
                return h(
                    DropdownMenuItem,
                    {
                        inset: true,
                        disabled: item.disabled,
                        onClick: item.onClick,
                        class: item.destructive ? 'text-destructive shadow-sm hover:bg-destructive/90' : ''
                    },
                    {
                        default: () => [
                            item.label,
                            item.shortcut && h(DropdownMenuShortcut, () => item.shortcut)
                        ]
                    }
                )

            case 'submenu':
                return h(
                    DropdownMenuSub,
                    null,
                    {
                        default: () => [
                            h(DropdownMenuSubTrigger, { inset: true }, () => item.label),
                            h(
                                DropdownMenuSubContent,
                                { class: 'w-48' },
                                {
                                    default: () => item.items.map(renderDropdownItem)
                                }
                            )
                        ]
                    }
                )

            case 'separator':
                return h(DropdownMenuSeparator)

            case 'checkbox':
                return h(
                    DropdownMenuCheckboxItem,
                    {
                        checked: item.checked,
                        onClick: item.onClick
                    },
                    {
                        default: () => [
                            item.label,
                            item.shortcut && h(DropdownMenuShortcut, () => item.shortcut)
                        ]
                    }
                )

            case 'radio-group':
                return h(
                    DropdownMenuRadioGroup,
                    {
                        modelValue: item.modelValue,
                        'onUpdate:modelValue': item.onChange
                    },
                    {
                        default: () => [
                            h(DropdownMenuLabel, { inset: true }, () => item.label),
                            h(DropdownMenuSeparator),
                            ...item.items.map(subItem =>
                                h(DropdownMenuRadioItem, { value: subItem.value }, () => subItem.label)
                            )
                        ]
                    }
                )

            default:
                return null
        }
    }
    // Hauptfunktion zum Rendern des Kontextmenüs, die row und items als Parameter erhält.
    // items können individuell oder als Default anhand der row gesetzt werden.
    const renderDropdownMenu = (
        row,
        items,
        triggerClass = 'text-right font-medium flex items-center justify-center'
    ) => {
        return h(
            DropdownMenu,
            null,
            {
                default: () => [
                    // Trigger-Button
                    h(
                        DropdownMenuTrigger,
                        { class: triggerClass, asChild: true },
                        () => h(MouseIcon)
                    ),

                    // Dropdown-Inhalt
                    h(
                        DropdownMenuContent,
                        { class: 'w-64' },
                        {
                            default: () => items.map(renderDropdownItem)
                        }
                    )
                ]
            }
        )
    }
    return { renderDropdownMenu, getDefaultMenuItems };
}
