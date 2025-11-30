<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ appointments: Object });

// --- MODAIS E ESTADOS ---
const showScheduleModal = ref(false); // Modal para definir a data
const showUploadModal = ref(false);
const selectedApt = ref(null);

// Form Agendamento (Clínica define data)
const formSchedule = useForm({
    scheduled_at: ''
});

// Form Upload (Mantido para reuso)
const formUpload = useForm({ file: null });

// --- FUNÇÕES DE AÇÃO ---

// 1. ABRIR MODAL DE AGENDAMENTO
const openSchedule = (apt) => {
    selectedApt.value = apt;
    // Tenta carregar data se já existia (para reagendar)
    formSchedule.scheduled_at = apt.scheduled_at ? new Date(apt.scheduled_at).toISOString().slice(0, 16) : '';
    showScheduleModal.value = true;
};

// 2. ENVIAR DATA (Chama o backend setSchedule)
const submitSchedule = () => {
    formSchedule.post(route('provider.appointments.schedule', selectedApt.value.id), {
        onSuccess: () => { 
            showScheduleModal.value = false; 
            formSchedule.reset(); 
            selectedApt.value = null;
        }
    });
};

// 3. CHECK-IN (Mudar de Pendente para Aguardando Médico)
const doCheckIn = (id) => {
    if(confirm('Confirmar presença do paciente?')) {
        router.post(route('provider.appointments.checkin', id));
    }
};

// 4. UPLOAD (Enviar Laudo)
const openUpload = (apt) => { selectedApt.value = apt; showUploadModal.value = true; };
const submitUpload = () => { formUpload.post(route('provider.appointments.upload', selectedApt.value.id), { onSuccess: () => { showUploadModal.value = false; formUpload.reset(); }}); };
</script>

<template>
    <Head title="Recepção e Agenda" />
    <AuthenticatedLayout>
        <template #header>Gestão de Agenda</template>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-sesi-blue mb-4">Solicitações e Agendamentos</h2>
                <p class="text-sm text-gray-500">Gerencie solicitações e a fila de check-in.</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[900px]">
                        <thead class="bg-gray-100 text-gray-600 text-sm uppercase font-bold">
                            <tr>
                                <th class="p-4">Data / Status</th>
                                <th class="p-4">Paciente</th>
                                <th class="p-4">Empresa</th>
                                <th class="p-4">Exame</th>
                                <th class="p-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="apt in appointments.data" :key="apt.id" class="hover:bg-gray-50 transition-colors">
                                
                                <td class="p-4">
                                    <div v-if="apt.status === 'solicitado'" class="flex items-center gap-2">
                                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold uppercase border border-purple-200 animate-pulse">Solicitado</span>
                                    </div>
                                    <div v-else>
                                        <p class="font-mono text-gray-700 font-bold">{{ new Date(apt.scheduled_at).toLocaleString() }}</p>
                                        <span class="text-xs uppercase font-bold" 
                                            :class="{'text-yellow-600': apt.status === 'pendente', 'text-green-600': apt.status === 'aguardando', 'text-blue-600': apt.status === 'concluido'}">
                                            {{ apt.status }}
                                        </span>
                                    </div>
                                </td>

                                <td class="p-4 font-bold text-gray-800">{{ apt.employee_name }}<br><span class="text-xs font-normal text-gray-500">{{ apt.exam_type }}</span></td>
                                <td class="p-4 text-gray-600">{{ apt.company?.name || 'Avulso' }}</td>
                                <td class="p-4 text-gray-600">{{ apt.exam_type }}</td>

                                <td class="p-4 text-right">
                                    
                                    <button v-if="apt.status === 'solicitado'" 
                                        @click="openSchedule(apt)"
                                        class="px-4 py-2 bg-sesi-blue hover:bg-blue-800 text-white font-bold rounded shadow-sm text-sm">
                                        Definir Data
                                    </button>

                                    <button v-else-if="apt.status === 'pendente'" 
                                        @click="doCheckIn(apt.id)"
                                        class="px-4 py-2 bg-sesi-green hover:bg-green-600 text-white font-bold rounded shadow-sm text-sm">
                                        Check-in
                                    </button>

                                    <button v-else-if="['aguardando', 'concluido'].includes(apt.status)" 
                                        @click="openUpload(apt)"
                                        class="px-3 py-1 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-100">
                                        Laudo
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showScheduleModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white p-8 rounded-xl w-full max-w-sm shadow-2xl">
                <h3 class="text-xl font-bold text-sesi-blue mb-4">Agendar Exame</h3>
                <p class="text-sm text-gray-500 mb-4">Defina a data e hora para <strong>{{ selectedApt?.employee_name }}</strong>.</p>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Data e Hora</label>
                    <input type="datetime-local" v-model="formSchedule.scheduled_at" class="w-full border-gray-300 rounded-lg focus:ring-sesi-blue p-2">
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showScheduleModal = false" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button @click="submitSchedule" :disabled="formSchedule.processing" class="bg-sesi-blue text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-800 shadow-md">
                        Confirmar Agenda
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showUploadModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white p-8 rounded-xl w-96 shadow-2xl">
                <h3 class="text-lg font-bold mb-4">Upload</h3>
                <input type="file" @input="formUpload.file = $event.target.files[0]" class="block w-full text-sm mb-4">
                <div class="flex justify-end gap-3"><button @click="showUploadModal=false">Cancelar</button><button @click="submitUpload" class="bg-green-600 text-white px-4 py-2 rounded">Enviar</button></div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>