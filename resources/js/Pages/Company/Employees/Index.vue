<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Recebe a lista paginada do Controller
defineProps({ employees: Object });

// Formata data para o padrão BR
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Funcionários" />

    <AuthenticatedLayout>
        <template #header>Gestão de Vidas</template>

        <div class="space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-sesi-blue">Colaboradores Ativos</h2>
                    <p class="text-sm text-gray-500">Gerencie as admissões e funções da sua empresa.</p>
                </div>
                
                <Link :href="route('employees.create')" 
                    class="w-full md:w-auto text-center px-6 py-3 bg-sesi-blue hover:bg-blue-800 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Adicionar Novo
                </Link>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[800px]">
                        <thead class="bg-gray-100 text-gray-600 text-sm uppercase font-bold border-b border-gray-200">
                            <tr>
                                <th class="p-5">Nome / CPF</th>
                                <th class="p-5">Função (Cargo)</th>
                                <th class="p-5">Admissão</th>
                                <th class="p-5">Nascimento</th>
                                <th class="p-5 text-center">Status</th>
                                <th class="p-5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="emp in employees.data" :key="emp.id" class="hover:bg-blue-50/30 transition-colors">
                                
                                <td class="p-5">
                                    <div class="font-bold text-gray-800 text-base">{{ emp.name }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">{{ emp.cpf }}</div>
                                </td>
                                
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ emp.role?.name || 'Não informado' }}
                                    </span>
                                </td>
                                
                                <td class="p-5 text-gray-600">
                                    {{ formatDate(emp.admission_date) }}
                                </td>
                                <td class="p-5 text-gray-600">
                                    {{ formatDate(emp.birth_date) }}
                                </td>
                                
                                <td class="p-5 text-center">
                                    <span v-if="emp.is_active" class="text-green-600 font-bold text-xs uppercase flex items-center justify-center gap-1">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span> Ativo
                                    </span>
                                    <span v-else class="text-red-600 font-bold text-xs uppercase">Inativo</span>
                                </td>

                                <td class="p-5 text-right">
                                    <button class="text-gray-400 hover:text-sesi-blue transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="employees.data.length === 0">
                                <td colspan="6" class="p-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-lg font-medium">Nenhum funcionário encontrado.</p>
                                        <p class="text-sm">Clique em "Adicionar Novo" ou faça a importação da Planilha M1.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="employees.links && employees.data.length > 0" class="p-4 border-t border-gray-200 bg-gray-50 flex justify-center">
                    <div class="flex gap-1">
                        <Link v-for="(link, k) in employees.links" :key="k"
                            :href="link.url || '#'"
                            class="px-3 py-1 text-sm rounded border transition-colors"
                            :class="{
                                'bg-sesi-blue text-white border-sesi-blue': link.active,
                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-100': !link.active,
                                'opacity-50 cursor-not-allowed': !link.url
                            }"
                            v-html="link.label"
                        />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>