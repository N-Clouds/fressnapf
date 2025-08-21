<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';

interface Props {
    tokens: any[];
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'API tokens',
        href: '/settings/api-tokens',
    },
];
const page = usePage();
const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('api-tokens.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="API token manager" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="API Token Manager" description="Create and delete API-Token´s" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Token name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Create</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Created.</p>
                        </Transition>
                    </div>
                </form>
            </div>

<!--            <DeleteUser />-->

            <div v-if="page.props.flash?.token" class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 break-all">
                <span>API token:</span>
                {{ page.props.flash.token }}
            </div>

            <ul class="flex flex-col space-y-2">
                <li v-for="token in tokens" class="w-full">
                    <div class="flex justify-between w-full">
                        Name: {{token.name}} - Last used: {{token.last_used_at ?? 'never'}}
                    </div>
                </li>
            </ul>
        </SettingsLayout>
    </AppLayout>
</template>
