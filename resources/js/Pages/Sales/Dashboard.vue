<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios'; 

const props = defineProps({ companies: Array });

// --- ESTADOS DE CONTROLE ---
const showCreateModal = ref(false);
const showImportModal = ref(false);
const importCompanyId = ref(null);
const fileInputRef = ref(null);
const expandedCompanyId = ref(null);
const isFetchingCnpj = ref(false); 
const cnpjError = ref(null);

// --- FORMULÁRIOS ---
const formCreate = useForm({ 
    name: '', 
    cnpj: '', 
    email: '', 
    corporate_name: '',
    status_code: '',
    foundation_date: '',
    cnae_primary: '',
    legal_nature: '',
    zip_code: '',
    address_line_1: '',
    address_number: '',
    neighborhood: '',
    city: '',
    state: '',
});
const formImport = useForm({ file: null });

// --- CONFIGURAÇÃO DA TIMELINE ---
const stepsConfig = [
    { key: 'contrato_assinado', label: 'Contrato Assinado', icon: '📝' },
    { key: 'importacao_m1', label: 'Importação Planilha M1', icon: '📊' },
    { key: 'visita_tecnica', label: 'Visita Técnica de Segurança', icon: '👷' },
    { key: 'aprovacao_pgr', label: 'Aprovação PGR', icon: '✅' },
    { key: 'aprovacao_pcmso', label: 'Aprovação PCMSO', icon: '🩺' },
    { key: 'concluido', label: 'Cliente Apto (Plataforma Liberada)', icon: '🚀' },
];

// --- FUNÇÕES AUXILIARES ---
const toggleExpand = (id) => expandedCompanyId.value = expandedCompanyId.value === id ? null : id;
const isStepCompleted = (current, check) => {
    const keys = stepsConfig.map(s => s.key);
    return keys.indexOf(current) >= keys.indexOf(check);
};
const isCurrentStep = (current, check) => current === check;
const formatDate = (date) => date ? new Date(date + 'T12:00:00').toLocaleDateString('pt-BR') : 'Pendente';

// Consulta API de CNPJ (Função para Autofill)
const lookupCnpj = async () => {
    const cleanedCnpjForValidation = formCreate.cnpj.replace(/\D/g, '');
    if (!cleanedCnpjForValidation || cleanedCnpjForValidation.length !== 14) {
        cnpjError.value = "Por favor, digite um CNPJ válido com 14 dígitos.";
        return;
    }
    
    isFetchingCnpj.value = true;
    cnpjError.value = null;

    // ATENÇÃO: REMOVIDO O TRY/CATCH. Falhas de rede farão o app crashar.
    try {
        const response = await axios.get(route('api.cnpj.lookup', formCreate.cnpj)); 

        if (response.data.success) {
            formCreate.corporate_name = response.data.corporate_name;
            formCreate.name = response.data.trade_name;
            formCreate.foundation_date = response.data.foundation_date;
            formCreate.status_code = response.data.status_code;
            formCreate.cnae_primary = response.data.cnae_primary;
            formCreate.legal_nature = response.data.legal_nature;
            formCreate.zip_code = response.data.zip_code;
            formCreate.address_line_1 = response.data.address_line_1;
            formCreate.address_number = response.data.address_number;
            formCreate.neighborhood = response.data.neighborhood;
            formCreate.city = response.data.city;
            formCreate.state = response.data.state;

            if (response.data.status_code !== 'ATIVA') {
                 cnpjError.value = `CNPJ ${response.data.status_code}. Atenção!`;
            } else {
                cnpjError.value = null;
            }
        } else {
            // Trata erro de lógica retornado pelo Controller
            cnpjError.value = response.data.error || "Erro ao consultar CNPJ.";
        }
    } catch (e) {
        // Tratamento de falha de conexão (Network error) - essencial após a remoção do bloco try/catch
        cnpjError.value = "Falha de comunicação com o servidor. Verifique sua conexão ou token de API no backend.";
    } finally {
        isFetchingCnpj.value = false;
    }
};

