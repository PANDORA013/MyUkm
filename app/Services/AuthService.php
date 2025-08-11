<?php

namespace App\Services;

use App\Models\User;
use App\Models\UKM;
use App\Models\UserPassword;
use App\Models\UserDeletion;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Service class for authentication operations
 * 
 * Handles user registration, login, and authentication-related business logic.
 */
class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        try {
            // Find UKM if ukm_code is provided
            $ukmId = null;
            if (!empty($data['ukm_code'])) {
                $ukm = UKM::where('code', $data['ukm_code'])->first();
                $ukmId = $ukm ? $ukm->id : null;
            }

            // Create user with consistent role naming
            $user = User::create([
                'name' => $data['name'],
                'nim' => $data['nim'],
                'password' => Hash::make($data['password']),
                'ukm_id' => $ukmId,
                'role' => 'anggota', // Consistent role naming
            ]);

            // Save encrypted password for admin reference
            $this->saveEncryptedPassword($user, $data['password']);

            // Auto-login the user
            Auth::login($user);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'ukm_id' => $ukmId
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Registration successful'
            ];

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'data' => Arr::except($data, ['password']), // Don't log password
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }

    /**
     * Authenticate user login
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        try {
            // Attempt authentication
            if (Auth::attempt($credentials)) {
                $user = User::where('nim', $credentials['nim'])->first();
                
                if ($user) {
                    // Update last login timestamp
                    $user->last_seen_at = now();
                    $user->save();
                }

                Log::info('User logged in successfully', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role
                ]);

                return [
                    'success' => true,
                    'user' => $user,
                    'message' => 'Login successful'
                ];
            } else {
                Log::warning('Login attempt failed', [
                    'nim' => $credentials['nim'] ?? 'unknown'
                ]);

                return [
                    'success' => false,
                    'message' => 'Invalid credentials. Please check your NIM and password.'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Login error', [
                'nim' => $credentials['nim'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Login failed. Please try again.'
            ];
        }
    }

    /**
     * Logout user
     *
     * @return array
     */
    public function logout(): array
    {
        try {
            $userId = Auth::id();
            
            Auth::logout();
            
            // Regenerate session for security
            session()->invalidate();
            session()->regenerateToken();

            Log::info('User logged out successfully', [
                'user_id' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'Logout successful'
            ];

        } catch (\Exception $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Logout failed'
            ];
        }
    }

    /**
     * Validate registration data
     *
     * @param array $data
     * @return array
     */
    public function validateRegistrationData(array $data): array
    {
        $errors = [];

        // Validate required fields
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }

        if (empty($data['nim'])) {
            $errors['nim'] = 'NIM is required';
        } else {
            // Check if NIM already exists
            if (User::where('nim', $data['nim'])->exists()) {
                $errors['nim'] = 'NIM already exists';
            }
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if ($data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'] = 'Password confirmation does not match';
        }

        // Validate UKM code if provided
        if (!empty($data['ukm_code'])) {
            if (!UKM::where('code', $data['ukm_code'])->exists()) {
                $errors['ukm_code'] = 'Invalid UKM code';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get redirect route based on user role
     *
     * @param User $user
     * @return string
     */
    public function getRedirectRoute(User $user): string
    {
        switch ($user->role) {
            case 'admin_website':
            case 'admin':
                return '/admin/dashboard';
            case 'admin_grup':
                return '/grup/dashboard';
            default:
                return route('home');
        }
    }

    /**
     * Save encrypted password for admin reference
     *
     * @param User $user
     * @param string $plainPassword
     * @return void
     */
    private function saveEncryptedPassword(User $user, string $plainPassword): void
    {
        try {
            UserPassword::updateOrCreate(
                ['user_id' => $user->id],
                ['password_enc' => Crypt::encryptString($plainPassword)]
            );
        } catch (\Exception $e) {
            // Don't fail registration if password encryption fails
            Log::warning('Failed to save encrypted password', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update user's last seen timestamp
     *
     * @param User $user
     * @return void
     */
    public function updateLastSeen(User $user): void
    {
        try {
            $user->update(['last_seen_at' => now()]);
        } catch (\Exception $e) {
            Log::warning('Failed to update last seen timestamp', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user has proper access based on role
     *
     * @param User $user
     * @return array
     */
    public function checkUserAccess(User $user): array
    {
        $issues = [];

        // Check admin_grup users have at least one managed group
        if ($user->role === 'admin_grup' && !$user->adminGroups()->exists()) {
            $issues[] = 'Admin grup user has no managed groups';
        }

        // Check for orphaned group memberships
        $groupCount = $user->groups()->count();
        if ($groupCount === 0 && $user->role !== 'admin_website') {
            $issues[] = 'User is not a member of any groups';
        }

        return [
            'has_issues' => !empty($issues),
            'issues' => $issues,
            'recommendations' => $this->getAccessRecommendations($user, $issues)
        ];
    }

    /**
     * Get access recommendations for user
     *
     * @param User $user
     * @param array $issues
     * @return array
     */
    private function getAccessRecommendations(User $user, array $issues): array
    {
        $recommendations = [];

        if (in_array('Admin grup user has no managed groups', $issues)) {
            if ($user->role === 'admin_grup') {
                $recommendations[] = 'Demote user to regular member role';
                $recommendations[] = 'Assign user as admin to at least one group';
            }
        }

        if (in_array('User is not a member of any groups', $issues)) {
            $recommendations[] = 'User should join at least one UKM/group';
        }

        return $recommendations;
    }

    /**
     * Check if user account has been deleted
     *
     * @param string $nim
     * @return bool
     */
    public function isUserDeleted(string $nim): bool
    {
        return UserDeletion::where('deleted_user_nim', $nim)->exists();
    }

    /**
     * Authenticate user login with deleted user check
     *
     * @param array $credentials
     * @return array
     */
    public function authenticateUser(array $credentials): array
    {
        try {
            // Check if user has been deleted
            if ($this->isUserDeleted($credentials['nim'])) {
                Log::warning('Login attempt for deleted user', [
                    'nim' => $credentials['nim']
                ]);

                return [
                    'success' => false,
                    'message' => 'Akun ini telah dihapus oleh admin dan tidak dapat digunakan lagi.',
                    'is_deleted' => true
                ];
            }

            // Proceed with regular authentication
            return $this->login($credentials);

        } catch (\Exception $e) {
            Log::error('Authentication error', [
                'nim' => $credentials['nim'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Authentication failed. Please try again.'
            ];
        }
    }
}
