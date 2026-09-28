<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('tenant.applications') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800">
                        {{ $application->unit_number ? 'Unit ' . $application->unit_number : 'Application' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Submitted {{ $application->created_at->format('F d, Y') }}</p>
                </div>
            </div>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($application->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Full Name</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $application->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $application->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Phone</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $application->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Move-In Date</dt>
                        <dd class="text-sm text-gray-900 mt-1">
                            {{ $application->move_in_date ? $application->move_in_date->format('M d, Y') : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Occupants</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $application->occupants }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Pets</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $application->pets ?? 'None' }}</dd>
                    </div>
                </dl>
            </div>

            @if ($application->notes)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Notes</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $application->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>