<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Applications</h2>
                <p class="text-sm text-gray-500 mt-1">Track your rental applications and their status.</p>
            </div>
            <a href="{{ route('tenant.applications.create') }}" 
               class="px-4 py-2 bg-[#3f9c3a] hover:bg-[#34852f] text-white text-sm font-medium rounded-lg transition">
                + New Application
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($applications->count() > 0)
                <div class="space-y-4">
                    @foreach ($applications as $application)
                        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            {{ $application->unit_number ? 'Unit ' . $application->unit_number : 'Rental Application' }}
                                        </h3>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">Applied on {{ $application->created_at->format('F d, Y') }}</p>
                                </div>
                                <a href="{{ route('tenant.applications.show', $application) }}" 
                                   class="text-sm font-medium text-[#3f9c3a] hover:text-[#34852f]">
                                    View →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#eaf5ea] mb-4">
                        <svg class="w-8 h-8 text-[#3f9c3a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No applications yet</h3>
                    <p class="text-sm text-gray-500 mb-6">You haven't submitted any rental applications yet.</p>
                    <a href="{{ route('tenant.applications.create') }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-[#3f9c3a] hover:bg-[#34852f] text-white text-sm font-medium rounded-lg transition">
                        + Submit Your First Application
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>