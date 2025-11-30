<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Recebe a lista apenas para autocompletar (sugestão)
defineProps({
    roles: Array 
});

const form = useForm({
    name: '',
    cpf: '',
    role_name: '', // Mudou: Agora enviamos o TEXTO, não o ID
    birth_date: '',
    admission_date: '',
});

// Máscara de CPF mantida
const mascaraCPF = (event) => {
    let value = event.target.value.replace(/\D/g, "");
    if (value.length > 11) value = value.slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    form.cpf = value;
};

const submit = () => {
    form.post(route('employees.store'));
};
</script>

<template>
    <Head title="Novo Colaborador" />

    <AuthenticatedLayout>
        <template #header>Gestão de Vidas</template>

        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 md:p-12 relative overflow-hidden">
                
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Cadastrar Colaborador</h2>
                    <p class="text-gray-500 mb-8">Preencha os dados abaixo.</p>

                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                            <input v-model="form.name" type="text" class="w-full rounded-xl border-gray-200 bg-gray-50/50 p-3" required>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                            <input :value="form.cpf" @input="mascaraCPF" type="text" class="w-full rounded-xl border-gray-200 bg-gray-50/50 p-3" required>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Função / Cargo
                            </label>
                            
                            <input 
                                v-model="form.role_name"
                                list="roles-list" 
                                type="text"
                                class="w-full rounded-xl border-gray-200 focus:border-cyan-500 focus:ring-cyan-500 bg-gray-50/50 p-3 transition-all" 
                                placeholder="Digite a função (Ex: Soldador, Dev, Gerente...)"
                                required
                            >

                            <datalist id="roles-list">
                                <option 
                                    v-for="role in roles" 
                                    :key="role.id" 
                                    :value="role.name"
                                >
                                    CBO: {{ role.cbo || 'Sem CBO' }}
                                </option>
                            </datalist>
                            
                            <p class="text-xs text-gray-400 mt-1">
                                Dica: Se a função não existir na lista, ela será criada automaticamente.
                            </p>
                            <div v-if="form.errors.role_name" class="text-red-500 text-sm mt-1">{{ form.errors.role_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                            <input v-model="form.birth_date" type="date" class="w-full rounded-xl border-gray-200 bg-gray-50/50 p-3" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data de Admissão</label>
                            <input v-model="form.admission_date" type="date" class="w-full rounded-xl border-gray-200 bg-gray-50/50 p-3" required>
                        </div>

                        <div class="col-span-2 flex justify-end gap-6 mt-6">
                            <Link :href="route('employees.index')" class="text-gray-500 hover:text-gray-700 py-3">Cancelar</Link>
                            <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold rounded-xl shadow-lg hover:-translate-y-1 transition-all">
                                Salvar Cadastro
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>