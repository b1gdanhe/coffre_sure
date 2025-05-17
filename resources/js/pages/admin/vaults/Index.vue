<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Vault } from '@/types';
import { Head } from '@inertiajs/vue3';
import {  Vault as VaultIcon, KeyRound } from 'lucide-vue-next';


import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Coffre fort',
        href: '/coffre-fort',
    },
];

interface Props {
    vaults: Array<Vault>;
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
                        <TableHead class="w-[100px]">
                            #
                        </TableHead>
                        <TableHead class="w-[100px]">
                            Coffre
                        </TableHead>
                        <TableHead class="text-right">
                            Description
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody >
                    <TableRow v-for="vault in vaults" :key="vault.id">
                        <TableCell class="font-medium">
                            <component :is="KeyRound" />
                        </TableCell>
                        <TableCell class="font-medium">
                            {{vault.name}}
                        </TableCell>
                        <TableCell class="text-right">{{ vault.description }}</TableCell>
                       
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
