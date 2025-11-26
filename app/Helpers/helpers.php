<?php

if (!function_exists('courseManagementRoute')) {
    /**
     * Generate the appropriate course management route based on user role
     *
     * @param string $action The action (index, create, store, show, edit, update, destroy)
     * @param mixed $courseManagement Optional course management model instance
     * @return string
     */
    function courseManagementRoute(string $action = 'index', $courseManagement = null): string
    {
        $user = auth()->user();

        if (!$user) {
            return $courseManagement
                ? route('course-management.' . $action, $courseManagement)
                : route('course-management.' . $action);
        }

        // Company managers use the company-prefixed routes
        if ($user->hasRole('company_manager')) {
            return $courseManagement
                ? route('company.course-management.' . $action, $courseManagement)
                : route('company.course-management.' . $action);
        }

        // STA managers and others use the standard routes
        return $courseManagement
            ? route('course-management.' . $action, $courseManagement)
            : route('course-management.' . $action);
    }
}

if (!function_exists('courseEnrollmentRoute')) {
    /**
     * Generate the appropriate course enrollment route based on user role
     *
     * @param string $action The action (index, create, store)
     * @param mixed $course Course model instance
     * @return string
     */
    function courseEnrollmentRoute(string $action, $course): string
    {
        $user = auth()->user();

        if (!$user) {
            return route('courses.enrollments.' . $action, $course);
        }

        // Company managers use the company-prefixed routes
        if ($user->hasRole('company_manager')) {
            return route('company.courses.enrollments.' . $action, $course);
        }

        // STA managers, teachers, and others use the standard routes
        return route('courses.enrollments.' . $action, $course);
    }
}

if (!function_exists('courseScheduleRoute')) {
    /**
     * Generate the appropriate course schedule route based on user role
     *
     * @param mixed $course Course model instance
     * @return string
     */
    function courseScheduleRoute($course): string
    {
        $user = auth()->user();

        if (!$user) {
            return route('courses.schedule', $course);
        }

        // Company managers use the company-prefixed routes
        if ($user->hasRole('company_manager')) {
            return route('company.courses.schedule', $course);
        }

        // STA managers, teachers, and others use the standard routes
        return route('courses.schedule', $course);
    }
}
