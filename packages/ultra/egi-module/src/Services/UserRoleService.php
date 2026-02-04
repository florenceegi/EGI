<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Services;

use App\Enums\NotificationStatus;
use App\Helpers\FegiAuth;
use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Throwable;

/**
 * 📜 Oracode Service: UserRoleService
 *
 * Implementation of the UserRoleServiceInterface for managing user roles within the EGI module.
 * Handles role assignments, verification, and user retrieval with proper error handling.
 *
 * @package     Ultra\EgiModule\Services
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Provides functionality for managing user roles within the EGI module,
 *              with specific focus on the 'creator' role assignment and verification.
 *              Implements standardized error handling via UEM.
 *
 * @context     🧩 Used within the EGI module's workflows when creator permissions need
 *              to be assigned or verified. Operates with permissions to modify user roles.
 *
 * @state       💾 Stateless. Relies on injected UltraErrorManager and UltraLogManager.
 *
 * @feature     🗝️ Creates and assigns the 'creator' role to users
 * @feature     🗝️ Verifies if users have the 'creator' role
 * @feature     🗝️ Retrieves all users with the 'creator' role
 * @feature     🗝️ Implements standardized error handling with UEM
 *
 * @signal      🚦 Returns operation success/failure via boolean values
 * @signal      🚦 Returns collections of users for the getCreators method
 * @signal      🚦 Logs operations and errors via ULM
 *
 * @privacy     🛡️ `@privacy-internal`: Accesses user models and role assignments
 * @privacy     🛡️ `@privacy-data`: Logs minimal user identifiers
 * @privacy     🛡️ `@privacy-purpose`: Manages role-based access control
 * @privacy     🛡️ `@privacy-consideration`: Role changes affect user data access capabilities
 *
 * @dependency  🤝 Ultra\ErrorManager\Interfaces\ErrorManagerInterface
 * @dependency  🤝 Ultra\UltraLogManager\UltraLogManager
 * @dependency  🤝 App\Models\User
 * @dependency  🤝 Spatie\Permission\Models\Role
 *
 * @testing     🧪 Unit Test: Mock dependencies and verify role assignment logic
 * @testing     🧪 Unit Test: Verify error handling with UEM for various edge cases
 * @testing     🧪 Integration Test: Verify actual role changes in the database
 *
 * @rationale   💡 Centralizes role management operations for the EGI module with
 *              proper error handling and logging for audit trails.
 */
class UserRoleService implements UserRoleServiceInterface {
    /**
     * 🧱 @dependency UltraErrorManager instance.
     * Used for standardized error handling.
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

    /**
     * 🧱 @dependency UltraLogManager instance.
     * Used for standardized logging and auditing.
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * 🧱 @property Log channel name.
     * Defines the ULM log channel to use.
     * @var string
     */
    protected string $logChannel = 'florenceegi';

