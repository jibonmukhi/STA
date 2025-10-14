<?php

return [
    // Page titles and headers
    'title' => 'Notifications',
    'page_title' => 'Notifications',
    'no_notifications' => 'No notifications',
    'no_new_notifications' => 'No new notifications',
    'view_all' => 'View all notifications',
    'mark_all_read' => 'Mark all as read',
    'mark_as_read' => 'Mark as read',
    'view_details' => 'View Details',

    // Filter options
    'filter_all' => 'All',
    'filter_unread' => 'Unread',
    'filter_read' => 'Read',

    // Messages
    'no_notifications_found' => 'No notifications found',
    'no_unread_notifications' => 'You have no unread notifications.',
    'no_read_notifications' => 'You have no read notifications.',
    'no_notifications_yet' => "You don't have any notifications yet.",
    'notification_marked_read' => 'Notification marked as read',
    'all_notifications_marked_read' => 'All notifications marked as read',
    'notification_deleted' => 'Notification deleted successfully',
    'notification_not_found' => 'Notification not found',
    'confirm_delete' => 'Are you sure you want to delete this notification?',

    // Notification types
    'types' => [
        'user_approval_request' => 'User Approval Request',
        'user_approved' => 'User Approved',
        'user_rejected' => 'User Registration Rejected',
        'company_invitation' => 'Company Invitation',
        'course_enrollment' => 'Course Enrollment',
        'certificate_issued' => 'Certificate Issued',
        'system_update' => 'System Update',
        'warning' => 'Warning',
        'success' => 'Success',
        'info' => 'Information',
    ],

    // User approval notifications
    'user_approval' => [
        'new_request' => 'New User Approval Request',
        'bulk_request' => 'New Bulk User Approval Request',
        'single_user_message' => 'New user :name submitted for approval by :company',
        'multiple_users_message' => ':count users submitted for approval by :company',
        'approved_title_single' => 'User Approved',
        'approved_title_multiple' => 'Users Approved',
        'approved_message_single' => 'User :name has been approved',
        'approved_message_multiple' => ':count users have been approved',
        'rejected_title_single' => 'User Registration Rejected',
        'rejected_title_multiple' => 'User Registrations Rejected',
        'rejected_message_single' => 'User registration for :name has been rejected',
        'rejected_message_multiple' => ':count user registrations have been rejected',
        'please_review' => 'Please review and approve or reject the user registration(s).',
        'review_pending' => 'Review Pending Approvals',
        'unknown_company' => 'Unknown Company',
    ],

    // Email notifications
    'mail' => [
        'greeting' => 'Hello :name,',
        'approval_request_subject' => 'New User Approval Request',
        'approval_request_single' => 'A new user has been submitted for approval by :company.',
        'approval_request_multiple' => ':count new users have been submitted for approval by :company.',
        'and_more' => '... and :count more user(s)',
        'thank_you' => 'Thank you for managing the STA system!',
        'thank_you_general' => 'Thank you for using the STA system!',

        'user_approved_subject' => 'User Approval Confirmation',
        'user_approved_single' => 'Good news! The user you submitted has been approved by :approver.',
        'user_approved_multiple' => 'Good news! :count users you submitted have been approved by :approver.',
        'account_active_single' => 'The user account is now active and can log in to the system.',
        'account_active_multiple' => 'All user accounts are now active and can log in to the system.',
        'view_company_users' => 'View Company Users',

        'user_rejected_subject' => 'User Registration Rejected',
        'user_rejected_single' => 'The user registration you submitted has been reviewed and rejected by :rejector.',
        'user_rejected_multiple' => ':count user registrations you submitted have been reviewed and rejected by :rejector.',
        'reason_for_rejection' => 'Reason for rejection: :reason',
        'contact_sta_manager' => 'If you believe this was an error or need further clarification, please contact the STA Manager.',
        'resubmit_info' => 'You may resubmit the user registration with corrected information if needed.',
        'submit_new_user' => 'Submit New User',
    ],

    // Generic notification messages
    'course_enrollment_title' => 'Course Enrollment Confirmation',
    'course_enrollment_message' => 'You have been enrolled in the course: :course',
    'certificate_issued_title' => 'Certificate Issued',
    'certificate_issued_message' => 'Your certificate for :certificate has been issued and is ready for download.',
    'company_invitation_title' => 'Company Invitation',
    'company_invitation_message' => 'You have been invited to join :company.',

    // Time formats
    'time' => [
        'just_now' => 'Just now',
        'minutes_ago' => ':count minute ago|:count minutes ago',
        'hours_ago' => ':count hour ago|:count hours ago',
        'days_ago' => ':count day ago|:count days ago',
        'weeks_ago' => ':count week ago|:count weeks ago',
        'months_ago' => ':count month ago|:count months ago',
        'years_ago' => ':count year ago|:count years ago',
    ],

    // Badge labels
    'new_badge' => 'New',
    'unread_count' => ':count unread',

    // Actions
    'actions' => [
        'mark_read' => 'Mark as Read',
        'mark_all_read' => 'Mark All as Read',
        'delete' => 'Delete',
        'view_details' => 'View Details',
        'view_all' => 'View All Notifications',
    ],
];
