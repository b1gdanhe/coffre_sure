<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Role } from '@/types/vaults';
import { Head } from '@inertiajs/vue3';

import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

interface Props {
    roles: Array<Role>;
}

defineProps<Props>();
</script>

<template>
    <Head title="Coffre fort" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Table>
                <TableCaption>Liste des roles</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> # </TableHead>
                        <TableHead class="w-[100px]"> Nom </TableHead>
                        <TableHead class="text-right"> Description </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="role in roles" :key="role.id">
                        <TableCell class="font-medium">
                            {{ role.id }}
                        </TableCell>

                        <TableCell class="font-medium">
                            {{ role.name }}
                        </TableCell>
                        <TableCell class="text-right">{{ role.description }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
