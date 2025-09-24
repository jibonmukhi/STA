<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the certificates.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Certificate::with(['user', 'company']);

        // Role-based filtering
        if ($user->hasRole('end_user')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('company_manager')) {
            $companyIds = $user->companies()->pluck('companies.id');
            $query->where(function ($q) use ($user, $companyIds) {
                $q->where('user_id', $user->id)
                  ->orWhereIn('company_id', $companyIds);
            });
        }
        // STA managers can see all certificates

        // Search filters
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_subject')) {
            $query->where('subject', 'like', '%' . $request->search_subject . '%');
        }

        if ($request->filled('search_organization')) {
            $query->where('training_organization', 'like', '%' . $request->search_organization . '%');
        }

        if ($request->filled('search_type')) {
            $query->where('certificate_type', $request->search_type);
        }

        if ($request->filled('search_status')) {
            if ($request->search_status === 'expired') {
                $query->where(function ($q) {
                    $q->where('status', 'expired')
                      ->orWhere('expiration_date', '<', now());
                });
            } elseif ($request->search_status === 'expiring_soon') {
                $query->expiringSoon(30);
            } else {
                $query->where('status', $request->search_status);
            }
        }

        if ($request->filled('search_company')) {
            $query->where('company_id', $request->search_company);
        }

        if ($request->filled('search_expiration_from')) {
            $query->where('expiration_date', '>=', $request->search_expiration_from);
        }

        if ($request->filled('search_expiration_to')) {
            $query->where('expiration_date', '<=', $request->search_expiration_to);
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'subject', 'expiration_date', 'issue_date', 'training_organization', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        $certificates = $query->paginate($request->get('per_page', 10))
                             ->withQueryString();

        // Get filter options
        $companies = Company::active()->orderBy('name')->get();
        $certificateTypes = Certificate::getCertificateTypes();
        $certificateStatuses = array_merge(Certificate::getCertificateStatuses(), [
            'expiring_soon' => __('certificates.status_expiring_soon'),
        ]);

        // Statistics
        $stats = [
            'total' => $query->count(),
            'active' => Certificate::active()->count(),
            'expired' => Certificate::expired()->count(),
            'expiring_soon' => Certificate::expiringSoon(30)->count(),
        ];

        return view('certificates.index', compact(
            'certificates',
            'companies',
            'certificateTypes',
            'certificateStatuses',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create()
    {
        $companies = Company::active()->orderBy('name')->get();
        $users = User::active()->orderBy('name')->get();
        $certificateTypes = Certificate::getCertificateTypes();
        $certificateLevels = Certificate::getCertificateLevels();

        return view('certificates.create', compact(
            'companies',
            'users',
            'certificateTypes',
            'certificateLevels'
        ));
    }

    /**
     * Store a newly created certificate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'subject' => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:255|unique:certificates',
            'issue_date' => 'required|date',
            'expiration_date' => 'required|date|after:issue_date',
            'duration_months' => 'nullable|integer|min:1|max:120',
            'training_organization' => 'required|string|max:255',
            'training_organization_code' => 'nullable|string|max:100',
            'instructor_name' => 'nullable|string|max:255',
            'training_organization_address' => 'nullable|string|max:500',
            'certificate_type' => 'required|in:training,qualification,compliance,professional,academic',
            'level' => 'nullable|in:beginner,intermediate,advanced,professional,expert',
            'hours_completed' => 'nullable|numeric|min:0|max:9999.99',
            'credits' => 'nullable|numeric|min:0|max:999.99',
            'score' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:50',
            'regulatory_body' => 'nullable|string|max:255',
            'compliance_standard' => 'nullable|string|max:255',
            'renewal_required' => 'boolean',
            'renewal_period_months' => 'nullable|integer|min:1|max:120',
            'status' => 'required|in:active,expired,revoked,pending,suspended',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'transcript_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
            'language' => 'required|in:en,it',
        ]);

        // Handle file uploads
        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $filename = 'certificates/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $validated['certificate_file_path'] = $file->storeAs('certificates', $filename);
        }

        if ($request->hasFile('transcript_file')) {
            $file = $request->file('transcript_file');
            $filename = 'transcripts/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $validated['transcript_file_path'] = $file->storeAs('transcripts', $filename);
        }

        // Generate certificate number if not provided
        if (empty($validated['certificate_number'])) {
            $validated['certificate_number'] = 'CN-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }

        // Generate verification code
        $validated['verification_code'] = 'CERT-' . strtoupper(uniqid());

        // Calculate next renewal date
        if ($validated['renewal_required'] && !empty($validated['renewal_period_months'])) {
            $expirationDate = Carbon::parse($validated['expiration_date']);
            $validated['next_renewal_date'] = $expirationDate->subMonths($validated['renewal_period_months']);
        }

        $certificate = Certificate::create($validated);

        return redirect()->route('certificates.show', $certificate)
                        ->with('success', __('certificates.certificate_created'));
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('end_user') && $certificate->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        } elseif ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            if ($certificate->user_id !== $user->id && !in_array($certificate->company_id, $userCompanyIds)) {
                abort(403, 'Unauthorized');
            }
        }

        $certificate->load(['user', 'company']);

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Show the form for editing the specified certificate.
     */
    public function edit(Certificate $certificate)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('end_user') && $certificate->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        } elseif ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            if ($certificate->user_id !== $user->id && !in_array($certificate->company_id, $userCompanyIds)) {
                abort(403, 'Unauthorized');
            }
        }

        $companies = Company::active()->orderBy('name')->get();
        $users = User::active()->orderBy('name')->get();
        $certificateTypes = Certificate::getCertificateTypes();
        $certificateLevels = Certificate::getCertificateLevels();

        return view('certificates.edit', compact(
            'certificate',
            'companies',
            'users',
            'certificateTypes',
            'certificateLevels'
        ));
    }

    /**
     * Update the specified certificate in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('end_user') && $certificate->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        } elseif ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            if ($certificate->user_id !== $user->id && !in_array($certificate->company_id, $userCompanyIds)) {
                abort(403, 'Unauthorized');
            }
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'subject' => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:255|unique:certificates,certificate_number,' . $certificate->id,
            'issue_date' => 'required|date',
            'expiration_date' => 'required|date|after:issue_date',
            'duration_months' => 'nullable|integer|min:1|max:120',
            'training_organization' => 'required|string|max:255',
            'training_organization_code' => 'nullable|string|max:100',
            'instructor_name' => 'nullable|string|max:255',
            'training_organization_address' => 'nullable|string|max:500',
            'certificate_type' => 'required|in:training,qualification,compliance,professional,academic',
            'level' => 'nullable|in:beginner,intermediate,advanced,professional,expert',
            'hours_completed' => 'nullable|numeric|min:0|max:9999.99',
            'credits' => 'nullable|numeric|min:0|max:999.99',
            'score' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:50',
            'regulatory_body' => 'nullable|string|max:255',
            'compliance_standard' => 'nullable|string|max:255',
            'renewal_required' => 'boolean',
            'renewal_period_months' => 'nullable|integer|min:1|max:120',
            'status' => 'required|in:active,expired,revoked,pending,suspended',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'transcript_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
            'language' => 'required|in:en,it',
        ]);

        // Handle file uploads
        if ($request->hasFile('certificate_file')) {
            // Delete old file
            if ($certificate->certificate_file_path) {
                Storage::delete($certificate->certificate_file_path);
            }

            $file = $request->file('certificate_file');
            $filename = 'certificates/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $validated['certificate_file_path'] = $file->storeAs('certificates', $filename);
        }

        if ($request->hasFile('transcript_file')) {
            // Delete old file
            if ($certificate->transcript_file_path) {
                Storage::delete($certificate->transcript_file_path);
            }

            $file = $request->file('transcript_file');
            $filename = 'transcripts/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $validated['transcript_file_path'] = $file->storeAs('transcripts', $filename);
        }

        // Calculate next renewal date
        if ($validated['renewal_required'] && !empty($validated['renewal_period_months'])) {
            $expirationDate = Carbon::parse($validated['expiration_date']);
            $validated['next_renewal_date'] = $expirationDate->subMonths($validated['renewal_period_months']);
        }

        $certificate->update($validated);

        return redirect()->route('certificates.show', $certificate)
                        ->with('success', __('certificates.certificate_updated'));
    }

    /**
     * Remove the specified certificate from storage.
     */
    public function destroy(Certificate $certificate)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('end_user') && $certificate->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        } elseif ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            if ($certificate->user_id !== $user->id && !in_array($certificate->company_id, $userCompanyIds)) {
                abort(403, 'Unauthorized');
            }
        }

        // Delete associated files
        if ($certificate->certificate_file_path) {
            Storage::delete($certificate->certificate_file_path);
        }

        if ($certificate->transcript_file_path) {
            Storage::delete($certificate->transcript_file_path);
        }

        $certificate->delete();

        return redirect()->route('certificates.index')
                        ->with('success', __('certificates.certificate_deleted'));
    }

    /**
     * Download certificate file
     */
    public function download(Certificate $certificate, $type = 'certificate')
    {
        $user = Auth::user();

        // Authorization check
        if ($user->hasRole('end_user') && $certificate->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        } elseif ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            if ($certificate->user_id !== $user->id && !in_array($certificate->company_id, $userCompanyIds)) {
                abort(403, 'Unauthorized');
            }
        }

        $filePath = $type === 'transcript' ? $certificate->transcript_file_path : $certificate->certificate_file_path;

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::download($filePath);
    }

    /**
     * Verify certificate by verification code
     */
    public function verify($verificationCode)
    {
        $certificate = Certificate::where('verification_code', $verificationCode)
                                 ->where('status', 'active')
                                 ->first();

        if (!$certificate) {
            return view('certificates.verify', [
                'certificate' => null,
                'message' => __('certificates.verification_failed')
            ]);
        }

        $certificate->load(['user', 'company']);

        return view('certificates.verify', [
            'certificate' => $certificate,
            'message' => __('certificates.verification_successful')
        ]);
    }
}