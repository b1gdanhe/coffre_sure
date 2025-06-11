<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { BreadcrumbItem, Entry } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    Copy,
    Edit,
    EllipsisVertical,
    ExternalLink,
    Eye,
    EyeOff,
    Globe,
    Key,
    LockKeyhole,
    Share2,
    Star,
    StickyNote,
    Tag as TagIcon,
    Trash2,
    User,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Identifiants',
        href: '/user/identifiants',
    },
];

interface Props {
    entries: Array<Entry>;
}

const props = defineProps<Props>();

// State management
const isSheetOpen = ref(false);
const selectedEntry = ref<Entry | null>(null);
const isPasswordVisible = ref(false);
const isEditMode = ref(false);

// Computed properties
const maskedPassword = computed(() => {
    if (!selectedEntry.value?.password) return '';
    return isPasswordVisible.value ? selectedEntry.value.password : '•'.repeat(selectedEntry.value.password.length);
});

const hasUrl = computed(() => {
    return selectedEntry.value?.url && selectedEntry.value.url.trim() !== '';
});

// Entry management functions
const openEntryDetail = (entry: Entry) => {
    selectedEntry.value = { ...entry }; // Clone to avoid direct mutation
    isSheetOpen.value = true;
    isEditMode.value = false;
    isPasswordVisible.value = false;
};

const closeSheet = () => {
    isSheetOpen.value = false;
    isEditMode.value = false;
    isPasswordVisible.value = false;
    selectedEntry.value = null;
};

const toggleEditMode = () => {
    isEditMode.value = !isEditMode.value;
};

const togglePasswordVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
};

// Action functions
const copyToClipboard = async (text: string, type: string) => {
    try {
        await navigator.clipboard.writeText(text);
        // You would typically show a toast notification here
        console.log(`${type} copié dans le presse-papier`);
    } catch (err) {
        console.error('Erreur lors de la copie:', err);
    }
};

const openUrl = () => {
    if (selectedEntry.value?.url) {
        let url = selectedEntry.value.url;
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            url = 'https://' + url;
        }
        window.open(url, '_blank');
    }
};

const toggleFavorite = async () => {
    if (selectedEntry.value) {
        selectedEntry.value.favorite = !selectedEntry.value.favorite;
        // Here you would make an API call to update the favorite status
        console.log('Favori mis à jour');
    }
};

const shareEntry = () => {
    // Implement sharing logic
    console.log("Partage de l'identifiant");
};

const deleteEntry = () => {
    // Implement delete logic with confirmation
    console.log("Suppression de l'identifiant");
};

const saveEntryChanges = async () => {
    if (selectedEntry.value) {
        // Here you would make an API call to save changes
        console.log('Sauvegarde des modifications');
        isEditMode.value = false;
    }
};

