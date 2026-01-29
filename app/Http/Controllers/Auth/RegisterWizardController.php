<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOrganizationData;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Registration Wizard (Multi-Step)
 * 🎯 Purpose: Guided multi-step registration process for better UX
 * 🛡️ Security: GDPR compliant with full consent management
 * 📊 Steps: 1. User Type → 2. Consents → 3. Data → 4. Summary
 * 
 * @package App\Http\Controllers\Auth
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2026-01-29
 */
class RegisterWizardController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected ConsentService $consentService;
    protected AuditLogService $auditService;

    /**
     * User types available for registration
     */
    protected array $userTypes = [
        'creator' => [
            'icon_svg_path' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
            'color' => 'oro-fiorentino',
        ],
        'patron' => [
            'icon_svg_path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
            'color' => 'verde-rinascita',
        ],
        'collector' => [
            'icon_svg_path' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'color' => 'blu-algoritmo',
        ],
        'company' => [
            'icon_svg_path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'color' => 'grigio-pietra',
        ],
        'trader_pro' => [
            'icon_svg_path' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
            'color' => 'amber-500',
            'disabled' => true, // 🚧 Coming soon - non ancora pronto
            'coming_soon' => true,
        ],
        'epp' => [
            'icon_svg_path' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c1.483 0 2.795-.298 3.996-.786M12 21c-1.483 0-2.795-.298-3.996-.786M3.786 15.004A9.004 9.004 0 0112 3c4.032 0 7.406 2.226 8.716 5.253M3.786 15.004A9.004 9.004 0 0012 21m-2.284-5.253A2.998 2.998 0 0012 15a2.998 2.998 0 002.284-1.253M12 12a2.998 2.998 0 01-2.284-1.253A2.998 2.998 0 0112 9a2.998 2.998 0 012.284 1.253A2.998 2.998 0 0112 12Z',
            'color' => 'teal-500',
        ],
    ];

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        ConsentService $consentService,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
    }

    /**
     * Step 1: Show user type selection
     */
    public function step1() {
        // Clear any previous wizard session data
        session()->forget('register_wizard');

        return view('auth.wizard.step1-user-type', [
            'userTypes' => $this->userTypes,
            'currentStep' => 1,
            'totalSteps' => 4,
        ]);
    }

    /**
     * Step 1: Store user type and proceed to Step 2
     */
    public function storeStep1(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:' . implode(',', array_keys($this->userTypes)),
        ], [
            'user_type.required' => __('register.js_errors.user_type_required'),
            'user_type.in' => 'Il tipo di account selezionato non è valido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        session()->put('register_wizard.user_type', $request->user_type);

        $this->logger->info('Registration wizard step 1 completed', [
            'user_type' => $request->user_type,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('register.wizard.step2');
    }

    /**
     * Step 2: Show consents (required pre-checked, optional to choose)
     */
    public function step2() {
        $userType = session('register_wizard.user_type');

        if (!$userType) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'La sessione è scaduta. Riprova dall\'inizio.');
        }

        return view('auth.wizard.step2-consents', [
            'userType' => $userType,
            'userTypeDetails' => $this->userTypes[$userType],
            'currentStep' => 2,
            'totalSteps' => 4,
        ]);
    }

    /**
     * Step 2: Store consents and proceed to Step 3
     */
    public function storeStep2(Request $request) {
        $userType = session('register_wizard.user_type');

        if (!$userType) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'La sessione è scaduta. Riprova dall\'inizio.');
        }

        // Required consents are always accepted (pre-checked)
        // Optional consents come from the form
        $consents = [
            'privacy_policy_accepted' => true,
            'terms_accepted' => true,
            'age_confirmation' => true,
            'analytics' => $request->boolean('consents.analytics'),
            'marketing' => $request->boolean('consents.marketing'),
            'profiling' => $request->boolean('consents.profiling'),
        ];

        session()->put('register_wizard.consents', $consents);

        $this->logger->info('Registration wizard step 2 completed', [
            'user_type' => $userType,
            'optional_consents' => array_filter([
                'analytics' => $consents['analytics'],
                'marketing' => $consents['marketing'],
                'profiling' => $consents['profiling'],
            ]),
        ]);

        return redirect()->route('register.wizard.step3');
    }

    /**
     * Step 3: Show personal data form
     */
    public function step3() {
        $userType = session('register_wizard.user_type');
        $consents = session('register_wizard.consents');

        if (!$userType || !$consents) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'La sessione è scaduta. Riprova dall\'inizio.');
        }

        return view('auth.wizard.step3-data', [
            'userType' => $userType,
            'userTypeDetails' => $this->userTypes[$userType],
            'wizardData' => [
                'user_type' => $userType,
                'consents' => $consents,
                'data' => session('register_wizard.data', []),
            ],
            'currentStep' => 3,
            'totalSteps' => 4,
        ]);
    }

    /**
     * Step 3: Store personal data and proceed to Step 4
     */
    public function storeStep3(Request $request) {
        $userType = session('register_wizard.user_type');
        $consents = session('register_wizard.consents');

        if (!$userType || !$consents) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'La sessione è scaduta. Riprova dall\'inizio.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nick_name' => 'nullable|string|max:255|unique:users,nick_name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'org_name' => 'nullable|string|max:255',
        ], [
            'name.required' => __('register.js_errors.name_required'),
            'email.required' => __('register.js_errors.email_required'),
            'email.email' => __('register.js_errors.email_invalid'),
            'email.unique' => 'Questa email è già registrata.',
            'nick_name.unique' => 'Questo nickname è già in uso.',
            'password.required' => __('register.js_errors.password_required'),
            'password.confirmed' => __('register.js_errors.password_mismatch'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        session()->put('register_wizard.data', [
            'name' => $request->name,
            'nick_name' => $request->nick_name,
            'email' => $request->email,
            'password' => $request->password, // Will be hashed on final submit
            'org_name' => $request->org_name,
        ]);

        $this->logger->info('Registration wizard step 3 completed', [
            'user_type' => $userType,
            'email' => $request->email,
        ]);

        return redirect()->route('register.wizard.step4');
    }

    /**
     * Step 4: Show summary for confirmation
     */
    public function step4() {
        $userType = session('register_wizard.user_type');
        $consents = session('register_wizard.consents');
        $data = session('register_wizard.data');

        if (!$userType || !$consents || !$data) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'La sessione è scaduta. Riprova dall\'inizio.');
        }

        return view('auth.wizard.step4-summary', [
            'userType' => $userType,
            'userTypeDetails' => $this->userTypes[$userType],
            'wizardData' => [
                'user_type' => $userType,
                'consents' => $consents,
                'data' => $data,
            ],
            'currentStep' => 4,
            'totalSteps' => 4,
        ]);
    }

    /**
     * Final: Create user account
     */
    public function complete(Request $request) {
        $userType = session('register_wizard.user_type');
        $consents = session('register_wizard.consents');
        $data = session('register_wizard.data');

        if (!$userType || !$consents || !$data) {
            return redirect()->route('register.wizard.step1')
                ->with('error', 'La sessione è scaduta. Riprova dall\'inizio.');
        }

        try {
            // Re-validate email uniqueness (in case someone registered in the meantime)
            if (User::where('email', $data['email'])->exists()) {
                return redirect()->route('register.wizard.step3')
                    ->with('error', 'Questa email è già registrata.')
                    ->withInput(['name' => $data['name'], 'nick_name' => $data['nick_name']]);
            }

            // Create the user
            $user = User::create([
                'name' => $data['name'],
                'nick_name' => $data['nick_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => $userType,
                'privacy_policy_accepted' => true,
                'terms_accepted' => true,
                'age_confirmation' => true,
            ]);

            // If company and org_name provided, create organization data
            if ($userType === 'company' && !empty($data['org_name'])) {
                UserOrganizationData::create([
                    'user_id' => $user->id,
                    'org_name' => $data['org_name'],
                ]);
            }

            // Store GDPR consents
            $this->consentService->storeConsentsFromRegistration($user, [
                'allow-personal-data-processing' => true,
                'allow-analytics-tracking' => $consents['analytics'],
                'allow-marketing-communications' => $consents['marketing'],
                'allow-profiling' => $consents['profiling'],
            ]);

            // Audit log
            $this->auditService->logUserAction(
                $user,
                'user_registered_wizard',
                [
                    'user_type' => $userType,
                    'registration_method' => 'wizard',
                    'ip_address' => $request->ip(),
                ],
                GdprActivityCategory::ACCOUNT_MANAGEMENT
            );

            $this->logger->info('User registered via wizard', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'email' => $user->email,
            ]);

            // Clear wizard session
            session()->forget('register_wizard');

            // Log the user in
            Auth::login($user);

            // Redirect to wallet setup or dashboard based on user type
            if (in_array($userType, ['creator', 'company', 'trader_pro'])) {
                return redirect()->route('register.wallet.setup')
                    ->with('success', 'Account creato! Ora configura il tuo wallet per iniziare.');
            }

            return redirect()->route('dashboard')
                ->with('success', __('register.registration_success'));
        } catch (\Exception $e) {
            $this->logger->error('Registration wizard failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? 'unknown',
            ]);

            return $this->errorManager->handle(
                'REGISTRATION_FAILED',
                ['email' => $data['email'] ?? ''],
                $e
            );
        }
    }
}
