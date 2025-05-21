<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { BreadcrumbItem, Entry } from '@/types';
import { Head } from '@inertiajs/vue3';
import { EllipsisVertical, SquareAsterisk } from 'lucide-vue-next';
import { ref } from 'vue';


import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetClose
} from '@/components/ui/sheet';
import Button from '@/components/ui/button/Button.vue';

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

// State for handling the selected entry and sheet visibility
const isSheetOpen = ref(false);
const selectedEntry = ref<Entry | null>(null);

// Function to open sheet with selected entry details
const openEntryDetail = (entry: Entry) => {
    selectedEntry.value = entry;
    isSheetOpen.value = true;
};

// Function to close sheet
const closeSheet = () => {
    isSheetOpen.value = false;
};

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
                    <TableRow v-for="entry in entries" :key="entry.id" @click="openEntryDetail(entry)">
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
                                <Button @click="openEntryDetail(entry)" variant="ghost" size="icon">
                                    <component :is="EllipsisVertical" class="" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <Sheet v-model:open="isSheetOpen" @update:open="value => !value && closeSheet()">
                <SheetContent>
                    <SheetHeader>
                        <SheetTitle>Détails de l'identifiant</SheetTitle>
                        <SheetDescription>
                            Consultez ou modifiez les informations de votre identifiant.
                        </SheetDescription>
                    </SheetHeader>

                    <div v-if="selectedEntry" class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="title" class="text-right">
                                Titre
                            </Label>
                            <Input id="title" v-model="selectedEntry.title" class="col-span-3" />
                        </div>

                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="url" class="text-right">
                                URL
                            </Label>
                            <Input id="url" v-model="selectedEntry.url" class="col-span-3" />
                        </div>

                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="category" class="text-right">
                                Catégorie
                            </Label>
                            <Input id="category" v-model="selectedEntry.category" class="col-span-3" />
                        </div>

                        <!-- Add more fields as needed -->

                    </div>

                    <SheetFooter>
                        <SheetClose asChild>
                            <Button variant="outline" @click="closeSheet">
                                Annuler
                            </Button>
                        </SheetClose>
                        <Button type="submit" @click="saveEntryChanges">
                            Enregistrer
                        </Button>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

        </div>
    </AdminLayout>
</template>
