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
        'project_rag_search_failed' => 'Error during RAG search in project :project_id for user :user_id. Query: :query',
        'rag_context_retrieval_failed' => 'Error retrieving RAG context for user :user_id. Query length: :query_length',
        'rag_embedding_generation_failed' => 'Error generating embedding for query (length :query_length). OpenAI API failed.',

        // AI Credits Errors (Task 5) - Dev
        'ai_credits_insufficient' => 'Insufficient AI credits for user :user_id. Balance: :balance, required: :required. Feature: :source_type',
        'ai_credits_deduct_failed' => 'Error deducting :credits credits from user :user_id (balance: :balance). Feature: :source_type. Error: :error',
        'ai_credits_refund_failed' => 'Error refunding :credits credits to user :user_id. Reason: :reason. Error: :error',
        'ai_credits_calculation_failed' => 'Error calculating credits from tokens (input: :input_tokens, output: :output_tokens). Error: :error',
        'ai_credits_estimation_failed' => 'Error estimating cost for :total_acts acts (chunk size: :chunk_size). Error: :error',

        // N.A.T.A.N. Chat Errors - Dev
        'natan_ai_consent_required' => 'AI processing consent missing for user :user_id. Operation blocked.',
        'natan_message_processing_failed' => 'Error processing N.A.T.A.N. message for user :user_id. Persona: :persona_id, session: :session_id. Error: :error',
        'natan_query_processing_failed' => 'Error generating AI response for user :user_id. Query length: :query_length, history: :history_count messages. Error: :error',
        'natan_session_delete_failed' => 'Error deleting N.A.T.A.N. session :session_id for user :user_id. Error: :error',
        'natan_search_preview_failed' => 'Error generating search preview for user :user_id. Query: :query. Error: :error',
        'natan_analysis_failed' => 'Error during N.A.T.A.N. analysis for user :user_id. Query: :query, limit: :limit acts. Error: :error',
        'natan_suggestions_failed' => 'Error retrieving suggested questions for user :user_id. Returning fallback suggestions. Error: :error',
        'natan_history_access_failed' => 'Error accessing chat history for user :user_id. Error: :error',
        'natan_session_access_failed' => 'Error accessing session :session_id for user :user_id. Error: :error',
        'natan_chunking_progress_failed' => 'Error polling chunking progress for session :session_id (user :user_id). Error: :error',
        'natan_chunking_final_failed' => 'Error retrieving final chunked analysis for session :session_id (user :user_id). Error: :error',
        'anthropic_model_fallback' => 'Anthropic API key does not have access to configured model :configured_model. Using fallback model :active_model instead. Reason: :fallback_reason. API key: :api_key_prefix',
        'natan_api_call_failed' => 'N.A.T.A.N. API call failed for user :user_id. Rate limit: :is_rate_limit, limit: :current_limit, retry: :retry_attempt. Error: :error',
        'natan_chunking_max_retries' => 'N.A.T.A.N. chunking exhausted max retries for chunk :chunk_index (max: :max_retries). Error: :error',
        'natan_job_session_not_found' => 'N.A.T.A.N. background job could not find session :session_id in cache.',
        'natan_job_analysis_failed' => 'N.A.T.A.N. background chunked analysis failed for session :session_id. Error: :error',
        'natan_job_user_not_found' => 'N.A.T.A.N. background job could not find user for session :session_id (user_id: :user_id).',
        // GDPR Consent Processing
        'gdpr_consent_processing_failed' => 'GDPR consent processing failed for user :user_id. Operation: :operation. Error: :error',

        // Egili Purchase System Errors (Dev Messages)
        'egili_purchase_process_failed' => '[EGILI] Purchase process failed. User :user_id, Amount: :egili_amount Egili, Payment Method: :payment_method, Error: :error_message',
        'egili_purchase_confirmation_error' => '[EGILI] Purchase confirmation page error. User :user_id, Order Reference: :order_reference, Error: :error',
        'egili_consent_missing' => '[EGILI] Missing payment-processing consent. User :user_id, Egili Amount: :egili_amount, Consent Status: :consent_status',
        'mint_insufficient_stripe_capabilities' => '[STRIPE] Stripe account(s) missing required capabilities (transfers + card_payments). Accounts: :insufficient_accounts',
        'egili_amount_invalid' => '[EGILI] Invalid purchase amount. User :user_id, Requested: :egili_amount, Min: :min_amount, Max: :max_amount',

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
        'project_rag_search_failed' => 'Unable to search project documents. Please try again later.',
        'rag_context_retrieval_failed' => 'Unable to retrieve data for your query. Please try again or contact support.',
        'rag_embedding_generation_failed' => 'Search service temporarily unavailable. Please try again later.',

        // AI Credits User Messages (Task 5)
        'ai_credits_insufficient' => 'Insufficient AI credits. Please purchase more credits to continue.',
        'ai_credits_deduct_failed' => 'Unable to process AI credits payment. Please try again or contact support.',
        'ai_credits_refund_failed' => 'Unable to process refund. Please contact support.',
        'ai_credits_calculation_failed' => 'Unable to calculate cost. Operation cancelled for your safety.',
        'ai_credits_estimation_failed' => 'Unable to estimate cost. Please try again later.',

        // N.A.T.A.N. Chat User Messages
        'natan_ai_consent_required' => 'AI processing consent required. Please update your privacy settings.',
        'natan_message_processing_failed' => 'Unable to process your message. Please try again.',
        'natan_query_processing_failed' => 'Unable to generate AI response. Please try again or contact support.',
        'natan_session_delete_failed' => 'Unable to delete chat session. Please try again or contact support.',
        'natan_search_preview_failed' => 'Unable to preview search results. Please try again.',
        'natan_analysis_failed' => 'Unable to complete analysis. Please try again or contact support.',
        'natan_suggestions_failed' => 'Unable to load suggested questions. Default suggestions shown.',
        'natan_history_access_failed' => 'Unable to load chat history. Please try again later.',
        'natan_session_access_failed' => 'Unable to load session messages. Please try again later.',
        'natan_chunking_progress_failed' => 'Unable to check analysis progress. Please refresh the page.',
        'natan_chunking_final_failed' => 'Unable to retrieve analysis results. Please contact support.',
        'anthropic_model_fallback' => 'The AI system is using an alternative model due to availability issues. Service continues normally.',
        'natan_api_call_failed' => 'AI service temporarily unavailable. Please try again in a few moments.',
        'natan_chunking_max_retries' => 'Analysis processing failed after multiple attempts. Please try again later or contact support.',
        'natan_job_session_not_found' => 'Analysis session expired or not found. Please start a new analysis.',
        'natan_job_analysis_failed' => 'Background analysis failed. Your credits have been refunded. Please try again or contact support.',
        'natan_job_user_not_found' => 'User session invalid. Please log in again and retry.',
        // GDPR Consent Processing
        'gdpr_consent_processing_failed' => 'We could not process your privacy consents. Please try again later or contact support.',

        // Egili Purchase System Errors (User Messages)
        'egili_purchase_process_failed' => 'An error occurred while processing your Egili purchase. Please try again later or contact support.',
        'egili_purchase_confirmation_error' => 'Unable to display order details. Check your email or contact support.',
        'egili_consent_missing' => 'You must consent to payment processing to purchase Egili. Go to privacy settings and accept the required consents.',
        'egili_amount_invalid' => 'The requested Egili amount is not valid. Check the minimum and maximum purchase limits.',
        'mint_insufficient_stripe_capabilities' => 'Payment system configuration incomplete. Our team has been notified.',
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'An error occurred. Please try again later or contact support.',
];