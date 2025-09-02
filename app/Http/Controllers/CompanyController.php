<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::query();
        
        // Handle individual field searches
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }
        
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->get('search_email') . '%');
        }
        
        if ($request->filled('search_phone')) {
            $query->where('phone', 'like', '%' . $request->get('search_phone') . '%');
        }
        
        if ($request->filled('search_piva')) {
            $query->where('piva', 'like', '%' . $request->get('search_piva') . '%');
        }
        
        // Handle status search
        if ($request->filled('search_status')) {
            $query->where('active', (bool) $request->get('search_status'));
        }
        
        // Handle date range search
        if ($request->filled('search_date_from')) {
            $query->whereDate('created_at', '>=', $request->get('search_date_from'));
        }
        
        if ($request->filled('search_date_to')) {
            $query->whereDate('created_at', '<=', $request->get('search_date_to'));
        }
        
        // Handle per page
        $perPage = $request->get('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100])) {
            $perPage = 10;
        }
        
        // Handle sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (!in_array($sortField, ['name', 'email', 'phone', 'piva', 'active', 'created_at'])) {
            $sortField = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $companies = $query->orderBy($sortField, $sortDirection)
                          ->paginate($perPage)
                          ->withQueryString();
        
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:companies',
                'phone' => 'nullable|string|max:20',
                'piva' => 'nullable|string|max:50',
                'website' => 'nullable|url|max:255',
                'address' => 'nullable|string|max:500',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'active' => 'sometimes|boolean'
            ]);

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('company-logos', 'public');
                $validated['logo'] = $logoPath;
            }

            $validated['active'] = (bool) $request->input('active', 0);

            $company = Company::create($validated);

            return redirect()->route('companies.index')
                ->with('success', "Company '{$company->name}' has been created successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create company: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:companies,email,' . $company->id,
                'phone' => 'nullable|string|max:20',
                'piva' => 'nullable|string|max:50',
                'website' => 'nullable|url|max:255',
                'address' => 'nullable|string|max:500',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'active' => 'sometimes|boolean'
            ]);

            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                    Storage::disk('public')->delete($company->logo);
                }
                
                $logoPath = $request->file('logo')->store('company-logos', 'public');
                $validated['logo'] = $logoPath;
            }

            $validated['active'] = (bool) $request->input('active', 0);

            $company->update($validated);

            return redirect()->route('companies.index')
                ->with('success', "Company '{$company->name}' has been updated successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update company: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            $companyName = $company->name;
            
            // Delete logo file
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $company->delete();

            return redirect()->route('companies.index')
                ->with('success', "Company '{$companyName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }
}
