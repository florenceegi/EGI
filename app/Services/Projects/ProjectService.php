<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * ProjectService
 *
 * Core service for PA Projects management
 *
 * @package App\Services\Projects
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Manage PA user projects with business logic
 */
class ProjectService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get all projects for user
     *
     * @param User $user PA user
     * @param bool $activeOnly Only active projects
     * @return Collection
     */
    public function getUserProjects(User $user, bool $activeOnly = true): Collection {
        $this->logger->info('[ProjectService] Getting user projects', [
            'user_id' => $user->id,
            'active_only' => $activeOnly,
        ]);

        $query = Project::forUser($user);

        if ($activeOnly) {
            $query->active();
        }

        return $query->with(['documents'])->get();
    }

    /**
     * Create new project
     *
     * @param User $user Project owner
     * @param array $data Project data
     * @return Project
     */
    public function createProject(User $user, array $data): Project {
        $this->logger->info('[ProjectService] Creating project', [
            'user_id' => $user->id,
            'name' => $data['name'] ?? 'unnamed',
        ]);

        try {
            // Check limits (20 projects per user)
            $existingCount = Project::forUser($user)->active()->count();

            if ($existingCount >= 20) {
                $this->logger->warning('[ProjectService] User reached project limit', [
                    'user_id' => $user->id,
                    'current_count' => $existingCount,
                ]);

                throw new \Exception('Maximum number of projects reached (20). Please delete unused projects first.');
            }

            $project = Project::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? 'folder_open',
                'color' => $data['color'] ?? '#1B365D',
                'settings' => $data['settings'] ?? null,
                'is_active' => true,
            ]);

            $this->logger->info('[ProjectService] Project created', [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            return $project;
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_CREATE_FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ], $e);

            throw $e;
        }
    }

    /**
     * Update project
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project {
        $this->logger->info('[ProjectService] Updating project', [
            'project_id' => $project->id,
        ]);

        try {
            $project->update([
                'name' => $data['name'] ?? $project->name,
                'description' => $data['description'] ?? $project->description,
                'icon' => $data['icon'] ?? $project->icon,
                'color' => $data['color'] ?? $project->color,
                'settings' => $data['settings'] ?? $project->settings,
            ]);

            $this->logger->info('[ProjectService] Project updated', [
                'project_id' => $project->id,
            ]);

            return $project->fresh();
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_UPDATE_FAILED', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ], $e);

            throw $e;
        }
    }

    /**
     * Delete project (soft delete - mark as inactive)
     *
     * @param Project $project
     * @return bool
     */
    public function deleteProject(Project $project): bool {
        $this->logger->info('[ProjectService] Deleting project', [
            'project_id' => $project->id,
        ]);

        try {
            // Mark as inactive instead of hard delete
            $project->update(['is_active' => false]);

            $this->logger->info('[ProjectService] Project deleted (soft)', [
                'project_id' => $project->id,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_DELETE_FAILED', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ], $e);

            throw $e;
        }
    }

    /**
     * Get project statistics
     *
     * @param Project $project
     * @return array
     */
    public function getProjectStatistics(Project $project): array {
        return [
            'total_documents' => $project->documents()->count(),
            'ready_documents' => $project->documents()->ready()->count(),
            'processing_documents' => $project->documents()->processing()->count(),
            'failed_documents' => $project->documents()->failed()->count(),
            'total_chunks' => $project->documents()->withCount('chunks')->get()->sum('chunks_count'),
            'total_chat_messages' => $project->chatMessages()->count(),
        ];
    }
}