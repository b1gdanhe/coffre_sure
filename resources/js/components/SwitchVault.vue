<script setup lang="ts">
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useSidebar } from '@/components/ui/sidebar';
import CreateEntry from '@/pages/users/entries/Create.vue';
import type { SharedData, User, Vault } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const currentVault = page.props.vaults.currentVault as Vault;
const vaults = page.props.vaults.list as Array<Vault>;
const { isMobile, state } = useSidebar();

const form = useForm({
    selectedVaultId: currentVault.id ?? '',
});

const changeVault = () => {
    console.log(form.selectedVaultId);
    form.post(route('vaults.switch'), {
        onFinish: () => form.reset('selectedVaultId'),
    });
};
</script>

<template>
    <div class="flex gap-2">
        <CreateEntry />

        <Select v-model="form.selectedVaultId" @update:modelValue="changeVault">
            <SelectTrigger class="w-full">
                <SelectValue :placeholder="currentVault?.name || 'Choisir le coffre'" />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectItem v-for="vault in vaults" :value="vault.id" :key="vault.id">
                        {{ vault.name }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
    </div>
</template>
