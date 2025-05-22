<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger, DialogClose } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { Entry, Tag, Vault } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { CreditCardIcon, FileIcon, GlobeIcon, KeyIcon, MoreHorizontalIcon, NotebookPen, Plus, StarIcon, UserIcon, WifiIcon, Vault as VIcon, RectangleEllipsis } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

const props = defineProps<{
    entry?: Entry;
    vaultId?: string;
    tags?: Tag[];
}>();

const emit = defineEmits(['saved', 'cancelled']);

const page = usePage();
const allTags = props.tags || [];

// État pour gérer le type d'ajout (entry ou vault)
const dialogType = ref<'entry' | 'vault'>('entry');
const isDialogOpen = ref(false);

const categoryIcons = {
    login: KeyIcon,
    card: CreditCardIcon,
    identity: UserIcon,
    secure_note: NotebookPen,
    crypto: KeyIcon,
    medical: KeyIcon,
    wifi: WifiIcon,
    document: FileIcon,
    other: MoreHorizontalIcon,
};

// Form pour les entrées
const entryForm = useForm({
    id: props.entry?.id || '',
    vault_id: props.entry?.vault_id || props.vaultId || '',
    title: props.entry?.title || '',
    username: props.entry?.username || '',
    password: props.entry?.password || '',
    url: props.entry?.url || '',
    notes: props.entry?.notes || '',
    icon: props.entry?.icon || '',
    category: props.entry?.category || 'login',
    favorite: props.entry?.favorite || false,
    tags: props.entry?.tags?.map((tag) => tag.id) || [],
});

// Form pour les coffres
const vaultForm = useForm({
    name: '',
    description: '',
    icon: '',
    is_default: false,
});

const isEditing = computed(() => !!props.entry);
const showPassword = ref(false);
const isSubmitting = ref(false);

// Fonctions pour ouvrir les dialogs
const openEntryDialog = () => {
    dialogType.value = 'entry';
    isDialogOpen.value = true;
};

const openVaultDialog = () => {
    dialogType.value = 'vault';
    isDialogOpen.value = true;
};

const submitForm = () => {
    isSubmitting.value = true;
    
    if (dialogType.value === 'entry') {
        submitEntryForm();
    } else {
        submitVaultForm();
    }
};

const submitEntryForm = () => {
    const endpoint = isEditing.value ? route('entries.update', props.entry.id) : route('entries.store');
    const method = isEditing.value ? 'put' : 'post';
    
    entryForm[method](endpoint, {
        onSuccess: () => {
            isDialogOpen.value = false;
            isSubmitting.value = false;
            emit('saved');
        },
        onError: () => {
            isSubmitting.value = false;
        },
    });
};

