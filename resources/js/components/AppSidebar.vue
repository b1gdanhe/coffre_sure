<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import type { NavItem, SharedData, User } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, LockKeyhole, Logs, User2, User as UserIcon, Vault } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const settingItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: 'admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Coffre fort',
        href: '/coffre-fort',
        icon: Vault,
    },
    {
        title: 'Utilisateurs',
        href: '/utilisateurs',
        icon: UserIcon,
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: User2,
    },
];

const logItems: NavItem[] = [
    {
        title: 'Activités',
        href: '/activites',
        icon: Logs,
    },
];

const userItems: NavItem[] = [
    {
        title: 'Identifiants',
        href: '/user/identifiants',
        icon: LockKeyhole,
    },
];
const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const getHomeLinkByRole = () => {
    // Vérifier si l'utilisateur a certains rôles
    if (user.roles.some((role) => role.name === 'admin')) {
        return route('admin.dashboard');
    } else {
        return route('user.dashboard');
    }
};

const isAdmin = () => {
    return user.roles.some((role) => role.name === 'admin');
};

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="getHomeLinkByRole()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="settingItems" groupe-label="Paramètres" v-if="isAdmin()" />
            <NavMain :items="logItems" groupe-label="Journals" v-if="isAdmin()" />
            <NavMain :items="userItems" groupe-label="" v-if="!isAdmin()" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
