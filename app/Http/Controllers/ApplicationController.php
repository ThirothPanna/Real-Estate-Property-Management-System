<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Show the tenant's applications list.
     */
    public function index()
    {
        $applications = Application::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('applications.index', compact('applications'));
    }

    /**
     * Show the form to create a new application.
     */
    public function create()
    {
        return view('applications.create');
    }

    /**
     * Store a new application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'unit_number' => 'nullable|string|max:50',
            'current_address' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:100',
            'current_state' => 'nullable|string|max:100',
            'current_zip' => 'nullable|string|max:20',
            'employer' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'previous_landlord' => 'nullable|string|max:255',
            'previous_landlord_phone' => 'nullable|string|max:20',
            'move_in_date' => 'nullable|date',
            'occupants' => 'nullable|integer|min:1',
            'pets' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        Application::create($validated);

        return redirect()->route('tenant.applications')
            ->with('success', 'Your application has been submitted successfully!');
    }

    /**
     * Show a single application (only the owner can view).
     */
    public function show(Application $application)
    {
        // Prevent tenants from viewing others' applications
        abort_unless($application->user_id === Auth::id(), 403);

        return view('applications.show', compact('application'));
    }
}