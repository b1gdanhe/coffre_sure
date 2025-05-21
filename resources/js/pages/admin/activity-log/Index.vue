<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { AccessLog, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Journal',
        href: '/journals',
    },
];

interface Props {
    logs: Array<AccessLog>;
}

defineProps<Props>();
</script>

<template>
    <Head title="Coffre fort" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Table>
                <TableCaption>Liste des logs</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> Date Heure</TableHead>
                        <TableHead class="w-[100px]"> Status</TableHead>
                        <TableHead class="text-right"> Message </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="log in logs" :key="log.id">
                        <TableCell class="font-medium">
                            {{ log.created_at }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ log.status }}
                        </TableCell>

                        <TableCell class="text-right">{{ log.action }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
