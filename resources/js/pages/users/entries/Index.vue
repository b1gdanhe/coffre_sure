<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { BreadcrumbItem, Entry } from '@/types';
import { Head } from '@inertiajs/vue3';
import { EllipsisVertical, SquareAsterisk } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Identifiants',
        href: '/user/identifiants',
    },
];

interface Props {
    entries: Array<Entry>;
}

defineProps<Props>();
</script>

<template>
    <Head title="Coffre fort" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Table>
                <TableCaption>Liste de mes coffres</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> # </TableHead>
                        <TableHead class=""> Identifiant </TableHead>
                        <TableHead class="w-[200px]"> Dernière utilisation </TableHead>
                        <TableHead class="w-[10px]"> Categorie </TableHead>
                        <TableHead class="text-right"> Action </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="entry in entries" :key="entry.id">
                        <TableCell class="font-medium">
                            <component :is="SquareAsterisk" class="h-6 w-6" />
                        </TableCell>
                        <TableCell class="font-medium">
                            <div class="flex flex-col">
                                <div>{{ entry.title }}</div>
                                <div class="text-neutral-500">{{ entry.url }}</div>
                            </div>
                        </TableCell>
                        <TableCell class="font-medium">{{ entry.last_used }}</TableCell>
                        <TableCell class="font-medium">
                            <Badge variant="outline">{{ entry.category.toUpperCase() }}</Badge>
                        </TableCell>
                        <TableCell class=" font-medium">
                            <div class="flex items-center justify-end">
                                <component :is="EllipsisVertical" class="" />
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
