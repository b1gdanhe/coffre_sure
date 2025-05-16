<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, User, User2, Vault, Logs, LockKeyhole } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';



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
        icon: User,
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
        href: '/identifiants',
        icon: LockKeyhole,
    },
];
const page = usePage();
const auth = computed(() => page.props.auth);
const getHomeLinkByRole = () => {
    // Vérifier si l'utilisateur a certains rôles
    if (auth.value.user.roles.some(role => role.name === 'admin')) {
        return route('admin.dashboard');
    } else {
        return route('user.dashboard');
    }
};

const isAdmin = () => {
    return auth.value.user.roles.some(role => role.name === 'admin')
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
