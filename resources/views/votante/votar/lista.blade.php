{{-- resources/views/votante/votar/lista.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="text-center mb-8 animate-fade-in">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-blue-900 mb-3">
                Sistema de Votación
            </h1>
            <p class="text-base sm:text-lg text-blue-700">
                Elecciones INCUBUNT 2026
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Selecciona tus candidatos preferidos para cada cargo
            </p>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-2 sm:space-x-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(selectedCandidates).length > 0 ? 'bg-green-600 text-white' : 'bg-blue-700 text-white'">
                        <span class="text-sm sm:text-base font-semibold">1</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Selección</span>
                </div>
                <div class="h-0.5 w-12 sm:w-20 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-300"
                         :class="Object.keys(selectedCandidates).length === 6 ? 'bg-green-600 text-white' : 'bg-gray-400 text-gray-700'">
                        <span class="text-sm sm:text-base font-semibold">2</span>
                    </div>
                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">Confirmación</span>
                </div>
            </div>
        </div>

        <form id="votingForm" action="#" method="POST" x-data="votingForm()">
            @csrf

            {{-- Instructions --}}
            <div class="mb-8 animate-fade-in">
                <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-4 sm:p-6 shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm sm:text-base font-bold text-blue-900 mb-2">
                                Instrucciones
                            </h3>
                            <p class="mt-1 text-sm text-blue-800">
                                Selecciona un partido político (esto elegirá automáticamente a sus candidatos para Presidencia, Vicepresidencia y Coordinador). Luego, selecciona directores para cada área funcional.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Selección de Partido --}}
            <div class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-6 flex items-center tracking-tight">
                    <span class="bg-blue-700 text-white rounded-full p-2 mr-3 shadow-lg">
                        <i class="fas fa-users"></i>
                    </span>
                    Selecciona un Partido Político
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    {{-- Partido Sinergia --}}
                    <div class="bg-white rounded-3xl shadow-lg p-6 cursor-pointer transition-all duration-500 hover:scale-105 hover:shadow-2xl relative border-4 border-blue-600"
                         :class="selectedParty === 1 ? 'ring-4 ring-blue-600 scale-105 shadow-2xl' : ''"
                         @click="selectParty(1, 1, 2, 3)">
                        
                        <div class="text-center mb-4">
                            <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-rocket text-blue-600 text-3xl"></i>
                            </div>
                            <h3 class="font-extrabold text-xl mb-1 text-blue-600">
                                Sinergia
                            </h3>
                            <p class="text-sm text-gray-600 italic">"Innovación y Liderazgo"</p>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Carlos+Mendez&background=3b82f6&color=fff" 
                                     alt="Carlos Mendez"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-blue-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-blue-600">Presidencia</p>
                                    <p class="font-semibold text-sm text-gray-900">Carlos Mendez</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Ana+Torres&background=3b82f6&color=fff" 
                                     alt="Ana Torres"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-blue-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-blue-600">Vicepresidencia</p>
                                    <p class="font-semibold text-sm text-gray-900">Ana Torres</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Luis+Puma&background=3b82f6&color=fff" 
                                     alt="Luis Puma"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-blue-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-blue-600">Coordinador</p>
                                    <p class="font-semibold text-sm text-gray-900">Luis Puma</p>
                                </div>
                            </div>
                        </div>

                        <template x-if="selectedParty === 1">
                            <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg animate-bounce">
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    {{-- Partido Progreso --}}
                    <div class="bg-white rounded-3xl shadow-lg p-6 cursor-pointer transition-all duration-500 hover:scale-105 hover:shadow-2xl relative border-4 border-green-600"
                         :class="selectedParty === 2 ? 'ring-4 ring-green-600 scale-105 shadow-2xl' : ''"
                         @click="selectParty(2, 11, 12, 13)">
                        
                        <div class="text-center mb-4">
                            <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-leaf text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="font-extrabold text-xl mb-1 text-green-600">
                                Progreso
                            </h3>
                            <p class="text-sm text-gray-600 italic">"Crecimiento y Futuro"</p>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Juan+Verde&background=16a34a&color=fff" 
                                     alt="Juan Verde"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-green-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-green-600">Presidencia</p>
                                    <p class="font-semibold text-sm text-gray-900">Juan Verde</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Maria+Flores&background=16a34a&color=fff" 
                                     alt="Maria Flores"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-green-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-green-600">Vicepresidencia</p>
                                    <p class="font-semibold text-sm text-gray-900">Maria Flores</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Pedro+Silva&background=16a34a&color=fff" 
                                     alt="Pedro Silva"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-green-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-green-600">Coordinador</p>
                                    <p class="font-semibold text-sm text-gray-900">Pedro Silva</p>
                                </div>
                            </div>
                        </div>

                        <template x-if="selectedParty === 2">
                            <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg animate-bounce">
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    {{-- Partido Unidad --}}
                    <div class="bg-white rounded-3xl shadow-lg p-6 cursor-pointer transition-all duration-500 hover:scale-105 hover:shadow-2xl relative border-4 border-purple-600"
                         :class="selectedParty === 3 ? 'ring-4 ring-purple-600 scale-105 shadow-2xl' : ''"
                         @click="selectParty(3, 21, 22, 23)">
                        
                        <div class="text-center mb-4">
                            <div class="w-20 h-20 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-hands-helping text-purple-600 text-3xl"></i>
                            </div>
                            <h3 class="font-extrabold text-xl mb-1 text-purple-600">
                                Unidad
                            </h3>
                            <p class="text-sm text-gray-600 italic">"Juntos por el Cambio"</p>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Sofia+Ramos&background=9333ea&color=fff" 
                                     alt="Sofia Ramos"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-purple-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-purple-600">Presidencia</p>
                                    <p class="font-semibold text-sm text-gray-900">Sofia Ramos</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Diego+Vargas&background=9333ea&color=fff" 
                                     alt="Diego Vargas"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-purple-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-purple-600">Vicepresidencia</p>
                                    <p class="font-semibold text-sm text-gray-900">Diego Vargas</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                <img src="https://ui-avatars.com/api/?name=Laura+Castro&background=9333ea&color=fff" 
                                     alt="Laura Castro"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-purple-600">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase text-purple-600">Coordinador</p>
                                    <p class="font-semibold text-sm text-gray-900">Laura Castro</p>
                                </div>
                            </div>
                        </div>

                        <template x-if="selectedParty === 3">
                            <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg animate-bounce">
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Selección de Directores --}}
            <div class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-6 flex items-center tracking-tight">
                    <span class="bg-purple-700 text-white rounded-full p-2 mr-3 shadow-lg">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    Selecciona Directores por Área
                </h2>

                {{-- Director de Marketing --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 rounded-full p-2 mr-2">
                            <i class="fas fa-bullhorn text-purple-700"></i>
                        </span>
                        Director de Marketing
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[4] === 31 ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate(4, 31)">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Roberto+Marketing&background=7c3aed&color=fff" 
                                     alt="Roberto Marketing"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Roberto Marketing</p>
                                    <p class="text-sm text-gray-600">Marketing Digital</p>
                                </div>
                                <template x-if="selectedCandidates[4] === 31">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[4] === 32 ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate(4, 32)">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Lucia+Brand&background=7c3aed&color=fff" 
                                     alt="Lucia Brand"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Lucia Brand</p>
                                    <p class="text-sm text-gray-600">Comunicación</p>
                                </div>
                                <template x-if="selectedCandidates[4] === 32">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Director de Finanzas --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 rounded-full p-2 mr-2">
                            <i class="fas fa-dollar-sign text-purple-700"></i>
                        </span>
                        Director de Finanzas
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[5] === 41 ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate(5, 41)">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Carmen+Finanzas&background=7c3aed&color=fff" 
                                     alt="Carmen Finanzas"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Carmen Finanzas</p>
                                    <p class="text-sm text-gray-600">Contabilidad</p>
                                </div>
                                <template x-if="selectedCandidates[5] === 41">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[5] === 42 ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate(5, 42)">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Jorge+Contador&background=7c3aed&color=fff" 
                                     alt="Jorge Contador"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Jorge Contador</p>
                                    <p class="text-sm text-gray-600">Economía</p>
                                </div>
                                <template x-if="selectedCandidates[5] === 42">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Director de RRHH --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 rounded-full p-2 mr-2">
                            <i class="fas fa-users text-purple-700"></i>
                        </span>
                        Director de Recursos Humanos
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[6] === 51 ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate(6, 51)">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Patricia+RRHH&background=7c3aed&color=fff" 
                                     alt="Patricia RRHH"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Patricia RRHH</p>
                                    <p class="text-sm text-gray-600">Psicología</p>
                                </div>
                                <template x-if="selectedCandidates[6] === 51">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-md p-4 cursor-pointer transition-all duration-300 hover:shadow-xl border-2"
                             :class="selectedCandidates[6] === 52 ? 'border-purple-600 bg-purple-50' : 'border-gray-200'"
                             @click="selectCandidate(6, 52)">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Miguel+Talento&background=7c3aed&color=fff" 
                                     alt="Miguel Talento"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-300">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Miguel Talento</p>
                                    <p class="text-sm text-gray-600">Administración</p>
                                </div>
                                <template x-if="selectedCandidates[6] === 52">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirmation Button --}}
            <div class="sticky bottom-0 bg-white border-t-4 border-blue-600 p-6 shadow-2xl rounded-t-3xl">
                <div class="max-w-4xl mx-auto">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-center sm:text-left">
                            <p class="text-sm text-gray-600">Votos seleccionados</p>
                            <p class="text-2xl font-bold text-blue-900">
                                <span x-text="Object.keys(selectedCandidates).length"></span> / 6
                            </p>
                        </div>
                        <button type="button"
                                @click="confirmVote()"
                                :disabled="Object.keys(selectedCandidates).length !== 6"
                                :class="Object.keys(selectedCandidates).length === 6 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full sm:w-auto px-8 py-4 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl text-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmar y Votar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hidden Inputs --}}
            <template x-for="(candidatoId, cargoId) in selectedCandidates">
                <input type="hidden" :name="'candidatos[' + cargoId + ']'" :value="candidatoId">
            </template>
        </form>

        {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             @click.self="showConfirmModal = false">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-gradient-to-r from-blue-700 to-blue-900 text-white p-6 rounded-t-3xl">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        Confirmar tu Voto
                    </h2>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-700 mb-6">
                        Por favor revisa tu selección antes de confirmar. Una vez emitido, <strong>no podrás cambiar tu voto</strong>.
                    </p>
                    
                    <div id="selectedCandidatesList" class="space-y-3 mb-6">
                        {{-- Populated by JavaScript --}}
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                            <p class="text-sm text-yellow-800">
                                <strong>Importante:</strong> Tu voto es secreto y no podrá ser modificado después de confirmar.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button"
                                @click="showConfirmModal = false"
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 rounded-xl font-bold transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="button"
                                @click="submitVote()"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold transition-colors duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Emitir Voto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function votingForm() {
    return {
        selectedCandidates: {},
        selectedParty: null,
        showConfirmModal: false,
        
        // Datos estáticos de candidatos
        candidatesData: {
            1: { cargo: 'Presidencia', nombre: 'Carlos Mendez', partido: 'Sinergia' },
            2: { cargo: 'Vicepresidencia', nombre: 'Ana Torres', partido: 'Sinergia' },
            3: { cargo: 'Coordinador', nombre: 'Luis Puma', partido: 'Sinergia' },
            11: { cargo: 'Presidencia', nombre: 'Juan Verde', partido: 'Progreso' },
            12: { cargo: 'Vicepresidencia', nombre: 'Maria Flores', partido: 'Progreso' },
            13: { cargo: 'Coordinador', nombre: 'Pedro Silva', partido: 'Progreso' },
            21: { cargo: 'Presidencia', nombre: 'Sofia Ramos', partido: 'Unidad' },
            22: { cargo: 'Vicepresidencia', nombre: 'Diego Vargas', partido: 'Unidad' },
            23: { cargo: 'Coordinador', nombre: 'Laura Castro', partido: 'Unidad' },
            31: { cargo: 'Director de Marketing', nombre: 'Roberto Marketing', partido: 'Independiente' },
            32: { cargo: 'Director de Marketing', nombre: 'Lucia Brand', partido: 'Independiente' },
            41: { cargo: 'Director de Finanzas', nombre: 'Carmen Finanzas', partido: 'Independiente' },
            42: { cargo: 'Director de Finanzas', nombre: 'Jorge Contador', partido: 'Independiente' },
            51: { cargo: 'Director de RRHH', nombre: 'Patricia RRHH', partido: 'Independiente' },
            52: { cargo: 'Director de RRHH', nombre: 'Miguel Talento', partido: 'Independiente' },
        },
        
        selectParty(partidoId, presidenteId, vicepresidenteId, coordinadorId) {
            this.selectedParty = partidoId;
            
            // Cargar automáticamente los candidatos del partido
            this.selectedCandidates[1] = presidenteId;
            this.selectedCandidates[2] = vicepresidenteId;
            this.selectedCandidates[3] = coordinadorId;
        },
        
        selectCandidate(cargoId, candidatoId) {
            this.selectedCandidates[cargoId] = candidatoId;
        },
        
        confirmVote() {
            if (Object.keys(this.selectedCandidates).length !== 6) {
                alert('Por favor selecciona candidatos para todos los cargos.');
                return;
            }
            this.showConfirmModal = true;
            this.updateConfirmationList();
        },
        
        updateConfirmationList() {
            const container = document.getElementById('selectedCandidatesList');
            container.innerHTML = '';
            
            for (const [cargoId, candidatoId] of Object.entries(this.selectedCandidates)) {
                const candidato = this.candidatesData[candidatoId];
                
                if (candidato) {
                    const div = document.createElement('div');
                    div.className = 'bg-blue-50 rounded-lg p-4 border-2 border-blue-200';
                    div.innerHTML = `
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">${candidato.cargo}</p>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                                ${candidato.nombre.split(' ').map(n => n[0]).join('')}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">${candidato.nombre}</p>
                                <p class="text-sm text-gray-600">${candidato.partido}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                }
            }
        },
        
        submitVote() {
            alert('¡Voto registrado exitosamente! (Modo demo - sin conexión a base de datos)');
            this.showConfirmModal = false;
            // document.getElementById('votingForm').submit();
        }
    }
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
[x-cloak] { display: none !important; }

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
@endpush
@endsection