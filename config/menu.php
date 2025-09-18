<?php

return [
    'sta_manager' => [
        [
            'title' => 'Dashboard',
            'route' => 'sta.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'User Management',
            'icon' => 'fas fa-users',
            'permission' => 'view users',
            'submenu' => [
                [
                    'title' => 'All Users',
                    'route' => 'users.index',
                    'permission' => 'view users'
                ],
                [
                    'title' => 'Add User',
                    'route' => 'users.create',
                    'permission' => 'create users'
                ],
                [
                    'title' => 'Pending Approvals',
                    'route' => 'users.pending.approvals',
                    'permission' => 'approve users'
                ]
            ]
        ],
        [
            'title' => 'Company Management',
            'icon' => 'fas fa-building',
            'permission' => 'view companies',
            'submenu' => [
                [
                    'title' => 'All Companies',
                    'route' => 'companies.index',
                    'permission' => 'view companies'
                ],
                [
                    'title' => 'Add Company',
                    'route' => 'companies.create',
                    'permission' => 'create companies'
                ]
            ]
        ],
        [
            'title' => 'Role Management',
            'route' => 'roles.index',
            'icon' => 'fas fa-user-shield',
            'permission' => 'view roles'
        ],
        [
            'title' => 'System Reports',
            'route' => 'system.reports',
            'icon' => 'fas fa-chart-bar',
            'permission' => 'view system reports'
        ]
    ],

    'company_manager' => [
        [
            'title' => 'Dashboard',
            'route' => 'company.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'Certificate',
            'route' => 'certificate',
            'icon' => 'fas fa-certificate',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'Calendar',
            'route' => 'calendar',
            'icon' => 'fas fa-calendar',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'My Reports',
            'route' => 'reports',
            'icon' => 'fas fa-chart-pie',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'My Companies',
            'route' => 'my-companies.index',
            'icon' => 'fas fa-building',
            'permission' => 'view companies'
        ],
        [
            'title' => 'Company Users',
            'icon' => 'fas fa-users',
            'permission' => 'manage company users',
            'submenu' => [
                [
                    'title' => 'View Users',
                    'route' => 'company-users.index',
                    'permission' => 'manage company users'
                ],
                [
                    'title' => 'Add User',
                    'route' => 'company-users.create',
                    'permission' => 'create users'
                ]
            ]
        ],
        [
            'title' => 'Company Reports',
            'route' => 'my-companies.index',
            'icon' => 'fas fa-chart-line',
            'permission' => 'view company reports'
        ]
    ],

    'end_user' => [
        [
            'title' => 'Dashboard',
            'route' => 'user.dashboard',
            'icon' => 'fas fa-home',
            'permission' => 'view dashboard'
        ],
        [
            'title' => 'Certificate',
            'route' => 'certificate',
            'icon' => 'fas fa-certificate',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'Calendar',
            'route' => 'calendar',
            'icon' => 'fas fa-calendar',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'My Companies',
            'route' => 'user.dashboard',
            'icon' => 'fas fa-building',
            'permission' => 'view personal reports'
        ],
        [
            'title' => 'My Reports',
            'route' => 'reports',
            'icon' => 'fas fa-chart-pie',
            'permission' => 'view personal reports'
        ]
    ]
];