const submitVaultForm = () => {
    vaultForm.post(route('vaults.store'), {
        onSuccess: () => {
            isDialogOpen.value = false;
            isSubmitting.value = false;
            emit('saved');
            // Reset du form vault
            vaultForm.reset();
        },
        onError: () => {
            isSubmitting.value = false;
        },
    });
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const generatePassword = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    let password = '';
    for (let i = 0; i < 16; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    entryForm.password = password;
};

const handleCancel = () => {
    isDialogOpen.value = false;
    if (dialogType.value === 'vault') {
        vaultForm.reset();
    }
    emit('cancelled');
};

// Computed pour les titres et descriptions dynamiques
const dialogTitle = computed(() => {
    if (dialogType.value === 'vault') return 'Ajouter un coffre';
    return isEditing.value ? 'Modifier un identifiant' : 'Ajouter un identifiant';
});

const dialogDescription = computed(() => {
    if (dialogType.value === 'vault') return 'Remplissez les informations pour créer un nouveau coffre.';
    return `Remplissez les informations pour ${isEditing.value ? 'modifier' : 'créer'} cet identifiant.`;
});

const submitButtonText = computed(() => {
    if (isSubmitting.value) return 'Enregistrement...';
    if (dialogType.value === 'vault') return 'Créer le coffre';
    return isEditing.value ? 'Mettre à jour' : 'Enregistrer';
});

const currentForm = computed(() => {
    return dialogType.value === 'entry' ? entryForm : vaultForm;
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger asChild>
            <Button class="bg-green-600">
                <Plus class="h-4 w-4" />
                Ajouter
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="openVaultDialog">
                <VIcon class="mr-2 h-4 w-4" />
                Coffre
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="openEntryDialog">
                <RectangleEllipsis class="mr-2 h-4 w-4" />
                Identifiant
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>

    <Dialog v-model:open="isDialogOpen">
        <DialogContent class="sm:max-w-[605px]">
            <DialogHeader>
                <DialogTitle>{{ dialogTitle }}</DialogTitle>
                <DialogDescription>{{ dialogDescription }}</DialogDescription>
            </DialogHeader>
            
            <!-- Formulaire pour les entrées -->
            <form v-if="dialogType === 'entry'" @submit.prevent="submitForm" class="space-y-6">
                <!-- Base Information -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="category">Catégorie</Label>
                            <Select v-model="entryForm.category">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Sélectionner une catégorie" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="login">Identifiant</SelectItem>
                                        <SelectItem value="card">Carte bancaire</SelectItem>
                                        <SelectItem value="identity">Identité</SelectItem>
                                        <SelectItem value="secure_note">Note sécurisée</SelectItem>
                                        <SelectItem value="crypto">Crypto</SelectItem>
                                        <SelectItem value="medical">Médical</SelectItem>
                                        <SelectItem value="wifi">Wi-Fi</SelectItem>
                                        <SelectItem value="document">Document</SelectItem>
                                        <SelectItem value="other">Autre</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <div v-if="entryForm.errors.category" class="mt-1 text-sm text-red-500">
                                {{ entryForm.errors.category }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="title">Titre</Label>
                            <Input id="title" v-model="entryForm.title" placeholder="Mon compte"
                                :class="{ 'border-red-500': entryForm.errors.title }" />
                            <div v-if="entryForm.errors.title" class="mt-1 text-sm text-red-500">
                                {{ entryForm.errors.title }}
                            </div>
                        </div>
                    </div>

                    <!-- Favorite Switch -->
                    <div class="flex items-center space-x-2">
                        <Switch id="favorite" v-model="entryForm.favorite" />
                        <Label for="favorite" class="flex items-center gap-2">
                            <StarIcon class="h-4 w-4" :class="entryForm.favorite ? 'text-yellow-500' : 'text-gray-400'" />
                            Favori
                        </Label>
                    </div>
                </div>

                <!-- Dynamic fields based on category -->
                <div v-if="entryForm.category === 'login'" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="username">Nom d'utilisateur</Label>
                        <Input id="username" v-model="entryForm.username" placeholder="utilisateur@exemple.fr" />
                        <div v-if="entryForm.errors.username" class="mt-1 text-sm text-red-500">
                            {{ entryForm.errors.username }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Mot de passe</Label>
                        <div class="flex space-x-2">
                            <div class="relative flex-grow">
                                <Input id="password" v-model="entryForm.password" :type="showPassword ? 'text' : 'password'"
                                    placeholder="Mot de passe" />
                                <button type="button" @click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-sm text-gray-500">{{ showPassword ? 'Cacher' : 'Voir' }}</span>
                                </button>
                            </div>
                            <Button type="button" variant="outline" @click="generatePassword"> Générer </Button>
                        </div>
                        <div v-if="entryForm.errors.password" class="mt-1 text-sm text-red-500">
                            {{ entryForm.errors.password }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="url">URL</Label>
                        <div class="relative flex">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <GlobeIcon class="h-4 w-4 text-gray-400" />
                            </span>
                            <Input id="url" v-model="entryForm.url" placeholder="https://exemple.fr" class="pl-10" />
                        </div>
                        <div v-if="entryForm.errors.url" class="mt-1 text-sm text-red-500">
                            {{ entryForm.errors.url }}
                        </div>
                    </div>
                </div>

                <!-- Notes field for all categories -->
                <div class="space-y-2">
                    <Label for="notes">Notes</Label>
                    <Textarea id="notes" v-model="entryForm.notes" placeholder="Informations supplémentaires..." rows="4" />
                    <div v-if="entryForm.errors.notes" class="mt-1 text-sm text-red-500">
                        {{ entryForm.errors.notes }}
                    </div>
                </div>

                <!-- Tags Selection -->
                <div class="space-y-2" v-if="allTags.length > 0">
                    <Label>Tags</Label>
                    <div class="flex flex-wrap gap-2">
                        <div v-for="tag in allTags" :key="tag.id" class="flex items-center space-x-2">
                            <Checkbox :id="`tag-${tag.id}`" :value="tag.id" v-model="entryForm.tags"
                                :style="{ borderColor: tag.color }" />
                            <Label :for="`tag-${tag.id}`" class="text-sm">
                                {{ tag.name }}
                            </Label>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Formulaire pour les coffres -->
            <form v-else @submit.prevent="submitForm" class="space-y-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label for="vault-name">Nom du coffre</Label>
                        <Input id="vault-name" v-model="vaultForm.name" placeholder="Mon coffre personnel"
                            :class="{ 'border-red-500': vaultForm.errors.name }" />
                        <div v-if="vaultForm.errors.name" class="mt-1 text-sm text-red-500">
                            {{ vaultForm.errors.name }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="vault-description">Description (optionnelle)</Label>
                        <Textarea id="vault-description" v-model="vaultForm.description" 
                            placeholder="Description du coffre..." rows="3" />
                        <div v-if="vaultForm.errors.description" class="mt-1 text-sm text-red-500">
                            {{ vaultForm.errors.description }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="vault-icon">Icône (optionnelle)</Label>
                        <Input id="vault-icon" v-model="vaultForm.icon" placeholder="🔒" />
                        <div v-if="vaultForm.errors.icon" class="mt-1 text-sm text-red-500">
                            {{ vaultForm.errors.icon }}
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Switch id="is-default" v-model="vaultForm.is_default" />
                        <Label for="is-default">Définir comme coffre par défaut</Label>
                    </div>
                </div>
            </form>

            <DialogFooter>
                <Button type="button" variant="outline" @click="handleCancel"> Annuler </Button>
                <Button type="submit" @click="submitForm" :disabled="isSubmitting">
                    {{ submitButtonText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>