// Quick actions for table rows
const quickActions = {
    copyUsername: (entry: Entry) => copyToClipboard(entry.username || '', "Nom d'utilisateur"),
    copyPassword: (entry: Entry) => copyToClipboard(entry.password || '', 'Mot de passe'),
    openUrl: (entry: Entry) => {
        if (entry.url) {
            let url = entry.url;
            if (!url.startsWith('http://') && !url.startsWith('https://')) {
                url = 'https://' + url;
            }

            // Préremplir les champs si possible
            if (entry.username || entry.password) {
                // Vérifiez si l'URL contient déjà des paramètres de requête
                const separator = url.includes('?') ? '&' : '?';

                // Ajout des paramètres de requête pour username et password
                // Attention : ceci suppose que le site Web cible prend en charge ces paramètres
                const params = [];
                if (entry.username) params.push(`username=${encodeURIComponent(entry.username)}`);
                if (entry.password) params.push(`password=${encodeURIComponent(entry.password)}`);

                url = `${url}${separator}${params.join('&')}`;
            }

            // Correction d'une faute de frappe ici : 'urtl' -> 'url'
            window.open(url, '_blank');
        }
    },
    openAndFill: (entry: Entry) => {
        if (entry.url) {
            let url = entry.url;
            if (!url.startsWith('http://') && !url.startsWith('https://')) {
                url = 'https://' + url;
            }

            // Ouvrir l'URL
            const newWindow = window.open(url, '_blank');

            // Attendre que la page se charge puis essayer de pré-remplir
            if (newWindow) {
                setTimeout(() => {
                    try {
                        // Tentative de pré-remplissage (limité par les politiques CORS)
                        const doc = newWindow.document;

                        // Chercher les champs de connexion courants
                        const usernameFields = doc.querySelectorAll('input[type="text"], input[type="email"], input[name*="user"], input[name*="login"], input[name*="email"]');
                        const passwordFields = doc.querySelectorAll('input[type="password"]');

                        if (usernameFields.length > 0 && entry.username) {
                            (usernameFields[0] as HTMLInputElement).value = entry.username;
                        }

                        if (passwordFields.length > 0 && entry.password) {
                            (passwordFields[0] as HTMLInputElement).value = entry.password;
                        }
                    } catch (error) {
                        console.warn('Impossible de pré-remplir les champs à cause des restrictions CORS:', error);
                    }
                }, 2000);
            }
        }
    },
    generateBookmarklet: (entry: Entry) => {
        const bookmarkletCode = `
            javascript:(function(){
                var username = '${entry.username?.replace(/'/g, "\\'")}';
                var password = '${entry.password?.replace(/'/g, "\\'")}';

                // Chercher et remplir les champs
                var usernameField = document.querySelector('input[type="text"], input[type="email"], input[name*="user"], input[name*="login"], input[name*="email"], input[id*="user"], input[id*="login"]');
                var passwordField = document.querySelector('input[type="password"]');

                if (usernameField && username) {
                    usernameField.value = username;
                    usernameField.dispatchEvent(new Event('input', { bubbles: true }));
                }

                if (passwordField && password) {
                    passwordField.value = password;
                    passwordField.dispatchEvent(new Event('input', { bubbles: true }));
                }

                alert('Champs pré-remplis !');
            })();
        `;

        // Copier le bookmarklet dans le presse-papier
        copyToClipboard(bookmarkletCode, 'Bookmarklet copié - Ajoutez-le à vos favoris');
    },

    openWithInstructions: (entry: Entry) => {
        if (entry.url) {
            let url = entry.url;
            if (!url.startsWith('http://') && !url.startsWith('https://')) {
                url = 'https://' + url;
            }

            // Ouvrir l'URL
            window.open(url, '_blank');

            // Copier les identifiants pour faciliter le collage manuel
            const credentials = `Nom d'utilisateur: ${entry.username}\nMot de passe: ${entry.password}`;
            copyToClipboard(credentials, 'Identifiants copiés dans le presse-papier');
        }
    },
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
                        <TableHead class="w-[10px]"> Catégorie </TableHead>
                        <TableHead class="text-right"> Actions </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="entry in entries" :key="entry.id">
                        <TableCell class="font-medium">
                            <div class="flex items-center gap-2">
                                <LockKeyhole class="h-6 w-6" />
                                <Star v-if="entry.favorite" class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                            </div>
                        </TableCell>
                        <TableCell class="font-medium">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span>{{ entry.title }}</span>
                                </div>
                                <div class="text-sm text-neutral-500">{{ entry.url }}</div>
                                <div class="text-xs text-neutral-400">{{ entry.username }}</div>
                            </div>
                        </TableCell>
                        <TableCell class="font-medium">{{ entry.last_used || 'Jamais' }}</TableCell>
                        <TableCell class="font-medium">
                            <Badge variant="outline">{{ entry.category.toUpperCase() }}</Badge>
                        </TableCell>
                        <TableCell class="font-medium">
                            <div class="flex items-center justify-end gap-1">
                                <!-- Quick action buttons -->
                                <Button
                                    v-if="entry.username"
                                    variant="ghost"
                                    size="icon"
                                    @click="quickActions.copyUsername(entry)"
                                    title="Copier l'identifiant"
                                >
                                    <User class="h-4 w-4" />
                                </Button>

                                <Button
                                    v-if="entry.password"
                                    variant="ghost"
                                    size="icon"
                                    @click="quickActions.copyPassword(entry)"
                                    title="Copier le mot de passe"
                                >
                                    <Key class="h-4 w-4" />
                                </Button>

                                <Button v-if="entry.url" variant="ghost" size="icon" @click="quickActions.openUrl(entry)" title="Ouvrir l'URL">
                                    <ExternalLink class="h-4 w-4" />
                                </Button>

                                <!-- Main actions dropdown -->
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="icon">
                                            <EllipsisVertical class="h-5 w-5" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <SheetTrigger>
                                            <DropdownMenuItem @click="openEntryDetail(entry)">
                                                <Eye class="mr-2 h-4 w-4" />
                                                Voir les détails
                                            </DropdownMenuItem>
                                        </SheetTrigger>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem @click="shareEntry">
                                            <Share2 class="mr-2 h-4 w-4" />
                                            Partager
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Entry Detail Sheet -->
            <Sheet v-model:open="isSheetOpen" @update:open="(value) => !value && closeSheet()">
                <SheetContent class="w-[600px] overflow-y-auto p-6 sm:max-w-[600px]">
                    <SheetHeader class="px-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <SheetTitle class="flex items-center gap-2">
                                    <LockKeyhole class="h-5 w-5" />
                                    {{ selectedEntry?.title }}
                                </SheetTitle>
                                <SheetDescription>
                                    {{ isEditMode ? 'Modifier les informations' : "Détails de l'identifiant" }}
                                </SheetDescription>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button variant="ghost" size="icon" @click="toggleFavorite" :class="selectedEntry?.favorite ? 'text-yellow-500' : ''">
                                    <Star :class="selectedEntry?.favorite ? 'fill-current' : ''" class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="toggleEditMode">
                                    <Edit class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </SheetHeader>

                    <div v-if="selectedEntry" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Informations générales</h3>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="title" class="text-right font-medium"> Titre </Label>
                                <Input id="title" v-model="selectedEntry.title" class="col-span-3" :readonly="!isEditMode" />
                            </div>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="url" class="text-right font-medium">
                                    <Globe class="mr-1 inline h-4 w-4" />
                                    URL
                                </Label>
                                <div class="col-span-3 flex gap-2">
                                    <Input
                                        id="url"
                                        v-model="selectedEntry.url"
                                        class="flex-1"
                                        :readonly="!isEditMode"
                                        placeholder="https://example.com"
                                    />
                                    <Button v-if="hasUrl" variant="outline" size="icon" @click="openUrl" title="Ouvrir l'URL">
                                        <ExternalLink class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- Credentials -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Identifiants</h3>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="username" class="text-right font-medium">
                                    <User class="mr-1 inline h-4 w-4" />
                                    Utilisateur
                                </Label>
                                <div class="col-span-3 flex gap-2">
                                    <Input id="username" v-model="selectedEntry.username" class="flex-1" :readonly="!isEditMode" />
                                    <Button
                                        v-if="selectedEntry.username"
                                        variant="outline"
                                        size="icon"
                                        @click="copyToClipboard(selectedEntry.username, 'Nom d\'utilisateur')"
                                        title="Copier l'identifiant"
                                    >
                                        <Copy class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="password" class="text-right font-medium">
                                    <Key class="mr-1 inline h-4 w-4" />
                                    Mot de passe
                                </Label>
                                <div class="col-span-3 flex gap-2">
                                    <Input
                                        id="password"
                                        :value="isEditMode ? selectedEntry.password : maskedPassword"
                                        @input="isEditMode && (selectedEntry.password = $event.target.value)"
                                        :type="isEditMode ? 'text' : 'password'"
                                        class="flex-1"
                                        :readonly="!isEditMode"
                                    />
                                    <Button variant="outline" size="icon" @click="togglePasswordVisibility" title="Afficher/Masquer le mot de passe">
                                        <Eye v-if="!isPasswordVisible" class="h-4 w-4" />
                                        <EyeOff v-else class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        v-if="selectedEntry.password"
                                        variant="outline"
                                        size="icon"
                                        @click="copyToClipboard(selectedEntry.password, 'Mot de passe')"
                                        title="Copier le mot de passe"
                                    >
                                        <Copy class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- Additional Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Informations supplémentaires</h3>

                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="category" class="text-right font-medium">
                                    <TagIcon class="mr-1 inline h-4 w-4" />
                                    Catégorie
                                </Label>
                                <div class="col-span-3">
                                    <Badge variant="outline">{{ selectedEntry.category.toUpperCase() }}</Badge>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-start gap-4">
                                <Label for="notes" class="pt-2 text-right font-medium">
                                    <StickyNote class="mr-1 inline h-4 w-4" />
                                    Notes
                                </Label>
                                <Textarea
                                    id="notes"
                                    v-model="selectedEntry.notes"
                                    class="col-span-3"
                                    :readonly="!isEditMode"
                                    placeholder="Ajouter des notes..."
                                    rows="3"
                                />
                            </div>
                        </div>

                        <Separator />

                        <!-- Metadata -->
                        <div class="space-y-2 text-sm text-neutral-500">
                            <div class="flex justify-between">
                                <span>Dernière utilisation:</span>
                                <span>{{ selectedEntry.last_used || 'Jamais' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Créé le:</span>
                                <span>{{ new Date(selectedEntry.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Modifié le:</span>
                                <span>{{ new Date(selectedEntry.updated_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>

                    <SheetFooter class="flex gap-2">
                        <div class="flex flex-1 gap-2">
                            <Button variant="outline" @click="shareEntry" class="flex-1">
                                <Share2 class="mr-2 h-4 w-4" />
                                Partager
                            </Button>
                            <Button variant="destructive" @click="deleteEntry">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Supprimer
                            </Button>
                        </div>

                        <div class="flex gap-2">
                            <SheetClose asChild>
                                <Button variant="outline" @click="closeSheet"> Fermer </Button>
                            </SheetClose>
                            <Button v-if="isEditMode" @click="saveEntryChanges" class="bg-blue-600 hover:bg-blue-700"> Enregistrer </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>
        </div>
    </AdminLayout>
</template>
