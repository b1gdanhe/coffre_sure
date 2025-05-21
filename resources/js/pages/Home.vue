<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
const page = usePage();
const name = page.props.name;
</script>

<template>
    <Head title="Accueil">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="relative flex min-h-screen flex-col items-center overflow-hidden p-6 text-white lg:justify-center lg:p-8">
        <!-- Couche de fond avec image -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[url(/img/home.jpeg)] bg-cover bg-center"></div>
            <!-- Overlay avec dégradé radial -->
            <div class="absolute inset-0 bg-gradient-to-r from-green-900/80 via-green-800/70 to-green-700/80 opacity-0.5"></div>
            <div class="bg-radial-gradient absolute inset-0"></div>
        </div>

        <!-- Contenu principal (au premier plan) -->
        <header class="z-10 mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-between gap-4 ">
                <Link :href="route('home')" class="relative z-20 flex items-center text-lg font-medium">
                    <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
                    {{ name }}
                </Link>
                <div class="flex gap-2">
                    <Link
                    v-if="$page.props.auth.user"
                    :href="route('user.dashboard')"
                    class="inline-block rounded-sm border border-white/30 px-5 py-1.5 text-sm leading-normal text-white transition-all duration-300 hover:border-white/60 hover:bg-white/10"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-white transition-all duration-300 hover:border-white/30 hover:bg-white/10"
                    >
                        Se connecter
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-white bg-white/90 px-5 py-1.5 text-sm leading-normal text-green-900 transition-all duration-300 hover:bg-white hover:shadow-md"
                    >
                        S'inscrire
                    </Link>
                </template>
                </div>
               
            </nav>
        </header>

        <div class="z-10 flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] justify-center overflow-hidden lg:max-w-4xl lg:flex-row">
                <div class="text-center">
                    <h1 class="text-6xl font-extrabold sm:text-7xl md:text-8xl">
                        <span class="bg-gradient-to-r from-green-200 via-green-300 to-green-100 bg-clip-text text-transparent drop-shadow-sm">
                            Coffre Sûr
                        </span>
                    </h1>
                    <p class="mx-auto mt-4 max-w-2xl text-xl text-white/90">
                        Sécurisez vos identifiants numériques avec la solution la plus fiable du marché
                    </p>
                    <div class="mt-8 flex justify-center gap-4">
                        <button class="rounded-md bg-green-600 px-6 py-3 font-medium transition-all duration-300 hover:bg-green-700">
                            Découvrir
                        </button>
                        <button
                            class="rounded-md border border-white/50 bg-transparent px-6 py-3 transition-all duration-300 hover:border-white hover:bg-white/10"
                        >
                            En savoir plus
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- Effet de lumière radiale -->
        <div class="absolute top-1/4 left-1/4 z-0 h-1/2 w-1/2 animate-pulse rounded-full bg-green-400/20 blur-3xl"></div>
        <div class="absolute right-1/4 bottom-0 z-0 h-1/3 w-1/3 rounded-full bg-green-300/10 blur-2xl"></div>

        <div class="z-10 hidden h-14 lg:block"></div>
    </div>
</template>
<style>
/* Style pour le dégradé radial custom */
.bg-radial-gradient {
    background: radial-gradient(circle at center, rgba(34, 197, 94, 0.3) 0%, rgba(6, 78, 59, 0.6) 50%, rgba(0, 0, 0, 0.8) 100%);
}

/* Animation de pulsation légère */
@keyframes pulse {
    0% {
        opacity: 0.5;
    }

    50% {
        opacity: 0.7;
    }

    100% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 5s infinite ease-in-out;
}
</style>
