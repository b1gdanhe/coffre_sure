<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    code: '',
});

const submit = () => {
    form.post(route('mfa.verify'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthLayout title="Verification"
        description="Veuillez entrer le code d\'authentification à six chiffres que nous venons d\'envoyer à votre adresse email.">

        <Head title="Confirm password" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">Code</Label>
                    <Input id="code" type="code" class="mt-1 block w-full" v-model="form.code" required
                        autocomplete="current-password" autofocus />

                    <InputError :message="form.errors.code" />
                </div>

                <div class="flex items-center">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Veirfier
                    </Button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
