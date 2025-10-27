<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Messages 2 - English
    |--------------------------------------------------------------------------
    | New error translations to avoid overly large files
    */

    'dev' => [
        // ProfileImage Controller Errors
        'profile_image_upload_validation_error' => 'Validation error during profile image upload for user :user_id.',
        'profile_image_upload_error' => 'Error during profile image upload for user :user_id.',
        'profile_set_current_image_error' => 'Error setting image as current for user :user_id.',
        'profile_image_delete_error' => 'Error deleting profile image for user :user_id.',
        'profile_banner_upload_error' => 'Error during banner upload for user :user_id.',
        'profile_set_current_banner_error' => 'Error setting current banner for user :user_id.',
        'profile_banner_delete_error' => 'Error deleting banner for user :user_id.',

        // PA Projects System Errors (FASE 4) - Dev
        'project_index_error' => 'Error loading projects list for user :user_id.',
        'project_create_page_error' => 'Error loading project creation form for user :user_id.',
        'project_create_failed' => 'Error creating project for user :user_id. Data: :request_data',
        'project_show_error' => 'Error loading project :project_id for user :user_id.',
        'project_edit_page_error' => 'Error loading project edit form :project_id for user :user_id.',
        'project_update_failed' => 'Error updating project :project_id for user :user_id.',
        'project_delete_failed' => 'Error deleting project :project_id for user :user_id.',
        'document_processing_failed' => 'Error processing document :document_id (project :project_id). Error: :error',
    ],

    'user' => [
        // ProfileImage Controller User Messages
        'profile_image_upload_validation_error' => 'Image data is invalid. Please check format and dimensions.',
        'profile_image_upload_error' => 'Unable to upload profile image. Please try again later.',
        'profile_set_current_image_error' => 'Unable to set image as current. Please try again.',
        'profile_image_delete_error' => 'Unable to delete profile image. Please try again later.',
        'profile_banner_upload_error' => 'Unable to upload banner image. Please try again later.',
        'profile_set_current_banner_error' => 'Unable to set current banner. Please try again.',
        'profile_banner_delete_error' => 'Unable to delete banner. Please try again later.',

        // PA Projects System User Messages (FASE 4)
        'project_index_error' => 'Unable to load projects list. Please try again later.',
        'project_create_page_error' => 'Unable to load creation form. Please try again.',
        'project_create_failed' => 'Unable to create project. Please check your data and try again.',
        'project_show_error' => 'Unable to load project. Please try again later.',
        'project_edit_page_error' => 'Unable to load edit form. Please try again.',
        'project_update_failed' => 'Unable to update project. Please check your data and try again.',
        'project_delete_failed' => 'Unable to delete project. Please try again later.',
        'document_processing_failed' => 'Unable to process document. Please check the format and try again.',
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'An error occurred. Please try again later or contact support.',
];