    /**
     * 🎯 Constructor: Injects required dependencies.
     *
     * @param ErrorManagerInterface $errorManager UEM for standardized error handling
     * @param UltraLogManager $logger ULM for standardized logging
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;

        // Define custom error codes if not already defined in config
        $this->defineRoleErrorCodes();
        $this->defineCollectionUserErrorCodes();
    }

    /**
     * {@inheritdoc}
     * 🎯 Assigns the creator role to a specific user.
     *
     * --- Logic ---
     * 1. Find the user by ID
     * 2. Verify the user exists
     * 3. Find or create the 'creator' role
     * 4. Check if the user already has the role
     * 5. Assign the role if needed
     * 6. Log the operation and handle any errors
     * --- End Logic ---
     *
     * @param int $userId The ID of the user to assign the creator role to
     * @return bool True if the assignment succeeded or was already in place, false otherwise
     *
     * @privacy-purpose User role assignment for EGI creator operations
     */
    public function assignCreatorRole(int $userId): bool {
        $context = [
            'user_id' => $userId,
            'role' => 'creator'
        ];

        try {
            $result = FegiAuth::assignRoleToUser($userId, 'creator');

            if ($result) {
                $this->logger->info('Creator role assigned to user', $context);
            } else {
                $this->logger->error('User not found or role assignment failed', $context);
                $this->errorManager->handle('ROLE_ASSIGNMENT_FAILED', $context, null, false);
            }

            return $result;
        } catch (Throwable $e) {
            $this->logger->error('Error during role assignment', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));
            $this->errorManager->handle('ROLE_ASSIGNMENT_FAILED', array_merge($context, [
                'error_message' => $e->getMessage()
            ]), $e, false);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     * 🔍 Checks if a user has the creator role.
     *
     * @param int $userId The ID of the user to check
     * @return bool True if the user has the creator role, false otherwise
     *
     * @privacy-purpose User role verification for access control
     */
    public function hasCreatorRole(int $userId): bool {
        // Create context for logging and error handling
        $context = [
            'user_id' => $userId,
            'role' => 'creator'
        ];

        try {
            // Find the user by ID
            $user = User::find($userId);

            // Verify the user exists
            if (!$user) {
                $this->logger->notice('User not found during role check', $context);
                return false;
            }

            // Check if the user has the role
            $hasRole = $user->hasRole('creator');

            // Log at appropriate level
            if ($hasRole) {
                $this->logger->debug('User has creator role', $context);
            } else {
                $this->logger->debug('User does not have creator role', $context);
            }

            return $hasRole;
        } catch (Throwable $e) {
            // Log the error
            $this->logger->error('Error checking user role', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle the error with UEM without throwing
            $this->errorManager->handle(
                'ROLE_CHECK_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw
            );

            return false;
        }
    }

    /**
     * {@inheritdoc}
     * 📋 Gets all users with the creator role.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of users with the creator role
     *
     * @privacy-purpose Access multiple users for administrative operations
     */
    public function getCreators() {
        $context = ['role' => 'creator'];

        try {
            // Get the role by name
            $creatorRole = Role::where('name', 'creator')->first();

            // If role doesn't exist, return empty collection
            if (!$creatorRole) {
                $this->logger->notice('Creator role does not exist', $context);
                return collect(); // Return empty collection
            }

            // Get all users with the role
            $creators = User::role('creator')->get();

            $this->logger->info('Retrieved creator users', array_merge($context, [
                'count' => $creators->count()
            ]));

            return $creators;
        } catch (Throwable $e) {
            // Log the error
            $this->logger->error('Error retrieving creators', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle the error with UEM without throwing
            $this->errorManager->handle(
                'ROLE_USERS_RETRIEVAL_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw
            );

            return collect(); // Return empty collection on error
        }
    }

    /**
     * 🧱 Define custom error codes specific to role operations.
     * Registers these codes with UEM for consistent error handling.
     *
     * @return void
     */
    protected function defineRoleErrorCodes(): void {
        // Define error for user not found during role operations
        $this->errorManager->defineError('ROLE_USER_NOT_FOUND', [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message' => 'User ID :user_id not found during role operation',
            'user_message' => 'The specified user could not be found.',
            'http_status_code' => 404,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for role assignment failures
        $this->errorManager->defineError('ROLE_ASSIGNMENT_FAILED', [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Failed to assign role :role to user :user_id: :error_message',
            'user_message' => 'There was a problem updating user permissions. Please try again or contact support.',
            'http_status_code' => 500,
            'msg_to' => 'sweet-alert',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for role check failures
        $this->errorManager->defineError('ROLE_CHECK_FAILED', [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message' => 'Error checking if user :user_id has role :role: :error_message',
            'user_message' => null, // No user message, internal error
            'http_status_code' => 500,
            'msg_to' => 'log-only',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for user retrieval failures
        $this->errorManager->defineError('ROLE_USERS_RETRIEVAL_FAILED', [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Error retrieving users with role :role: :error_message',
            'user_message' => 'Unable to load user list. Please try again later.',
            'http_status_code' => 500,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for role permissions retrieval failures
        $this->errorManager->defineError('ROLE_PERMISSIONS_RETRIEVAL_FAILED', [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message' => 'Error retrieving permissions for role :role_name: :error_message',
            'user_message' => null, // No user message, internal error with fallback
            'http_status_code' => 500,
            'msg_to' => 'log-only',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);
    }

    /**
     * 🎯 Enhanced UserRoleService Method: Collection User Record Creation
     *
     * Adds collection-user pivot record creation with proper role assignment
     * following Oracode 3.0 principles and UEM integration patterns.
     *
     * @oracode-dimension technical
     * @value-flow Creates association records enabling creator permissions in collections
     * @community-impact Enables proper access control for collection management
     * @transparency-level Full audit trail of user-collection associations
     * @sustainability-factor Maintains data integrity through proper relationship management
     * @narrative-coherence Aligns with FlorenceEGI creator empowerment vision
     */

    /**
     * 🎯 Creates a collection-user pivot record with proper role assignment
     *
     * Establishes the relationship between a user and collection in the pivot table
     * with appropriate role, permissions, and metadata for FlorenceEGI operations.
     *
     * @param int $userId The ID of the user to associate with the collection
     * @param int $collectionId The ID of the collection to associate the user with
     * @param string $role The role to assign ('creator', 'collaborator', 'viewer', etc.)
     * @param array $permissions Optional array of specific permissions for this association
     * @param array $metadata Optional metadata for the relationship
     * @return bool True if the record was created successfully, false otherwise
     *
     * @throws \InvalidArgumentException If invalid role or missing required data
     *
     * @oracode-dimension technical|governance
     * @value-flow Enables access control and permission management for collections
     * @community-impact Facilitates collaborative collection management
     * @transparency-level Full logging of permission assignments
     * @sustainability-factor Maintains referential integrity and audit trails
     * @narrative-coherence Supports FlorenceEGI collaborative creation model
     *
     * @privacy-purpose Collection access control and permission management
     * @privacy-data Creates association records with role and permission data
     * @privacy-consideration Role assignments affect user data access capabilities
     */
    public function createCollectionUserRecord(
        int $userId,
        int $collectionId,
        string $role = 'creator',
        array $permissions = [],
        array $metadata = []
    ): bool {
        // Create comprehensive context for logging and error handling
        $context = [
            'user_id' => $userId,
            'collection_id' => $collectionId,
            'role' => $role,
            'operation' => 'create_collection_user_record',
            'service' => static::class
        ];

        // Enhanced logging for operation start
        $this->logger->info('[UserRoleService] Starting collection-user record creation', $context);

        try {
            // Enhanced validation with specific error contexts
            $this->validateCollectionUserCreation($userId, $collectionId, $role, $context);

            // Set default permissions based on role if none provided
            if (empty($permissions)) {
                $permissions = $this->getPermissionsFromSpatieRole($role);
            }

            // Check for existing record to prevent duplicates
            $existingRecord = CollectionUser::where('user_id', $userId)
                ->where('collection_id', $collectionId)
                ->first();

            if ($existingRecord) {
                // Update existing record if found
                $updateResult = $this->updateExistingCollectionUserRecord(
                    $existingRecord,
                    $role,
                    $permissions,
                    $metadata,
                    $context
                );

                if ($updateResult) {
                    $this->logger->info('[UserRoleService] Updated existing collection-user record', [
                        ...$context,
                        'record_id' => $existingRecord->id,
                        'action' => 'updated_existing'
                    ]);
                }

                return $updateResult;
            }

            // Prepare enhanced record data
            $recordData = [
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'role' => $role,
                'is_owner' => $role === 'creator',
                'joined_at' => now(),
                'metadata' => array_merge([
                    'created_via' => 'user_role_service',
                    'permissions_assigned' => $permissions,
                    'creation_timestamp' => now()->toISOString(),
                    'created_by_service' => static::class
                ], $metadata),
                'status' => NotificationStatus::ACTIVE->value
            ];

            // Create the collection-user record
            $collectionUser = CollectionUser::create($recordData);

            if (!$collectionUser) {
                throw new \Exception("Failed to create CollectionUser record - database operation returned false");
            }

            // Enhanced success logging
            $successContext = array_merge($context, [
                'collection_user_id' => $collectionUser->id,
                'permissions_count' => count($permissions),
                'is_owner' => $recordData['is_owner'],
                'status' => 'created_successfully'
            ]);

            $this->logger->info('[UserRoleService] Collection-user record created successfully', $successContext);

            // Optional: Also ensure Spatie role assignment for consistency
            if ($role === 'creator') {
                $this->assignCreatorRole($userId);
            }

            return true;
        } catch (\Exception $e) {
            // Handle validation errors
            $validationContext = array_merge($context, [
                'validation_error' => $e->getMessage(),
                'error_type' => 'validation'
            ]);

            $this->logger->warning('[UserRoleService] Validation failed for collection-user record creation', $validationContext);

            $this->errorManager->handle(
                'COLLECTION_USER_VALIDATION_FAILED',
                $validationContext,
                $e,
                false // Don't throw
            );

            return false;
        } catch (\Throwable $e) {
            // Handle all other errors
            $errorContext = array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            $this->logger->error('[UserRoleService] Error creating collection-user record', $errorContext);

            $this->errorManager->handle(
                'COLLECTION_USER_CREATION_FAILED',
                $errorContext,
                $e,
                false // Don't throw
            );

            return false;
        }
    }

    /**
     * 🛡️ Enhanced validation for collection-user record creation
     *
     * @param int $userId The user ID to validate
     * @param int $collectionId The collection ID to validate
     * @param string $role The role to validate
     * @param array $context Logging context
     * @throws \InvalidArgumentException If validation fails
     *
     * @oracode-dimension technical
     * @value-flow Ensures data integrity for collection associations
     * @transparency-level Validation rules are explicitly defined and logged
     */
    protected function validateCollectionUserCreation(int $userId, int $collectionId, string $role, array $context): void {
        // Validate user exists
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception("User with ID {$userId} not found");
        }

        // Validate collection exists
        $collection = Collection::find($collectionId);
        if (!$collection) {
            throw new \Exception("Collection with ID {$collectionId} not found");
        }

        // Validate role is in allowed list
        $allowedRoles = $this->getAllowedCollectionRoles();
        if (!in_array($role, $allowedRoles)) {
            throw new \Exception("Role '{$role}' is not allowed. Allowed roles: " . implode(', ', $allowedRoles));
        }

        // Log successful validation
        $this->logger->debug('[UserRoleService] Collection-user creation validation passed', array_merge($context, [
            'user_exists' => true,
            'collection_exists' => true,
            'role_valid' => true,
            'allowed_roles' => $allowedRoles
        ]));
    }

    /**
     * 🎯 Get permissions from Spatie role
     *
     * @param string $roleName The role to get permissions for
     * @return array Array of permissions for the role from Spatie system
     *
     * @oracode-dimension governance
     * @value-flow Retrieves actual permission sets from Spatie role system
     * @transparency-level Uses actual role-permission relationships from database
     */
    public function getPermissionsFromSpatieRole(string $roleName): array {
        $context = [
            'role_name' => $roleName,
            'operation' => 'get_spatie_role_permissions',
            'service' => static::class
        ];

        try {
            // Find the role in Spatie system
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->logger->warning('[UserRoleService] Role not found in Spatie system, using fallback permissions', $context);

                // Fallback to basic view permission if role doesn't exist
                return ['view'];
            }

            // Get all permissions associated with this role
            $permissions = $role->permissions()->pluck('name')->toArray();

            $this->logger->debug('[UserRoleService] Retrieved permissions from Spatie role', array_merge($context, [
                'permissions_count' => count($permissions),
                'permissions' => $permissions
            ]));

            // Return permissions or fallback if empty
            return !empty($permissions) ? $permissions : ['view'];
        } catch (Throwable $e) {
            $this->logger->error('[UserRoleService] Error retrieving Spatie role permissions', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle error and return fallback
            $this->errorManager->handle(
                'ROLE_PERMISSIONS_RETRIEVAL_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw
            );

            return ['view']; // Safe fallback
        }
    }

    /**
     * 🔄 Update existing collection-user record
     *
     * @param CollectionUser $existingRecord The existing record to update
     * @param string $role The new role
     * @param array $permissions The new permissions
     * @param array $metadata The new metadata
     * @param array $context Logging context
     * @return bool True if update successful
     *
     * @oracode-dimension technical
     * @value-flow Updates association records maintaining audit trail
     * @transparency-level All updates are logged with before/after states
     */
    protected function updateExistingCollectionUserRecord(
        CollectionUser $existingRecord,
        string $role,
        array $permissions,
        array $metadata,
        array $context
    ): bool {
        try {
            // Prepare update data
            $updateData = [
                'role' => $role,
                'is_owner' => $role === 'creator',
                'metadata' => array_merge($existingRecord->metadata ?? [], [
                    'updated_via' => 'user_role_service',
                    'permissions_updated' => $permissions,
                    'last_update_timestamp' => now()->toISOString(),
                    'updated_by_service' => static::class,
                    'previous_role' => $existingRecord->role
                ], $metadata),
                'status' => 'active'
            ];

            // Log the update attempt
            $this->logger->debug('[UserRoleService] Updating existing collection-user record', array_merge($context, [
                'existing_record_id' => $existingRecord->id,
                'previous_role' => $existingRecord->role,
                'new_role' => $role,
                'previous_is_owner' => $existingRecord->is_owner,
                'new_is_owner' => $updateData['is_owner']
            ]));

            // Perform the update
            $updateResult = $existingRecord->update($updateData);

            if ($updateResult) {
                $this->logger->info('[UserRoleService] Collection-user record updated successfully', array_merge($context, [
                    'record_id' => $existingRecord->id,
                    'update_successful' => true
                ]));
            }

            return $updateResult;
        } catch (\Throwable $e) {
            $this->logger->error('[UserRoleService] Failed to update existing collection-user record', array_merge($context, [
                'record_id' => $existingRecord->id,
                'update_error' => $e->getMessage()
            ]));

            return false;
        }
    }

    /**
     * 🎯 Updates user's current collection ID with enhanced validation and error handling
     *
     * This method is responsible for safely updating the current_collection_id field
     * in the users table. It provides comprehensive validation, error handling, and
     * audit logging following Oracode OS1 principles.
     *
     * @param int $userId The ID of the user to update
     * @param int $collectionId The ID of the collection to set as current
     * @param array $logContext Optional context for enhanced logging
     * @return bool True if update successful, method will throw/block on errors
     *
     * @throws \Exception Via UEM error handling for critical failures
     *
     * @oracode-dimension technical|governance
     * @value-flow Maintains user-collection state consistency for FlorenceEGI operations
     * @community-impact Ensures users maintain proper collection context for uploads
     * @transparency-level Full audit trail of current collection changes
     * @sustainability-factor Maintains data integrity through proper validation
     * @narrative-coherence Supports FlorenceEGI user experience continuity
     *
     * @privacy-purpose Collection association management for user experience
     * @privacy-data Updates user table with collection reference only
     * @privacy-consideration Collection changes affect user's default context
     */
    public function updateUserCurrentCollection(int $userId, int $collectionId, array $logContext = []): bool {
        // Enhanced context for comprehensive logging
        $context = array_merge($logContext, [
            'user_id' => $userId,
            'collection_id' => $collectionId,
            'operation' => 'update_user_current_collection',
            'service' => static::class
        ]);

        $this->logger->info('[UserRoleService] Starting user current collection update', $context);

        try {
            // VALIDATION PHASE: Critical validations that must pass
            $this->validateUserCurrentCollectionUpdate($userId, $collectionId, $context);

            // BUSINESS LOGIC PHASE: Perform the actual update
            $user = User::find($userId);

            // Log the previous state for audit trail
            $previousCollectionId = $user->current_collection_id;
            $enhancedContext = array_merge($context, [
                'previous_collection_id' => $previousCollectionId,
                'new_collection_id' => $collectionId
            ]);

            // Perform atomic update
            $user->current_collection_id = $collectionId;
            $updateResult = $user->save();

            if (!$updateResult) {
                throw new \Exception("User model save() returned false for user {$userId}");
            }

            // SUCCESS LOGGING
            $this->logger->info('[UserRoleService] User current collection updated successfully', array_merge($enhancedContext, [
                'update_successful' => true,
                'database_save_result' => $updateResult
            ]));

            return true;
        } catch (\Throwable $e) {
            // CRITICAL ERROR HANDLING
            $errorContext = array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            $this->logger->error('[UserRoleService] Failed to update user current collection', $errorContext);

            // Use UEM for BLOCKING error handling
            // This will stop the entire flow and show appropriate user message
            $this->errorManager->handle(
                'USER_CURRENT_COLLECTION_UPDATE_FAILED',
                $errorContext,
                $e,
                true // Throw exception to block execution
            );

            // This return should never be reached due to UEM throwing
            return false;
        }
    }

    /**
     * 🛡️ Enhanced validation for user current collection update
     *
     * Validates that both user and collection exist and that the operation is valid.
     * Throws exceptions with specific error codes for different validation failures.
     *
     * @param int $userId The user ID to validate
     * @param int $collectionId The collection ID to validate
     * @param array $context Logging context
     * @throws \InvalidArgumentException If validation fails
     *
     * @oracode-dimension technical
     * @value-flow Ensures data integrity before database operations
     * @transparency-level All validation rules are explicitly defined and logged
     */
    protected function validateUserCurrentCollectionUpdate(int $userId, int $collectionId, array $context): void {
        // Validate user exists
        $user = User::find($userId);
        if (!$user) {
            $this->logger->warning('[UserRoleService] User not found during current collection update', $context);

            $this->errorManager->handle(
                'USER_CURRENT_COLLECTION_VALIDATION_FAILED',
                array_merge($context, [
                    'validation_error' => "User with ID {$userId} not found",
                    'validation_type' => 'user_existence'
                ]),
                new \Exception("User with ID {$userId} not found"),
                true // Throw to block execution
            );
        }

        // Validate collection exists
        $collection = Collection::find($collectionId);
        if (!$collection) {
            $this->logger->warning('[UserRoleService] Collection not found during current collection update', $context);

            $this->errorManager->handle(
                'USER_CURRENT_COLLECTION_VALIDATION_FAILED',
                array_merge($context, [
                    'validation_error' => "Collection with ID {$collectionId} not found",
                    'validation_type' => 'collection_existence'
                ]),
                new \Exception("Collection with ID {$collectionId} not found"),
                true // Throw to block execution
            );
        }

        // Validate ownership (optional business rule)
        if ($collection->creator_id !== $userId) {
            $this->logger->warning('[UserRoleService] Collection ownership mismatch during current collection update', array_merge($context, [
                'collection_creator_id' => $collection->creator_id,
                'user_id' => $userId
            ]));

            $this->errorManager->handle(
                'USER_CURRENT_COLLECTION_VALIDATION_FAILED',
                array_merge($context, [
                    'validation_error' => "User {$userId} cannot set collection {$collectionId} as current (not owner)",
                    'validation_type' => 'ownership_mismatch',
                    'collection_creator_id' => $collection->creator_id
                ]),
                new \Exception("User {$userId} cannot set collection {$collectionId} as current (not owner)"),
                true // Throw to block execution
            );
        }

        // Log successful validation
        $this->logger->debug('[UserRoleService] User current collection update validation passed', array_merge($context, [
            'user_exists' => true,
            'collection_exists' => true,
            'ownership_valid' => true,
            'validation_status' => 'passed'
        ]));
    }

    /**
     * 📋 Get allowed collection roles
     *
     * @return array Array of allowed role names for collections
     *
     * @oracode-dimension governance
     * @value-flow Defines valid roles for collection access control
     * @transparency-level Role definitions are explicitly listed
     */
    protected function getAllowedCollectionRoles(): array {
        return [
            'creator',
            'collaborator',
            'viewer',
            'admin'
        ];
    }

    /**
     * 🧱 Define additional error codes for collection-user operations
     *
     * @return void
     *
     * @oracode-dimension technical
     * @value-flow Enables standardized error handling for collection operations
     * @transparency-level Error definitions are explicit and documented
     */
    protected function defineCollectionUserErrorCodes(): void {
        // Define error for collection-user validation failures
        $this->errorManager->defineError('COLLECTION_USER_VALIDATION_FAILED', [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message' => 'Validation failed for collection-user record creation: :validation_error',
            'user_message' => 'Unable to create collection association due to invalid data.',
            'http_status_code' => 400,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for collection-user creation failures
        $this->errorManager->defineError('COLLECTION_USER_CREATION_FAILED', [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Failed to create collection-user record for user :user_id and collection :collection_id: :error_message',
            'user_message' => 'There was a problem setting up collection access. Please try again or contact support.',
            'http_status_code' => 500,
            'msg_to' => 'sweet-alert',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);
    }
}