// --- AÇÕES ---
const createCompany = () => formCreate.post(route('sales.store'), { onSuccess: () => { showCreateModal.value = false; formCreate.reset(); }});
const advanceStep = (id) => { if(confirm('Confirmar conclusão desta etapa e avançar?')) useForm({}).post(route('sales.advance', id)); };
const openImportModal = (id) => { importCompanyId.value = id; formImport.reset(); showImportModal.value = true; };
const removeFile = () => { formImport.file = null; if (fileInputRef.value) fileInputRef.value.value = ''; };
const submitImport = () => { 
    if (!formImport.file) return; 
    formImport.post(route('sales.import_m1', importCompanyId.value), { 
        onSuccess: () => { showImportModal.value = false; formImport.reset(); removeFile(); }, 
        onError: () => { alert('Erro na importação: Verifique as colunas do arquivo.'); }
    }); 
};
</script>

<template>
    <Head title="Gestão de Implantação" />
    <AuthenticatedLayout>
        <template #header>Pipeline de Vendas</template>

        <div class="space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-sesi-blue">Carteira de Clientes</h2>
                    <p class="text-sm text-gray-500">Gerencie o onboarding das empresas.</p>
                </div>
                <button @click="showCreateModal = true" class="w-full md:w-auto justify-center px-6 py-3 bg-sesi-green hover:bg-green-600 text-white font-bold rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Nova Implantação
                </button>
            </div>

            <div class="space-y-4">
                <div v-for="company in companies" :key="company.id" 
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
                    
                    <div @click="toggleExpand(company.id)" class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center cursor-pointer hover:bg-gray-50 transition-colors gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-sm flex-shrink-0" :class="company.is_active ? 'bg-sesi-green' : 'bg-sesi-blue'">
                                {{ company.name.charAt(0) }}
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="text-lg font-bold text-gray-800 truncate">{{ company.name }}</h3>
                                <p class="text-sm text-gray-500 font-mono">{{ company.cnpj }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between w-full md:w-auto gap-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border flex-shrink-0" :class="company.is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-blue-50 text-sesi-blue border-blue-100'">
                                {{ company.is_active ? 'Ativo' : 'Em Implantação' }}
                            </span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300 flex-shrink-0" :class="expandedCompanyId === company.id ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    <div v-if="expandedCompanyId === company.id" class="p-4 md:p-8 bg-gray-50 border-t border-gray-100 animate-fade-in">
                        <div class="max-w-4xl mx-auto">
                            <h4 class="font-bold text-gray-700 mb-8 flex items-center gap-2">
                                <span class="w-1 h-6 bg-sesi-blue rounded-full"></span> Status
                            </h4>
                            
                            <div class="relative">
                                <div class="absolute left-6 top-4 bottom-10 w-0.5 bg-gray-300 z-0"></div>

                                <div v-for="(step, index) in stepsConfig" :key="step.key" class="relative z-10 flex items-start gap-6 pb-8 group last:pb-0">
                                    
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full border-4 flex items-center justify-center transition-all bg-white" :class="[isStepCompleted(company.onboarding_step, step.key) ? 'border-sesi-green text-white bg-sesi-green' : 'border-gray-300 text-gray-400', isCurrentStep(company.onboarding_step, step.key) && !company.is_active ? 'ring-4 ring-blue-100 border-sesi-blue text-sesi-blue bg-white scale-110' : '']">
                                        <svg v-if="isStepCompleted(company.onboarding_step, step.key) && !isCurrentStep(company.onboarding_step, step.key)" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        <span v-else class="text-lg font-bold">{{ index + 1 }}</span>
                                    </div>

                                    <div class="flex-1 bg-white p-5 rounded-xl border shadow-sm flex flex-col gap-4 transition-all" :class="isCurrentStep(company.onboarding_step, step.key) && !company.is_active ? 'border-sesi-blue shadow-md' : 'border-gray-200 opacity-90'">
                                        
                                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                                            <div>
                                                <h5 class="font-bold text-lg flex items-center gap-2" :class="isStepCompleted(company.onboarding_step, step.key) ? 'text-gray-800' : 'text-gray-500'">{{ step.icon }} {{ step.label }}</h5>
                                                <div class="text-sm mt-1 text-gray-600">{{ step.label === 'Visita Técnica de Segurança' && company.technical_visit_at ? '📅 Agendada: ' + formatDate(company.technical_visit_at) : step.label === 'Visita Técnica de Segurança' ? 'Aguardando agendamento pela Segurança.' : step.desc }}</div>
                                            </div>

                                            <div class="w-full md:w-auto">
                                                
                                                <div v-if="step.key === 'importacao_m1' && isCurrentStep(company.onboarding_step, step.key) && !company.is_active">
                                                    <button @click="openImportModal(company.id)" class="w-full md:w-auto justify-center px-4 py-2 bg-sesi-blue text-white text-sm font-bold rounded-lg flex items-center gap-2 hover:bg-blue-800 transition-colors shadow-sm">Upload Planilha</button>
                                                    <a :href="route('sales.template_m1')" class="text-xs text-center block mt-2 text-sesi-blue hover:underline">Baixar Modelo</a>
                                                </div>
                                                
                                                <button v-else-if="isCurrentStep(company.onboarding_step, step.key) && !company.is_active" @click="advanceStep(company.id)" class="w-full md:w-auto justify-center px-4 py-2 bg-sesi-blue text-white text-sm font-bold rounded-lg flex items-center gap-2 hover:bg-blue-800 transition-colors shadow-sm">Concluir Etapa</button>
                                                
                                                <div v-if="isStepCompleted(company.onboarding_step, step.key) && !isCurrentStep(company.onboarding_step, step.key)" class="text-sesi-green font-bold text-sm bg-green-50 px-3 py-1 rounded-full border border-green-100 inline-block">Concluído</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl relative">
                <h3 class="text-xl font-bold text-sesi-blue mb-4">Cadastrar Nova Empresa</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block font-bold text-gray-700 text-sm mb-1">CNPJ</label>
                        <div class="flex gap-2">
                            <input v-model="formCreate.cnpj" type="text" class="flex-1 border-gray-300 rounded-lg focus:border-sesi-blue focus:ring-sesi-blue p-3" placeholder="00.000.000/0000-00">
                            <button @click.prevent="lookupCnpj" :disabled="isFetchingCnpj" class="px-4 py-2 bg-sesi-blue text-white rounded-lg hover:bg-blue-800 flex items-center gap-2 transition-colors">
                                Consultar
                            </button>
                        </div>
                        <p v-if="cnpjError" class="text-red-500 text-xs mt-1">{{ cnpjError }}</p>
                    </div>

                    <div><label class="block font-bold text-gray-700 text-sm mb-1">Nome Fantasia</label><input v-model="formCreate.name" type="text" class="w-full border-gray-300 rounded-lg focus:border-sesi-blue p-3"></div>
                    <div><label class="block font-bold text-gray-700 text-sm mb-1">E-mail Gestor</label><input v-model="formCreate.email" type="email" class="w-full border-gray-300 rounded-lg focus:border-sesi-blue p-3"></div>
                    
                    <div v-if="formCreate.corporate_name" class="pt-2 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Dados da Receita:</p>
                        <p class="text-sm text-gray-700">{{ formCreate.corporate_name }} - {{ formCreate.city }}/{{ formCreate.state }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                    <button @click="showCreateModal = false" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-lg transition-colors">Cancelar</button>
                    <button @click="createCompany" :disabled="formCreate.processing" class="px-6 py-2 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600 shadow-md transition-colors">
                        Iniciar Onboarding
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showImportModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl relative">
                <h3 class="text-xl font-bold text-sesi-blue mb-4">Importar Planilha M1</h3>
                <p class="text-sm text-gray-500 mb-6">Selecione o arquivo Excel (.xlsx) para cadastrar funcionários e cargos em lote.</p>
                
                <div v-if="!formImport.file" class="mb-6">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            <p class="text-sm text-gray-500"><span class="font-semibold text-sesi-blue">Clique para enviar</span> (XLSX, CSV)</p>
                        </div>
                        <input ref="fileInputRef" type="file" class="hidden" accept=".xlsx, .xls, .csv" @input="formImport.file = $event.target.files[0]" />
                    </label>
                </div>

                <div v-else class="mb-6 bg-blue-50 p-4 rounded-lg flex items-center justify-between border border-blue-200">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <svg class="w-5 h-5 text-sesi-blue flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span class="text-sm font-bold text-gray-800 truncate">{{ formImport.file.name }}</span>
                    </div>
                    <button @click="removeFile" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded-full transition-colors" title="Remover arquivo">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button @click="showImportModal = false" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-lg transition-colors">Cancelar</button>
                    <button @click="submitImport" :disabled="formImport.processing || !formImport.file" class="px-6 py-2 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600 shadow-md transition-colors disabled:opacity-50">
                        <span v-if="formImport.processing">Importando...</span>
                        <span v-else>Confirmar Importação</span>
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>