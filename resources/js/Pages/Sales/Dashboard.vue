<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ companies: Array });

// --- ESTADOS E CONTROLES ---
const showCreateModal = ref(false);
const formCreate = useForm({ name: '', cnpj: '', email: '' });
const showImportModal = ref(false);
const importCompanyId = ref(null);
const formImport = useForm({ file: null });
const fileInputRef = ref(null);
const expandedCompanyId = ref(null);

// --- CONFIGURAÇÃO ---
const stepsConfig = [
    { key: 'contrato_assinado', label: 'Contrato Assinado', icon: '📝' },
    { key: 'importacao_m1', label: 'Importação Planilha M1', icon: '📊' },
    { key: 'visita_tecnica', label: 'Visita Técnica de Segurança', icon: '👷' },
    { key: 'aprovacao_pgr', label: 'Aprovação PGR', icon: '✅' },
    { key: 'aprovacao_pcmso', label: 'Aprovação PCMSO', icon: '🩺' },
    { key: 'concluido', label: 'Cliente Apto (Plataforma Liberada)', icon: '🚀' },
];

// --- FUNÇÕES ---
const toggleExpand = (id) => expandedCompanyId.value = expandedCompanyId.value === id ? null : id;
const isStepCompleted = (current, check) => {
    const keys = stepsConfig.map(s => s.key);
    return keys.indexOf(current) >= keys.indexOf(check);
};
const isCurrentStep = (current, check) => current === check;
const formatVisitDate = (date) => date ? new Date(date + 'T12:00:00').toLocaleDateString('pt-BR') : '';

// --- AÇÕES ---
const createCompany = () => formCreate.post(route('sales.store'), { onSuccess: () => { showCreateModal.value = false; formCreate.reset(); }});
const advanceStep = (id) => { if(confirm('Confirmar conclusão?')) useForm({}).post(route('sales.advance', id)); };
const openImportModal = (id) => { importCompanyId.value = id; formImport.reset(); showImportModal.value = true; };
const removeFile = () => { formImport.file = null; if (fileInputRef.value) fileInputRef.value.value = ''; };
const submitImport = () => { if (!formImport.file) return; formImport.post(route('sales.import_m1', importCompanyId.value), { onSuccess: () => { showImportModal.value = false; formImport.reset(); removeFile(); }}); };
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
                <div v-for="company in companies" :key="company.id" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
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
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="expandedCompanyId === company.id ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
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
                                        <div>
                                            <h5 class="font-bold text-lg flex items-center gap-2" :class="isStepCompleted(company.onboarding_step, step.key) ? 'text-gray-800' : 'text-gray-500'">{{ step.icon }} {{ step.label }}</h5>
                                            <div class="text-sm mt-1">
                                                <span v-if="step.key === 'visita_tecnica' && company.technical_visit_at" class="text-gray-600 font-bold bg-blue-50 px-2 rounded">📅 {{ formatVisitDate(company.technical_visit_at) }}</span>
                                            </div>
                                        </div>

                                        <div class="w-full md:w-auto">
                                            <div v-if="step.key === 'importacao_m1' && isCurrentStep(company.onboarding_step, step.key) && !company.is_active">
                                                <button @click="openImportModal(company.id)" class="w-full md:w-auto justify-center px-4 py-2 bg-sesi-blue text-white text-sm font-bold rounded-lg flex items-center gap-2 hover:bg-blue-800"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg> Upload Planilha</button>
                                                <a :href="route('sales.template_m1')" class="text-xs text-center block mt-2 text-sesi-blue hover:underline">Baixar Modelo</a>
                                            </div>
                                            <button v-else-if="isCurrentStep(company.onboarding_step, step.key) && !company.is_active" @click="advanceStep(company.id)" class="w-full md:w-auto justify-center px-4 py-2 bg-sesi-blue hover:bg-blue-800 text-white text-sm font-bold rounded-lg flex items-center gap-2">Concluir Etapa</button>
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

        <div v-if="showCreateModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4">
             <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl relative">
                <h3 class="text-xl font-bold text-sesi-blue mb-4">Nova Empresa</h3>
                <input v-model="formCreate.name" type="text" class="w-full mb-3 border-gray-300 rounded-lg p-3" placeholder="Razão Social">
                <input v-model="formCreate.cnpj" type="text" class="w-full mb-3 border-gray-300 rounded-lg p-3" placeholder="CNPJ">
                <input v-model="formCreate.email" type="email" class="w-full mb-6 border-gray-300 rounded-lg p-3" placeholder="Email">
                <div class="flex justify-end gap-3"><button @click="showCreateModal=false" class="px-4 py-2 text-gray-500">Cancelar</button><button @click="createCompany" class="px-6 py-2 bg-sesi-green text-white font-bold rounded-lg">Salvar</button></div>
             </div>
        </div>
        
        <div v-if="showImportModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4">
             <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-2xl relative">
                <h3 class="text-xl font-bold text-sesi-blue mb-4">Importar M1</h3>
                <div v-if="!formImport.file" class="mb-6 border-2 border-dashed p-6 text-center rounded-lg"><input ref="fileInputRef" type="file" @input="formImport.file=$event.target.files[0]" class="block w-full text-sm text-gray-500"></div>
                <div v-else class="mb-6 bg-blue-50 p-4 rounded flex justify-between items-center"><span class="truncate max-w-[200px]">{{ formImport.file.name }}</span> <button @click="removeFile" class="text-red-500 font-bold">X</button></div>
                <div class="flex justify-end gap-3"><button @click="showImportModal=false" class="px-4 py-2 text-gray-500">Cancelar</button><button @click="submitImport" class="px-6 py-2 bg-sesi-green text-white font-bold rounded-lg">Importar</button></div>
             </div>
        </div>

    </AuthenticatedLayout>
</template>