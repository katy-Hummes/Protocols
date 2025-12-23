<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    audit: Object,
});

const translateType = (type) => {
    switch (type) {
        case 'T':
            return 'Administrador da TI';
        case 'S':
            return 'Administrador do sistema';
        case 'A':
            return 'Atendente';
        default:
            return 'Desconhecido';
    }
};
const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);

const formatDate = (dateString) => {
    const options = {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit', hour12: false
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Visualizar Auditoria
            </h2>
        </template>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <v-card class="overflow-hidden shadow-xl sm:rounded-lg p-4">
                <v-container class="font-bold text-lg mb-4 text-center">Auditoria: {{ audit.id }}</v-container>
                <v-container class="grid grid-cols-3">
                    <v-container>
                        <span>Usuário: </span>
                        <span>{{ audit.user.name }}</span>
                    </v-container>
                    <v-container class="m-2">
                        <span>Evento: </span>
                        <span>{{ audit.event }}</span>
                    </v-container>
                    <v-container>
                        <span>Tipo de Auditoria: </span>
                        <span>{{ audit.auditable_type.split('\\').pop() }}</span>
                    </v-container>
                    <v-container>
                        <span>Data e hora: </span>
                        <span>{{ formatDate(audit.created_at) }}</span>
                    </v-container>
                    <v-container>
                        <span>Tabela: </span>
                        <span>{{ audit.auditable_type }}</span>
                    </v-container>
                    <v-container>
                        <span>ID Auditado: </span>
                        <span>{{ audit.auditable_id }}</span>
                    </v-container>
                    <v-container>
                        <span>URL: </span>
                        <span>{{ audit.url }}</span>
                    </v-container>
                    <v-container class="m-2">
                        <span>IP: </span>
                        <span>{{ audit.ip_address }}</span>
                    </v-container>
                </v-container>
                <v-container class="grid md:grid-cols-2 gap-4">
                    <v-card>
                        <v-container>
                            <div v-if="audit.old_values">
                                <h2 class="font-bold">Valores Antigos:</h2>
                                <div v-for="(value, key) in audit.old_values" :key="key">
                                    <div>
                                        <span>{{ capitalize(key) + ': ' }}</span>
                                        <span>{{ value }}</span>
                                    </div>
                                </div>
                            </div>
                        </v-container>
                    </v-card>
                    <v-card>
                        <v-container>
                            <div v-if="audit.new_values && Object.keys(audit.new_values).length">
                                <h2 class="font-bold">Novos Valores:</h2>
                                <div v-for="(value, key) in audit.new_values" :key="key">
                                    <div>
                                        <span>{{ capitalize(key) + ': ' }}</span>
                                        <span>{{ value }}</span>
                                    </div>
                                </div>
                            </div>
                        </v-container>
                    </v-card>
                </v-container>
                <v-container>
                    <Link :href="route('audit.index')"
                        class="text-base  font-semibold border-2 border-gray-600 rounded-3xl mx-4 px-4 py-1 hover:bg-purple-800 hover:text-white">
                    Voltar</Link>
                </v-container>
            </v-card>
        </div>
    </AppLayout>
</template>