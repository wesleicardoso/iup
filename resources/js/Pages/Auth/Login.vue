<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Acessar Plataforma" />

        <div class="mb-10">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Acesse sua plataforma IUp</h2>
            <p class="text-gray-500">Insira suas credenciais para acessar o painel.</p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            
            <div>
                <InputLabel for="email" value="E-mail Corporativo" class="text-gray-700 font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-iup-blue focus:ring-iup-blue py-3 px-4"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="seu.nome@empresa.com.br"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Senha" class="text-gray-700 font-semibold" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-iup-blue focus:ring-iup-blue py-3 px-4"
                    v-model="form.password"
                    required
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-iup-blue focus:ring-iup-blue rounded" />
                    <span class="ms-2 text-sm text-gray-600">Lembrar-me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-iup-blue hover:text-iup-dark-blue-text hover:underline"
                >
                    Esqueceu a senha?
                </Link>
            </div>

            <button
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-bold text-white bg-iup-green hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-iup-green transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                Acessar Plataforma
            </button>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Não tem uma conta?
                    <Link :href="route('register')" class="font-bold text-iup-blue hover:underline">
                        Solicite a Implantação
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>