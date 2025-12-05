@extends('layouts.sidebar')

@section('content')
    <div class="py-6 w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Receptionist') }}
            </h2>
            <a href="{{ route('resepsionis.index') }}">
                <x-secondary-button>
                    {{ __('Kembali') }}
                </x-secondary-button>
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                
                <form action="{{ route('resepsionis.update', $resepsionis->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT') 

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $resepsionis->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $resepsionis->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="col-span-1 md:col-span-2 border-t border-gray-100 pt-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-4">Ganti Password (Opsional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengubah" />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4 border-t border-gray-100 pt-4">
                        <a href="{{ route('resepsionis.index') }}">
                            <x-secondary-button type="button">
                                {{ __('Batal') }}
                            </x-secondary-button>
                        </a>

                        <x-primary-button>
                            {{ __('Update Data') }}
                        </x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection