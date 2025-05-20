<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import SwitchVault from './SwitchVault.vue';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();
</script>

<template>
    <header
        class=" bg-white dark:bg-neutral-900 sticky z-50 top-0 flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4 justify-between">
        <div class="flex items-center gap-2 ">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>

        </div>
        <div class="">
            <template v-if="breadcrumbs.length > 0">
                <SwitchVault :breadcrumbs="breadcrumbs" />
            </template>
        </div>
    </header>
</template>
