<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-3 lg:px-0">
        <div class="relative hidden h-full flex-col bg-muted p-10 text-white dark:border-r lg:flex">
            <!-- Couche de fond avec image -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-[url(/img/home.jpeg)] bg-cover bg-center"></div>
                <!-- Overlay avec dégradé radial -->
                <div class="absolute inset-0 bg-gradient-to-r from-green-900/80 via-green-800/70 to-green-700/80"></div>
                <div class="absolute inset-0 bg-radial-gradient"></div>
            </div>
            <!-- <div class="absolute inset-0 bg-zinc-900" /> -->
            <Link :href="route('home')" class="relative z-20 flex items-center text-lg font-medium">
            <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
            {{ name }}
            </Link>
            <!-- <div v-if="quote" class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-sm text-neutral-300">{{ quote.author }}</footer>
                </blockquote>
            </div> -->
        </div>
        <div class="lg:p-8 lg:col-span-2">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px] ">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
