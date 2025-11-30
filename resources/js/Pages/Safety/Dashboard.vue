<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ companies: Array });

// --- ESTADOS DE CONTROLE ---
const showDateModal = ref(false);
const showDocumentModal = ref(false);
const selectedCompany = ref(null);
const currentStepKey = ref('');

// --- FORMULÁRIOS ---
const formSchedule = useForm({ visit_date: '' });
const formDoc = useForm({ file: null }); // Form para o upload do PDF

// --- FUNÇÕES AUXILIARES ---
const formatDate = (date) => date ? new Date(date).toLocaleDateString('pt-BR') : 'Pendente';

// --- AÇÕES DO TÉCNICO ---

// 1. Abrir Modal de Agendamento
const openSchedule = (company) => {
    selectedCompany.value = company;
    formSchedule.visit_date = company.technical_visit_at || '';
    showDateModal.value = true;
};

// 2. Salvar Data da Visita e Avançar para PGR (Ação de Agendar)
const submitSchedule = () => {
    formSchedule.post(route('safety.schedule', selectedCompany.value.id), {
        onSuccess: () => { 
            showDateModal.value = false; 
            formSchedule.reset(); 
        }
    });
};

// 3. Abrir Modal de Documento (PGR / PCMSO)
const openDocumentModal = (company, stepKey) => {
    selectedCompany.value = company;
    currentStepKey.value = stepKey;
    showDocumentModal.value = true;
    formDoc.reset();
};

// 4. Enviar Documento para Aprovação (storeDocument - Não avança o step aqui)
const submitDocument = () => {
    // Rota: safety.document.upload/{companyId}/{stepKey}
    formDoc.post(route('safety.document.upload', [selectedCompany.value.id, currentStepKey.value]), {
        onSuccess: () => {
            showDocumentModal.value = false;
            formDoc.reset();
        },
        onError: () => {
            alert('Erro ao enviar. Verifique o tamanho (máx 5MB) e formato (PDF).');
        }
    });
};
</script>

<template>
    <Head title="Segurança do Trabalho" />
    <AuthenticatedLayout>
        <template #header>Gestão Técnica (PGR/Visitas)</template>

        <div class="space-y-6">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-sesi-blue mb-4">Empresas Aguardando Ação</h2>
                <p class="text-sm text-gray-500 mb-4">Empresas nas etapas de Implantação, Visita ou Aprovação de Documentos.</p>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[900px]">
                        <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                            <tr>
                                <th class="p-4">Empresa</th>
                                <th class="p-4">Etapa Atual</th>
                                <th class="p-4">Data Visita</th>
                                <th class="p-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50">
                                
                                <td class="p-4 font-bold">
                                    {{ company.name }}<br>
                                    <span class="text-xs font-normal text-gray-500">{{ company.cnpj }}</span>
                                </td>
                                
                                <td class="p-4">
                                    <span class="bg-blue-50 text-sesi-blue px-3 py-1 rounded-full text-xs font-bold uppercase">
                                        {{ company.onboarding_step.replace('_', ' ') }}
                                    </span>
                                </td>
                                
                                <td class="p-4">
                                    <span :class="company.technical_visit_at ? 'font-bold text-gray-800' : 'text-gray-400 italic'">
                                        {{ formatDate(company.technical_visit_at) }}
                                    </span>
                                </td>
                                
                                <td class="p-4 text-right flex justify-end gap-3">
                                    
                                    <button v-if="['importacao_m1', 'visita_tecnica'].includes(company.onboarding_step)"
                                        @click="openSchedule(company)" 
                                        class="px-4 py-2 border border-sesi-blue text-sesi-blue rounded-lg hover:bg-blue-50 transition-colors text-sm font-bold">
                                        {{ company.technical_visit_at ? 'Reagendar' : 'Agendar Visita' }}
                                    </button>

                                    <button v-if="company.onboarding_step === 'aprovacao_pgr'"
                                        @click="openDocumentModal(company, 'aprovacao_pgr')"
                                        class="px-4 py-2 bg-sesi-green text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-bold shadow-sm">
                                        Upload PGR
                                    </button>
                                    
                                    <button v-if="company.onboarding_step === 'aprovacao_pcmso'"
                                        @click="openDocumentModal(company, 'aprovacao_pcmso')"
                                        class="px-4 py-2 bg-sesi-green text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-bold shadow-sm">
                                        Upload PCMSO
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="companies.length === 0">
                                <td colspan="4" class="p-8 text-center text-gray-500">Nenhuma pendência técnica no momento.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showDateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
            <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl">
                <h3 class="text-lg font-bold text-sesi-blue mb-4">Agendar Visita Técnica</h3>
                <p class="text-sm text-gray-500 mb-4">Defina a data da visita para {{ selectedCompany?.name }}.</p>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Data da Visita</label>
                    <input type="date" v-model="formSchedule.visit_date" class="w-full border-gray-300 rounded-lg focus:ring-sesi-blue p-3">
                    <p class="text-xs text-gray-500 mt-2">Esta data ficará visível para o cliente.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showDateModal = false" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button @click="submitSchedule" :disabled="formSchedule.processing" class="px-6 py-2 bg-sesi-blue text-white font-bold rounded-lg hover:bg-blue-800">
                        Salvar Data
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showDocumentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
            <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold text-sesi-blue mb-4">
                    Upload: <span class="uppercase font-extrabold">{{ currentStepKey.replace('aprovacao_', '') }}</span>
                </h3>
                <p class="text-sm text-gray-500 mb-6">Anexe o documento final para iniciar o processo de aprovação do cliente.</p>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Arquivo PDF (máx 5MB)</label>
                    <input type="file" @input="formDoc.file = $event.target.files[0]" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100 transition-colors cursor-pointer"
                        accept="application/pdf" />
                    <p class="text-xs text-red-500 mt-2">O arquivo deve ser o laudo final em PDF.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showDocumentModal = false" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button @click="submitDocument" :disabled="formDoc.processing || !formDoc.file" 
                        class="px-6 py-2 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600 shadow-md transition-colors disabled:opacity-50">
                        Enviar para Aprovação
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>