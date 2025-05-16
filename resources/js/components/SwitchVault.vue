<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type SharedData, type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';
import { Vault } from '@/types/vaults';
import CurrentVaultInfo from './vaults/CurrentVaultInfo.vue';
import UserVaultContent from './vaults/UserVaultContent.vue';

const page = usePage<SharedData>();
console.log(page.props);
const user = page.props.auth.user as User;
const currentVault = page.props.vaults.currentVault as Vault;
const vaults = page.props.vaults.list as Array<Vault>;
const { isMobile, state } = useSidebar();

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <Select>
                <SelectTrigger className="w-[180px]">
                    <SelectValue placeholder="Theme" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="light">Light</SelectItem>
                    <SelectItem value="dark">Dark</SelectItem>
                    <SelectItem value="system">System</SelectItem>
                </SelectContent>
            </Select>

        </SidebarMenuItem>
    </SidebarMenu>
</template>
