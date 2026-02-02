<?php

namespace App\Http\Controllers\Notifications;

use App\Helpers\FegiAuth;
use App\Models\CustomDatabaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Supports\NotificationViewResolver;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class NotificationDetailsController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    public function show($id) {
        try {
            $this->logger->info('[NOTIFICATION_DETAILS] Show notification details started', [
                'notification_id' => $id,
                'user_id' => FegiAuth::id()
            ]);

            // Verifica che l'utente sia autenticato
            $user = FegiAuth::user();
            if (!$user) {
                $this->logger->warning('[NOTIFICATION_DETAILS] Unauthenticated access attempt', [
                    'notification_id' => $id
                ]);

                return $this->errorManager->handle('NOTIFICATION_UNAUTHORIZED_ACCESS', [
                    'notification_id' => $id,
                    'operation' => 'show_notification_details'
                ]);
            }

            $notification = $user
                ->customNotifications()
                ->where('id', $id)
                ->with('model')
                ->first();

            if (!$notification) {
                $this->logger->warning('[NOTIFICATION_DETAILS] Notification not found', [
                    'notification_id' => $id,
                    'user_id' => $user->id
                ]);

                return $this->errorManager->handle('NOTIFICATION_NOT_FOUND', [
                    'notification_id' => $id,
                    'user_id' => $user->id,
                    'operation' => 'show_notification_details'
                ]);
            }

            $this->logger->info('[NOTIFICATION_DETAILS] Notification found, processing view', [
                'notification_id' => $id,
                'notification_type' => $notification->type,
                'user_id' => $user->id
            ]);

            /**
             * Se la notifica ha una vista specificata, usala, altrimenti risolvi la vista in base al tipo di notifica
             * NotificationViewResolver::resolveView($notification->type); si basa sul FQCN della notifica,
             * standard OS 1.5 Self declaring code. Adottato inzialmente solo per le notifiche GDPR.
             */
            $viewKey = $notification->view; // ?? NotificationViewResolver::resolveView($notification->type);

            if (is_array($viewKey)) {
                $viewKey = array_map('strtolower', $viewKey); // Converte ogni elemento dell'array in minuscolo
            } elseif (is_string($viewKey)) {
                $viewKey = strtolower($viewKey); // Converte la stringa in minuscolo
            }

            if (str_contains($viewKey, '.')) {
                // Handle dot notation (e.g. 'commerce.egi_sold')
                $parts = explode('.', $viewKey);
                $section = $parts[0];
                $key = $parts[1];
                $config = config("notification-views.{$section}.{$key}", []);
            } else {
                 $config = $viewKey ? config('notification-views.' . $viewKey, []) : [];
            }
            
            $view = $config['view'] ?? null;

            $this->logger->info('[NOTIFICATION_DETAILS] View configuration resolved', [
                'notification_id' => $id,
                'view_key' => $viewKey,
                'view' => $view,
                'config' => $config,
                'user_id' => $user->id
            ]);

            $render = $config['render'] ?? 'controller';
            $controller = $config['controller'] ?? null;

            $this->logger->info('[NOTIFICATION_DETAILS] Render configuration prepared', [
                'notification_id' => $id,
                'view_key' => $viewKey,
                'view' => $view,
                'render' => $render,
                'controller' => $controller,
                'user_id' => $user->id
            ]);

            $this->logger->info('[NOTIFICATION_DETAILS] Final notification data prepared', [
                'notification_id' => $id,
                'notification_type' => $notification->type,
                'view' => $view,
                'render' => $render,
                'user_id' => $user->id
            ]);

            if ($view) {
                try {
                    if ($render === 'livewire') {
                        $this->logger->info('[NOTIFICATION_DETAILS] Rendering Livewire view', [
                            'notification_id' => $id,
                            'render' => $render,
                            'view' => $view,
                            'user_id' => $user->id
                        ]);
                        return view('livewire.' . $view, ['notification' => $notification]);
                    } elseif ($render === 'controller' && $controller) {
                        try {
                            $data = app()->make($controller)->prepare($notification);
                            $this->logger->info('[NOTIFICATION_DETAILS] Rendering controller view', [
                                'notification_id' => $id,
                                'controller' => $controller,
                                'view' => $view,
                                'data_keys' => array_keys($data ?? []),
                                'user_id' => $user->id
                            ]);
                            return view($view, $data);
                        } catch (\Exception $controllerException) {
                            $this->logger->error('[NOTIFICATION_DETAILS] Controller instantiation failed', [
                                'notification_id' => $id,
                                'controller' => $controller,
                                'error' => $controllerException->getMessage(),
                                'user_id' => $user->id
                            ]);

                            return $this->errorManager->handle('NOTIFICATION_CONTROLLER_ERROR', [
                                'notification_id' => $id,
                                'controller' => $controller,
                                'error' => $controllerException->getMessage(),
                                'user_id' => $user->id
                            ], $controllerException);
                        }
                    } else {
                        $this->logger->info('[NOTIFICATION_DETAILS] Rendering standard view', [
                            'notification_id' => $id,
                            'render' => $render,
                            'view' => $view,
                            'user_id' => $user->id
                        ]);
                        return view($view, ['notification' => $notification]);
                    }
                } catch (\Exception $viewException) {
                    $this->logger->error('[NOTIFICATION_DETAILS] View rendering failed', [
                        'notification_id' => $id,
                        'view' => $view,
                        'render' => $render,
                        'error' => $viewException->getMessage(),
                        'user_id' => $user->id
                    ]);

                    return $this->errorManager->handle('NOTIFICATION_VIEW_RENDER_ERROR', [
                        'notification_id' => $id,
                        'view' => $view,
                        'render' => $render,
                        'error' => $viewException->getMessage(),
                        'user_id' => $user->id
                    ], $viewException);
                }
            } else {
                $this->logger->error('[NOTIFICATION_DETAILS] Unsupported notification type', [
                    'notification_id' => $id,
                    'notification_type' => $notification->type,
                    'view_key' => $viewKey,
                    'user_id' => $user->id
                ]);

                return $this->errorManager->handle('NOTIFICATION_UNSUPPORTED_TYPE', [
                    'notification_id' => $id,
                    'notification_type' => $notification->type,
                    'view_key' => $viewKey,
                    'user_id' => $user->id
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error('[NOTIFICATION_DETAILS] Unexpected error', [
                'notification_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => FegiAuth::id()
            ]);

            return $this->errorManager->handle('NOTIFICATION_DETAILS_UNEXPECTED_ERROR', [
                'notification_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
                'operation' => 'show_notification_details'
            ], $e);
        }
    }
}
