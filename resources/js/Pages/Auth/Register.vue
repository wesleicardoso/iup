<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar Conta" />

        <div class="mb-10">
            <h2 class="text-3xl font-bold text-sesi-blue mb-2">Criar nova conta</h2>
            <p class="text-gray-500">Preencha os dados abaixo para iniciar.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            
            <div>
                <InputLabel for="name" value="Nome Completo / Razão Social" class="text-gray-700 font-semibold" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-sesi-blue focus:ring-sesi-blue py-3 px-4"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Ex: Indústria Metalúrgica Ltda"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-mail Corporativo" class="text-gray-700 font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-sesi-blue focus:ring-sesi-blue py-3 px-4"
                    v-model="form.email"
                    required
                    placeholder="contato@empresa.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Senha" class="text-gray-700 font-semibold" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-sesi-blue focus:ring-sesi-blue py-3 px-4"
                    v-model="form.password"
                    required
                    placeholder="Mínimo 8 caracteres"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirmar Senha" class="text-gray-700 font-semibold" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-sesi-blue focus:ring-sesi-blue py-3 px-4"
                    v-model="form.password_confirmation"
                    required
                    placeholder="Repita a senha"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-4">
                <button
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-bold text-white bg-sesi-green hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sesi-green transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Cadastrar
                </button>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Já possui cadastro?
                    <Link :href="route('login')" class="font-bold text-sesi-blue hover:underline">
                        Fazer login
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>