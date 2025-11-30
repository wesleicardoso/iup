<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    appointment: Object,
    existingRecord: Object,
    history: Array
});

// Formulário
const form = useForm({
    anamnesis: props.existingRecord?.anamnesis || '',
    physical_exam: props.existingRecord?.physical_exam || '',
    conclusion: props.existingRecord?.conclusion || 'apto',
    attachments: [] // Arquivos
});

// Controle visual das abas
const activeTab = ref('clinico'); // clinico, arquivos, historico
const filePreviews = ref([]);

const handleFileUpload = (e) => {
    const files = Array.from(e.target.files);
    form.attachments = [...form.attachments, ...files];
    
    files.forEach(file => {
        filePreviews.value.push({
            name: file.name,
            size: (file.size / 1024).toFixed(0) + ' KB',
            type: file.type
        });
    });
};

const submit = () => {
    form.post(route('doctor.store', props.appointment.id));
};
</script>

<template>
    <Head title="Sala de Atendimento" />

    <div class="flex h-screen w-full bg-gray-100 font-sans overflow-hidden">
        
        <aside class="w-80 bg-white border-r border-gray-200 flex flex-col shadow-lg z-10">
            <div class="p-6 bg-sesi-blue text-white">
                <h2 class="text-xs font-bold opacity-70 uppercase tracking-widest mb-1">Paciente</h2>
                
                <div class="text-2xl font-bold truncate" :title="appointment.employee?.name || appointment.employee_name">
                    {{ appointment.employee?.name || appointment.employee_name }}
                </div>

                <div class="mt-2 text-sm text-blue-100">
                    <p>CPF: {{ appointment.employee?.cpf || 'Não cadastrado' }}</p>
                    
                    <p class="mt-1 font-bold">
                        {{ appointment.employee?.role?.name || 'Função: Geral' }}
                    </p>
                    
                    <p class="opacity-70">
                        {{ appointment.company?.name || 'Empresa não vinculada' }}
                    </p>
                </div>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto">
                <h3 class="font-bold text-gray-700 mb-4">Motivo do Exame</h3>
                <div class="bg-blue-50 p-4 rounded-lg text-sesi-blue text-sm font-semibold mb-6 border border-blue-100">
                    {{ appointment.exam_type }}
                </div>

                <h3 class="font-bold text-gray-700 mb-4">Histórico Recente</h3>
                <div class="space-y-3">
                    <div class="text-sm text-gray-400 italic">Nenhum atendimento anterior encontrado.</div>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200">
                 <Link :href="route('doctor.index')" class="block w-full text-center py-3 text-gray-500 hover:text-gray-800 font-bold transition-colors">
                    &larr; Voltar para Fila
                </Link>
            </div>
        </aside>

        <main class="flex-1 flex flex-col min-w-0 bg-gray-50">
            
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm">
                <div class="flex gap-1">
                    <button @click="activeTab = 'clinico'" 
                        :class="activeTab === 'clinico' ? 'bg-sesi-blue text-white' : 'text-gray-600 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-lg font-bold transition-all text-sm">
                        Exame Clínico
                    </button>
                    <button @click="activeTab = 'arquivos'" 
                        :class="activeTab === 'arquivos' ? 'bg-sesi-blue text-white' : 'text-gray-600 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-lg font-bold transition-all text-sm flex items-center gap-2">
                        Imagens e Laudos
                        <span v-if="form.attachments.length" class="bg-sesi-green text-white text-xs px-1.5 rounded-full">{{ form.attachments.length }}</span>
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">Médico Responsável</span>
                    <button @click="submit" :disabled="form.processing" class="px-6 py-2 bg-sesi-green hover:bg-green-600 text-white font-bold rounded-lg shadow-md transition-transform active:scale-95">
                        Finalizar Atendimento
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                
                <div v-show="activeTab === 'clinico'" class="max-w-4xl mx-auto space-y-8 animate-fade-in">
                    
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <label class="block text-sesi-blue font-bold mb-3 text-lg">Anamnese / História Clínica</label>
                        <textarea v-model="form.anamnesis" rows="6" 
                            class="w-full border-gray-300 rounded-lg focus:ring-sesi-blue focus:border-sesi-blue p-4 text-gray-700 leading-relaxed"
                            placeholder="Descreva as queixas do paciente, histórico de doenças, medicamentos em uso..."></textarea>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <label class="block text-sesi-blue font-bold mb-3 text-lg">Exame Físico</label>
                        <textarea v-model="form.physical_exam" rows="6" 
                            class="w-full border-gray-300 rounded-lg focus:ring-sesi-blue focus:border-sesi-blue p-4 text-gray-700 leading-relaxed"
                            placeholder="PA, Ausculta, inspeção visual, testes específicos..."></textarea>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm border-l-8 border-sesi-green flex items-center justify-between">
                        <div>
                            <label class="block text-gray-800 font-bold mb-1">Conclusão do Exame</label>
                            <p class="text-sm text-gray-500">Defina a aptidão do funcionário para a função.</p>
                        </div>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer p-4 border rounded-lg hover:bg-green-50" :class="form.conclusion === 'apto' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200'">
                                <input type="radio" value="apto" v-model="form.conclusion" class="text-green-600 focus:ring-green-500">
                                <span class="font-bold text-green-700">APTO</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer p-4 border rounded-lg hover:bg-yellow-50" :class="form.conclusion === 'apto_restricao' ? 'border-yellow-500 bg-yellow-50 ring-1 ring-yellow-500' : 'border-gray-200'">
                                <input type="radio" value="apto_restricao" v-model="form.conclusion" class="text-yellow-600 focus:ring-yellow-500">
                                <span class="font-bold text-yellow-700">APTO C/ RESTRIÇÃO</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer p-4 border rounded-lg hover:bg-red-50" :class="form.conclusion === 'inapto' ? 'border-red-500 bg-red-50 ring-1 ring-red-500' : 'border-gray-200'">
                                <input type="radio" value="inapto" v-model="form.conclusion" class="text-red-600 focus:ring-red-500">
                                <span class="font-bold text-red-700">INAPTO</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div v-show="activeTab === 'arquivos'" class="max-w-4xl mx-auto">
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 border-dashed border-2 text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500">Arraste imagens ou PDFs aqui, ou clique para selecionar.</p>
                            <p class="text-xs text-gray-400 mt-2">Raio-X, Audiometrias, Hemogramas...</p>
                        </div>
                        
                        <input type="file" multiple @change="handleFileUpload" class="hidden" id="fileInput" accept="image/*,application/pdf">
                        <label for="fileInput" class="px-6 py-2 bg-sesi-blue text-white rounded-lg cursor-pointer hover:bg-blue-800 font-bold">
                            Selecionar Arquivos
                        </label>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div v-for="(file, index) in filePreviews" :key="index" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                            <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-gray-500 mr-3 font-bold uppercase">
                                {{ file.name.split('.').pop() }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ file.name }}</p>
                                <p class="text-xs text-gray-500">{{ file.size }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
.animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>