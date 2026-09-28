<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Submit a Rental Application</h2>
                <p class="text-sm text-gray-500 mt-1">Fill out the form below to apply for a rental property.</p>
            </div>
            <a href="{{ route('tenant.applications') }}" class="text-sm text-gray-600 hover:text-gray-800">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.applications.store') }}" class="space-y-6 bg-white rounded-lg shadow-sm p-6">
                @csrf

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input name="full_name" type="text" required value="{{ old('full_name', auth()->user()->name) }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input name="email" type="email" required value="{{ old('email', auth()->user()->email) }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                            <input name="phone" type="text" required value="{{ old('phone') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                            <input name="current_address" type="text" value="{{ old('current_address') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input name="current_city" type="text" value="{{ old('current_city') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State / ZIP</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input name="current_state" type="text" value="{{ old('current_state') }}" placeholder="State"
                                       class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                                <input name="current_zip" type="text" value="{{ old('current_zip') }}" placeholder="ZIP"
                                       class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Employment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employer</label>
                            <input name="employer" type="text" value="{{ old('employer') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                            <input name="job_title" type="text" value="{{ old('job_title') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Income</label>
                            <input name="monthly_income" type="number" step="0.01" min="0" value="{{ old('monthly_income') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desired Move-In</label>
                            <input name="move_in_date" type="date" value="{{ old('move_in_date') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Reference</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Previous Landlord</label>
                            <input name="previous_landlord" type="text" value="{{ old('previous_landlord') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Landlord Phone</label>
                            <input name="previous_landlord_phone" type="text" value="{{ old('previous_landlord_phone') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Additional</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Number</label>
                            <input name="unit_number" type="text" value="{{ old('unit_number') }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Occupants</label>
                            <input name="occupants" type="number" min="1" value="{{ old('occupants', 1) }}"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pets</label>
                            <input name="pets" type="text" value="{{ old('pets') }}" placeholder="e.g., 1 cat"
                                   class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="block w-full rounded-lg border-gray-200 focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-2.5 px-3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-lg bg-[#3f9c3a] hover:bg-[#34852f] text-white font-bold transition">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>