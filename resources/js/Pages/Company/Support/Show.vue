<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({ ticket: Object });
const user = usePage().props.auth.user;

const form = useForm({
    message: ''
});

const submit = () => {
    form.post(route('support.reply', props.ticket.id), {
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head :title="`Chamado #${ticket.id}`" />
    <AuthenticatedLayout>
        <template #header>
            Chamado #{{ ticket.id }} - {{ ticket.status }}
        </template>

        <div class="flex flex-col h-[calc(100vh-180px)]"> <div class="bg-white p-6 border-b border-gray-200 flex justify-between items-start mb-4 rounded-t-lg">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ ticket.subject }}</h1>
                    <span class="inline-block mt-2 px-3 py-1 bg-blue-50 text-sesi-blue text-sm font-bold rounded-full">
                        {{ ticket.category.name }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Aberto em: {{ new Date(ticket.created_at).toLocaleDateString() }}</p>
                    <p class="text-sm font-bold text-gray-700 capitalize">Prioridade: {{ ticket.priority }}</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-6 bg-gray-50 border border-gray-200 rounded-lg mb-4 flex flex-col-reverse">
                <div v-for="msg in ticket.messages" :key="msg.id" 
                    class="flex gap-4 max-w-3xl"
                    :class="msg.user_id === user.id ? 'ml-auto flex-row-reverse' : ''"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                         :class="msg.user_id === user.id ? 'bg-sesi-blue' : 'bg-gray-400'">
                        {{ msg.user.name.charAt(0) }}
                    </div>

                    <div class="p-4 rounded-2xl shadow-sm text-sm"
                         :class="msg.user_id === user.id ? 'bg-blue-100 text-gray-800 rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none'">
                        <p class="whitespace-pre-wrap">{{ msg.message }}</p>
                        <div class="mt-2 text-xs opacity-60 text-right">
                            {{ new Date(msg.created_at).toLocaleString() }}
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white p-4 rounded-lg border border-gray-200 flex gap-4">
                <textarea 
                    v-model="form.message" 
                    rows="2" 
                    class="flex-1 rounded-lg border-gray-300 focus:border-sesi-blue focus:ring-sesi-blue resize-none"
                    placeholder="Digite sua resposta..."
                    required
                ></textarea>
                <button type="submit" :disabled="form.processing" class="px-6 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600 self-end py-3">
                    Enviar
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>