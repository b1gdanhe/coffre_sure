<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger , DialogClose} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { Entry, Tag } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { CreditCardIcon, FileIcon, GlobeIcon, KeyIcon, MoreHorizontalIcon, NotebookPen, Plus, StarIcon, UserIcon, WifiIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    entry?: Entry;
    vaultId?: string;
    tags?: Tag[];
}>();

const emit = defineEmits(['saved', 'cancelled']);

const page = usePage();
const allTags = props.tags || [];

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

const form = useForm({
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

const isEditing = !!props.entry;
const showPassword = ref(false);
const isSubmitting = ref(false);

const submitForm = () => {
    isSubmitting.value = true;
    const endpoint = isEditing ? route('entries.update', props.entry.id) : route('entries.store');
    const method = isEditing ? 'put' : 'post';
    form[method](endpoint, {
        onSuccess: () => {
            isSubmitting.value = false;
            emit('saved');
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
    // Generate a strong password (16 characters with mixed types)
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    let password = '';
    for (let i = 0; i < 16; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    form.password = password;
};
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button class="bg-green-600">
                <Plus class="h-4 w-4" />
                Ajouter
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[605px]">
            <DialogHeader>
                <DialogTitle> {{ isEditing ? 'Modifier' : 'Ajouter' }} un identifiant </DialogTitle>
                <DialogDescription> Remplissez les informations pour {{ isEditing ? 'modifier' : 'créer' }} cet identifiant. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Base Information -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="category">Catégorie</Label>
                            <Select v-model="form.category">
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
                            <div v-if="form.errors.category" class="mt-1 text-sm text-red-500">
                                {{ form.errors.category }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="title">Titre</Label>
                            <Input id="title" v-model="form.title" placeholder="Mon compte" :class="{ 'border-red-500': form.errors.title }" />
                            <div v-if="form.errors.title" class="mt-1 text-sm text-red-500">
                                {{ form.errors.title }}
                            </div>
                        </div>
                    </div>

                    <!-- Favorite Switch -->
                    <div class="flex items-center space-x-2">
                        <Switch id="favorite" v-model="form.favorite" />
                        <Label for="favorite" class="flex items-center gap-2">
                            <StarIcon class="h-4 w-4" :class="form.favorite ? 'text-yellow-500' : 'text-gray-400'" />
                            Favori
                        </Label>
                    </div>
                </div>

                <!-- Dynamic fields based on category -->
                <div v-if="form.category === 'login'" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="username">Nom d'utilisateur</Label>
                        <Input id="username" v-model="form.username" placeholder="utilisateur@exemple.fr" />
                        <div v-if="form.errors.username" class="mt-1 text-sm text-red-500">
                            {{ form.errors.username }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Mot de passe</Label>
                        <div class="flex space-x-2">
                            <div class="relative flex-grow">
                                <Input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Mot de passe" />
                                <button type="button" @click="togglePasswordVisibility" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-sm text-gray-500">{{ showPassword ? 'Cacher' : 'Voir' }}</span>
                                </button>
                            </div>
                            <Button type="button" variant="outline" @click="generatePassword"> Générer </Button>
                        </div>
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-500">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="url">URL</Label>
                        <div class="relative flex">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <GlobeIcon class="h-4 w-4 text-gray-400" />
                            </span>
                            <Input id="url" v-model="form.url" placeholder="https://exemple.fr" class="pl-10" />
                        </div>
                        <div v-if="form.errors.url" class="mt-1 text-sm text-red-500">
                            {{ form.errors.url }}
                        </div>
                    </div>
                </div>

                <!-- Other category fields would go here -->

                <!-- Notes field for all categories -->
                <div class="space-y-2">
                    <Label for="notes">Notes</Label>
                    <Textarea id="notes" v-model="form.notes" placeholder="Informations supplémentaires..." rows="4" />
                    <div v-if="form.errors.notes" class="mt-1 text-sm text-red-500">
                        {{ form.errors.notes }}
                    </div>
                </div>

                <!-- Tags Selection -->
                <div class="space-y-2" v-if="allTags.length > 0">
                    <Label>Tags</Label>
                    <div class="flex flex-wrap gap-2">
                        <div v-for="tag in allTags" :key="tag.id" class="flex items-center space-x-2">
                            <Checkbox :id="`tag-${tag.id}`" :value="tag.id" v-model="form.tags" :style="{ borderColor: tag.color }" />
                            <Label :for="`tag-${tag.id}`" class="text-sm">
                                {{ tag.name }}
                            </Label>
                        </div>
                    </div>
                </div>
            </form>
            <DialogFooter>
                <DialogClose as-child>
                    <Button type="button" variant="outline" > Annuler </Button>
                </DialogClose>
                <Button type="submit" @click="submitForm" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Enregistrement...' : isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
