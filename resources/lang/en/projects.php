<?php

return [
    // Success messages
    'created_successfully' => 'Project ":name" created successfully',
    'updated_successfully' => 'Project updated successfully',
    'deleted_successfully' => 'Project deleted successfully',
    'document_uploaded_successfully' => 'Document ":filename" uploaded successfully',

    // Error messages
    'not_found' => 'Project not found',
    'unauthorized' => 'You are not authorized to access this project',
    'limit_reached' => 'You have reached the maximum limit of :limit projects. Please delete unused projects first.',
    'document_limit_reached' => 'You have reached the maximum limit of :limit documents for this project',
    'file_too_large' => 'File exceeds maximum size of :size MB',
    'invalid_file_type' => 'Invalid file type. Supported formats: :types',

    // Labels
    'name' => 'Project name',
    'description' => 'Description',
    'icon' => 'Icon',
    'color' => 'Color',
    'created_at' => 'Created at',
    'updated_at' => 'Updated at',
    'documents_count' => 'Documents',
    'ready_documents_count' => 'Ready documents',
    'processing_documents_count' => 'Processing documents',
    'failed_documents_count' => 'Failed documents',
    'total_chunks_count' => 'Total chunks',
    'chat_messages_count' => 'Chat messages',

    // Actions
    'create' => 'Create project',
    'create_new' => 'New Project',
    'create_first_project' => 'Create your first project',
    'edit' => 'Edit project',
    'delete' => 'Delete project',
    'upload_document' => 'Upload document',
    'view_documents' => 'View documents',
    'view_details' => 'View details',
    'view_chat' => 'View chat',
    'settings' => 'Settings',
    'filter_apply' => 'Apply filters',
    'filter_clear' => 'Clear filters',
    'clear_filters' => 'Remove filters',
    'close' => 'Close',
    'coming_soon' => 'Coming Soon',
    'modal_description' => 'Project management will be available soon. You will be able to upload documents, organize them into projects, and use them for priority chats with N.A.T.A.N.',

    // Page titles
    'page_title_index' => 'My Projects',
    'page_title_create' => 'New Project',
    'page_title_edit' => 'Edit Project',
    'page_title_show' => 'Project Details',
    'projects' => 'Projects',

    // Search and Filters
    'search_placeholder' => 'Search projects',
    'search_by_name_desc' => 'Search by name or description',
    'filter_status' => 'Status',
    'all_status' => 'All statuses',
    'status' => 'Status',
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'active_filters' => 'Active filters',
    'search' => 'Search',

    // Empty states
    'no_projects_title' => 'No projects yet',
    'no_projects_message' => 'Create your first project to start organizing documents and conversations.',
    'no_results_title' => 'No results found',
    'no_results_message' => 'There are no projects matching the selected filters. Try modifying your search criteria.',
    'no_description' => 'No description',

    // Stats and info
    'chats_count' => 'Chats',
    'limits_title' => 'Project limits',
    'limits_message' => 'You have :current projects out of :max maximum (:remaining available).',

    // Tabs
    'tab_documents' => 'Documents',
    'tab_chat' => 'Chat',
    'tab_settings' => 'Settings',
];
