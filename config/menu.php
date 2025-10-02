<?php

return [
    'sta_manager' => [
        [
            'title' => 'navigation.dashboard',
            'route' => 'sta.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'navigation.user_management',
            'icon' => 'fas fa-users',
            'permission' => 'view users',
            'submenu' => [
                [
                    'title' => 'navigation.all_users',
                    'route' => 'users.index',
                    'permission' => 'view users'
                ],
                [
                    'title' => 'navigation.add_user',
                    'route' => 'users.create',
                    'permission' => 'create users'
                ],
                [
                    'title' => 'navigation.pending_approvals',
                    'route' => 'users.pending.approvals',
                    'permission' => 'approve users'
                ]
            ]
        ],
        [
            'title' => 'navigation.company_management',
            'icon' => 'fas fa-building',
            'permission' => 'view companies',
            'submenu' => [
                [
                    'title' => 'navigation.all_companies',
                    'route' => 'companies.index',
                    'permission' => 'view companies'
                ],
                [
                    'title' => 'navigation.add_company',
                    'route' => 'companies.create',
                    'permission' => 'create companies'
                ]
            ]
        ],
        [
            'title' => 'navigation.role_management',
            'route' => 'roles.index',
            'icon' => 'fas fa-user-shield',
            'permission' => 'view roles'
        ],
        [
            'title' => 'navigation.system_reports',
            'route' => 'system.reports',
            'icon' => 'fas fa-chart-bar',
            'permission' => 'view system reports'
        ],
        [
            'title' => 'navigation.settings',
            'route' => 'settings.index',
            'icon' => 'fas fa-cog',
            'permission' => 'manage settings'
        ],
        [
            'title' => 'navigation.data_vault',
            'route' => 'data-vault.index',
            'icon' => 'fas fa-database',
            'permission' => 'manage settings'
        ],
        [
            'title' => 'navigation.certificate_management',
            'route' => 'certificates.index',
            'icon' => 'fas fa-certificate',
            'permission' => 'view personal reports',
            'submenu' => [
                [
                    'title' => 'navigation.all_certificates',
                    'route' => 'certificates.index',
                    'permission' => 'view personal reports'
                ],
                [
                    'title' => 'navigation.add_certificate',
                    'route' => 'certificates.create',
                    'permission' => 'view personal reports'
                ]
            ]
        ],
        [
            'title' => 'navigation.course_management',
            'icon' => 'fas fa-graduation-cap',
            'submenu' => [
                [
                    'title' => 'navigation.all_courses',
                    'route' => 'courses.index'
                ],
                [
                    'title' => 'navigation.add_course',
                    'route' => 'courses.create'
                ],
                [
                    'title' => 'navigation.course_planning',
                    'route' => 'courses.planning'
                ]
            ]
        ],
        [
            'title' => 'navigation.calendar',
            'route' => 'calendar',
            'icon' => 'fas fa-calendar',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'navigation.my_reports',
            'route' => 'reports',
            'icon' => 'fas fa-chart-pie',
            'permission' => 'view personal reports'
        ]
    ],

    'company_manager' => [
        [
            'title' => 'navigation.dashboard',
            'route' => 'company.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'navigation.certificate_management',
            'route' => 'certificates.index',
            'icon' => 'fas fa-certificate',
            'permission' => 'view personal reports',
            'submenu' => [
                [
                    'title' => 'navigation.all_certificates',
                    'route' => 'certificates.index',
                    'permission' => 'view personal reports'
                ],
                [
                    'title' => 'navigation.add_certificate',
                    'route' => 'certificates.create',
                    'permission' => 'view personal reports'
                ]
            ]
        ],
        [
            'title' => 'navigation.course_management',
            'icon' => 'fas fa-graduation-cap',
            'submenu' => [
                [
                    'title' => 'navigation.all_courses',
                    'route' => 'courses.index'
                ],
                [
                    'title' => 'navigation.course_planning',
                    'route' => 'courses.planning'
                ]
            ]
        ],
        [
            'title' => 'navigation.calendar',
            'route' => 'calendar',
            'icon' => 'fas fa-calendar',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'navigation.my_reports',
            'route' => 'reports',
            'icon' => 'fas fa-chart-pie',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'navigation.my_companies',
            'route' => 'my-companies.index',
            'icon' => 'fas fa-building',
            'permission' => 'view companies'
        ],
        [
            'title' => 'navigation.company_users',
            'icon' => 'fas fa-users',
            'permission' => 'manage company users',
            'submenu' => [
                [
                    'title' => 'navigation.view_users',
                    'route' => 'company-users.index',
                    'permission' => 'manage company users'
                ],
                [
                    'title' => 'navigation.add_user',
                    'route' => 'company-users.create',
                    'permission' => 'create users'
                ]
            ]
        ],
        [
            'title' => 'navigation.company_reports',
            'route' => 'my-companies.index',
            'icon' => 'fas fa-chart-line',
            'permission' => 'view company reports'
        ]
    ],

    'teacher' => [
        [
            'title' => 'navigation.dashboard',
            'route' => 'teacher.dashboard',
            'icon' => 'fas fa-home',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'navigation.my_courses',
            'route' => 'teacher.my-courses',
            'icon' => 'fas fa-book',
            'permission' => 'manage own courses'
        ],
        [
            'title' => 'navigation.schedule',
            'route' => 'teacher.schedule',
            'icon' => 'fas fa-calendar-alt',
            'permission' => 'create course schedules'
        ],
        [
            'title' => 'navigation.certificates',
            'route' => 'teacher.certificates',
            'icon' => 'fas fa-certificate',
            'permission' => 'issue certificates'
        ]
    ],

    'end_user' => [
        [
            'title' => 'navigation.dashboard',
            'route' => 'user.dashboard',
            'icon' => 'fas fa-home',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'navigation.certificate_management',
            'route' => 'certificates.index',
            'icon' => 'fas fa-certificate',
            'permission' => 'view personal reports',
            'submenu' => [
                [
                    'title' => 'navigation.my_certificates',
                    'route' => 'certificates.index',
                    'permission' => 'view personal reports'
                ]
            ]
        ],
        [
            'title' => 'navigation.course_management',
            'icon' => 'fas fa-graduation-cap',
            'submenu' => [
                [
                    'title' => 'navigation.all_courses',
                    'route' => 'courses.index'
                ],
                [
                    'title' => 'navigation.course_planning',
                    'route' => 'courses.planning'
                ]
            ]
        ],
        [
            'title' => 'navigation.calendar',
            'route' => 'calendar',
            'icon' => 'fas fa-calendar',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'navigation.my_companies',
            'route' => 'user.dashboard',
            'icon' => 'fas fa-building',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'navigation.my_reports',
            'route' => 'reports',
            'icon' => 'fas fa-chart-pie',
            'permission' => 'view personal reports'
        ]
    ]
];