<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
defineProps({ companies: Array });
const showDateModal = ref(false);
const form = useForm({ visit_date: '' });
const selectedCompany = ref(null);
const openSchedule = (c) => { selectedCompany.value = c; form.visit_date = c.technical_visit_at || ''; showDateModal.value = true; };
const submitSchedule = () => form.post(route('safety.schedule', selectedCompany.value.id), { onSuccess: () => showDateModal.value = false });
const approvePGR = (id) => { if(confirm('Aprovar?')) useForm({}).post(route('safety.approve', id)); };
const formatDate = (d) => d ? new Date(d).toLocaleDateString('pt-BR') : 'Pendente';
</script>

<template>
    <Head title="Segurança" />
    <AuthenticatedLayout>
        <template #header>Gestão Técnica</template>
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-sesi-blue mb-4">Empresas Pendentes</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[800px]">
                        <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                            <tr>
                                <th class="p-4">Empresa</th>
                                <th class="p-4">Etapa</th>
                                <th class="p-4">Data Visita</th>
                                <th class="p-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50">
                                <td class="p-4 font-bold">{{ company.name }}<br><span class="text-xs font-normal text-gray-500">{{ company.cnpj }}</span></td>
                                <td class="p-4"><span class="bg-blue-50 text-blue-800 px-2 py-1 rounded text-xs font-bold uppercase">{{ company.onboarding_step }}</span></td>
                                <td class="p-4 font-mono">{{ formatDate(company.technical_visit_at) }}</td>
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <button @click="openSchedule(company)" class="px-3 py-1 border border-blue-500 text-blue-500 rounded hover:bg-blue-50 text-sm font-bold">{{ company.technical_visit_at ? 'Reagendar' : 'Agendar' }}</button>
                                    <button v-if="company.technical_visit_at" @click="approvePGR(company.id)" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm font-bold">Aprovar PGR</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div v-if="showDateModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">
                <h3 class="font-bold text-lg mb-4">Data da Visita</h3>
                <input type="date" v-model="form.visit_date" class="w-full border-gray-300 rounded mb-4">
                <div class="flex justify-end gap-2">
                    <button @click="showDateModal=false" class="text-gray-500">Cancelar</button>
                    <button @click="submitSchedule" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>