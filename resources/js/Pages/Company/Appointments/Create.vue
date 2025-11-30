<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Recebe funcionários e clínicas
defineProps({ 
    providers: Array,
    employees: Array 
});

const form = useForm({
    employee_id: '',
    provider_id: '',
    exam_type: '',
});

const submit = () => {
    form.post(route('appointments.store'));
};
</script>

<template>
    <Head title="Solicitar Exame" />

    <AuthenticatedLayout>
        <template #header>Solicitar Exame</template>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                
                <h2 class="text-xl font-bold text-sesi-blue mb-6">Nova Solicitação</h2>
                
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Selecione o Colaborador</label>
                        <select v-model="form.employee_id" class="w-full rounded-lg border-gray-300 focus:ring-sesi-blue p-3">
                            <option value="" disabled>Escolha um funcionário...</option>
                            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                                {{ emp.name }} (CPF: {{ emp.cpf }}) - {{ emp.role?.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.employee_id" class="text-red-500 text-sm mt-1">{{ form.errors.employee_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Exame</label>
                        <select v-model="form.exam_type" class="w-full rounded-lg border-gray-300 focus:ring-sesi-blue p-3">
                            <option value="" disabled>Selecione...</option>
                            <option>Admissional</option>
                            <option>Demissional</option>
                            <option>Periódico</option>
                            <option>Retorno ao Trabalho</option>
                            <option>Mudança de Função</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Unidade / Clínica</label>
                        <select v-model="form.provider_id" class="w-full rounded-lg border-gray-300 focus:ring-sesi-blue p-3">
                            <option value="" disabled>Escolha a unidade...</option>
                            <option v-for="prov in providers" :key="prov.id" :value="prov.id">
                                {{ prov.name }} ({{ prov.city }})
                            </option>
                        </select>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg text-sm text-sesi-blue flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Após a solicitação, a clínica entrará em contato ou definirá a data do agendamento, que aparecerá no seu painel.</p>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <Link :href="route('appointments.index')" class="px-6 py-3 text-gray-500 font-bold hover:bg-gray-100 rounded-lg">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600 shadow-md">
                            Enviar Solicitação
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>