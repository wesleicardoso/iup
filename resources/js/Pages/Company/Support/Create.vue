<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({ categories: Array });

const form = useForm({
    category_id: '',
    subject: '',
    priority: 'media',
    message: ''
});

const submit = () => {
    form.post(route('support.store'));
};
</script>

<template>
    <Head title="Abrir Chamado" />
    <AuthenticatedLayout>
        <template #header>Novo Chamado</template>

        <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-bold text-sesi-blue mb-6">Como podemos ajudar?</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block font-medium text-gray-700 mb-2">Qual a área responsável?</label>
                    <select v-model="form.category_id" class="w-full rounded-lg border-gray-300 focus:border-sesi-blue focus:ring-sesi-blue">
                        <option value="" disabled>Selecione uma categoria...</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-2">Assunto Resumido</label>
                    <input v-model="form.subject" type="text" class="w-full rounded-lg border-gray-300 focus:border-sesi-blue focus:ring-sesi-blue" placeholder="Ex: Erro ao agendar exame...">
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-2">Prioridade</label>
                    <select v-model="form.priority" class="w-full rounded-lg border-gray-300 focus:border-sesi-blue focus:ring-sesi-blue">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta (Urgente)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-2">Descreva seu problema</label>
                    <textarea v-model="form.message" rows="5" class="w-full rounded-lg border-gray-300 focus:border-sesi-blue focus:ring-sesi-blue" placeholder="Detalhe o que está acontecendo..."></textarea>
                </div>

                <div class="flex justify-end gap-4">
                    <Link :href="route('support.index')" class="px-6 py-3 text-gray-600 hover:text-gray-900">Cancelar</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600">
                        Enviar Chamado
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>