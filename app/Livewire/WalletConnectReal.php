<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;

/**
 * @Oracode Livewire Component: Universal Real Wallet Connect Modal
 * 🎯 Purpose: Reusable component for connecting real Algorand wallets anywhere
 * 🛡️ Security: Validates wallet format, verifies on-chain existence
 * 🧱 Core Logic: Three-path flow - existing user / register / guest
 *
 * @package App\Livewire
 * @author Padmin D. Curtis for Fabio Cherici
 * @version 1.0.0 (Real Wallet Integration)
 * @date 2025-11-25
 *
 * @usage <livewire:wallet-connect-real />
 * @events
 *   - Listens: 'openWalletConnectModal' to open modal
 *   - Dispatches: 'walletConnected' when connection successful
 *   - Dispatches: 'walletDisconnected' when disconnected
 *
 * @signature [WalletConnectReal::v1.0] florence-egi-livewire-wallet
 */
class WalletConnectReal extends Component {
    // Modal state
    public bool $isOpen = false;
    public string $currentStep = 'input'; // input, options, loading, success, error

    // Wallet data
    public string $walletAddress = '';
    public string $verificationStatus = ''; // wallet_found_user_exists, wallet_found_no_user, wallet_not_on_chain, invalid_format
    public ?string $userName = null;
    public bool $isWeakAuth = false;

    // UI state
    public string $errorMessage = '';
    public string $successMessage = '';
    public bool $isLoading = false;

    // Pending action after connection
    public ?string $pendingAction = null;

    /**
     * Validation rules for wallet input
     */
    protected function rules(): array {
        return [
            'walletAddress' => [
                'required',
                'string',
                'size:58',
                'regex:/^[A-Z2-7]+$/i'
            ]
        ];
    }

    /**
     * Custom error messages
     */
    protected function messages(): array {
        return [
            'walletAddress.required' => __('collection.wallet.real_wallet_required'),
            'walletAddress.size' => __('collection.wallet.real_wallet_invalid_length'),
            'walletAddress.regex' => __('collection.wallet.real_wallet_invalid_chars'),
        ];
    }

    /**
     * Open the modal
     *
     * @param string|null $pendingAction Action to execute after connection
     */
    #[On('openWalletConnectModal')]
    public function openModal(?string $pendingAction = null): void {
        $this->reset(['walletAddress', 'errorMessage', 'successMessage', 'verificationStatus', 'userName']);
        $this->currentStep = 'input';
        $this->pendingAction = $pendingAction;
        $this->isOpen = true;
    }

    /**
     * Close the modal
     */
    public function closeModal(): void {
        $this->isOpen = false;
        $this->reset(['walletAddress', 'errorMessage', 'successMessage', 'verificationStatus', 'pendingAction']);
    }

    /**
     * Real-time validation as user types
     */
    public function updatedWalletAddress($value): void {
        // Auto-uppercase
        $this->walletAddress = strtoupper(trim($value));

        // Clear previous errors when user starts typing
        $this->errorMessage = '';

        // Live format validation
        if (strlen($this->walletAddress) === 58) {
            if (!preg_match('/^[A-Z2-7]+$/', $this->walletAddress)) {
                $this->errorMessage = __('collection.wallet.real_wallet_invalid_chars');
            }
        }
    }

    /**
     * Verify the wallet address
     */
    public function verifyWallet(): void {
        $this->validate();

        $this->isLoading = true;
        $this->errorMessage = '';
        $this->currentStep = 'loading';

        try {
            $response = Http::post(route('wallet.real.verify'), [
                'wallet_address' => $this->walletAddress,
                '_token' => csrf_token()
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $this->verificationStatus = $data['status'];
                $this->userName = $data['user_name'] ?? null;
                $this->isWeakAuth = $data['is_weak_auth'] ?? false;

                switch ($this->verificationStatus) {
                    case 'wallet_found_user_exists':
                        // User exists - show connect option
                        $this->currentStep = 'connect';
                        break;

                    case 'wallet_found_no_user':
                    case 'wallet_not_on_chain':
                        // New wallet - show options
                        $this->currentStep = 'options';
                        break;
                }
            } else {
                $this->errorMessage = $data['message'] ?? __('collection.wallet.real_wallet_verify_error');
                $this->currentStep = 'input';
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('collection.wallet.real_wallet_connection_error');
            $this->currentStep = 'input';
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Connect existing user
     */
    public function connectExistingUser(): void {
        $this->isLoading = true;

        try {
            $response = Http::post(route('wallet.real.connect'), [
                'wallet_address' => $this->walletAddress,
                '_token' => csrf_token()
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $this->successMessage = $data['message'];
                $this->currentStep = 'success';

                // Dispatch event for parent components
                $this->dispatch('walletConnected', [
                    'wallet_address' => $this->walletAddress,
                    'user_status' => $data['user_status'],
                    'user_name' => $data['user_name']
                ]);

                // Reload page after short delay to update session
                $this->dispatch('refreshPage');
            } else {
                $this->errorMessage = $data['message'] ?? __('collection.wallet.real_wallet_connect_error');
                $this->currentStep = 'connect';
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('collection.wallet.real_wallet_connection_error');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Continue as guest (create weak auth user)
     */
    public function continueAsGuest(): void {
        $this->isLoading = true;

        try {
            $response = Http::post(route('wallet.real.create-guest'), [
                'wallet_address' => $this->walletAddress,
                '_token' => csrf_token()
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $this->successMessage = $data['message'];
                $this->currentStep = 'success';

                $this->dispatch('walletConnected', [
                    'wallet_address' => $this->walletAddress,
                    'user_status' => 'weak_auth',
                    'user_name' => $data['user_name']
                ]);

                $this->dispatch('refreshPage');
            } else {
                $this->errorMessage = $data['message'] ?? __('collection.wallet.real_wallet_guest_error');
                $this->currentStep = 'options';
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('collection.wallet.real_wallet_connection_error');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Redirect to full registration
     */
    public function goToRegister(): void {
        $this->isLoading = true;

        try {
            $response = Http::post(route('wallet.real.prepare-register'), [
                'wallet_address' => $this->walletAddress,
                '_token' => csrf_token()
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $this->redirect($data['redirect']);
            } else {
                $this->errorMessage = __('collection.wallet.real_wallet_redirect_error');
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('collection.wallet.real_wallet_connection_error');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Go back to input step
     */
    public function goBackToInput(): void {
        $this->currentStep = 'input';
        $this->errorMessage = '';
    }

    /**
     * Render the component
     */
    public function render() {
        return view('livewire.wallet-connect-real');
    }
}
