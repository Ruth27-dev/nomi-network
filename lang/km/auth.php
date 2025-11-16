<?php

return [
    'sign-in' => [
        'title' => 'ចូលគណនី',
        'form' => [
            'username' => [
                'label'         => 'Email or Username',
                'placeholder'   => 'Enter Email or Username',
            ],
            'phone_username'    => [
                'label'         => 'ឈ្មោះអ្នកប្រើប្រាស់ ឬលេខទូរស័ព្ទ',
                'placeholder'   => 'បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់ ឬលេខទូរស័ព្ទ',
            ],
            'password' => [
                'label'         => 'ពាក្យសម្ងាត់',
                'placeholder'   => 'បញ្ចូលពាក្យសម្ងាត់',
            ],
            'remember'          => 'រក្សាទុកគណនីរបស់ខ្ញុំ',
            'forgot-password'   => 'ភ្លេចពាក្យសម្ងាត់?',
            'button'            => 'ចូលប្រើ',
            'error_login'       => 'គណនី ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវទេ',
            'company_disabled'  => 'ក្រុមហ៊ុនរបស់អ្នកត្រូវបានបិទ',
        ],
    ],
    'forgot-password' => [
        'title' => 'ភ្លេចពាក្យសម្ងាត់',
        'timer' => 'ផ្ញើរម្ដងទៀតក្នុង :seconds វិនាទី',
        'form' => [
            'email' => [
                'label'         => 'Email',
                'placeholder'   => 'Enter Email',
            ],
            'phone'     => [
                'label'         => 'លេខទូរស័ព្ទ',
                'placeholder'   => 'បញ្ចូលលេខទូរស័ព្ទ',
            ],
            'verification_code' => [
                'label'         => 'លេខកូដផ្ទៀងផ្ទាត់',
                'placeholder'   => 'បញ្ចូលលេខកូដផ្ទៀងផ្ទាត់',
            ],
            'password'          => [
                'label'         => 'ពាក្យសម្ងាត់',
                'placeholder'   => 'បញ្ចូលពាក្យសម្ងាត់',
            ],
            'confirm-password'  => [
                'label'         => 'បញ្ជាក់ពាក្យសម្ងាត់',
                'placeholder'   => 'បញ្ជាក់ពាក្យសម្ងាត់',
            ],
            'error_account'     => 'គណនីនេះត្រូវបានបិទហើយ',
            'has-account'       => 'មានគណនីរួចហើយ? ចូលគណនី',
            'sending'           => 'កំពុងផ្ញើរ...',
            'verifying'         => 'កំពុងផ្ទៀងផ្ទាត់...',
            'saving'            => 'កំពុងរក្សាទុក...',
            'button'            => 'ផ្ញើរទៅលេខទូរស័ព្ទ',
            'resend'            => 'ផ្ញើរម្ដងទៀត',
            'verify'            => 'ផ្ទៀងផ្ទាត់',
            'save-changes'      => 'រក្សាទុកការផ្លាស់ប្តូរ',
            'too-many-attempts' => 'ការព្យាយាមច្រើនពេក សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។',
            'error_verification_code'   => 'លេខកូដផ្ទៀងផ្ទាត់មិនត្រឹមត្រូវទេ',
            'success_verification_code' => 'លេខកូដផ្ទៀងផ្ទាត់ត្រូវបានត្រឹមត្រូវ',
            'expired_verification_code' => 'លេខកូដផ្ទៀងផ្ទាត់ផុតកំណត់',
            'invalid_phone'     => 'លេខទូរស័ព្ទមិនត្រឹមត្រូវ',
            'quota_exceeded'    => 'ការប្រើប្រាស់ច្រើនជាងកំណត់ សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។',
        ],
    ],
    'reset-password' => [
        'title' => 'Reset Password',
        'form' => [
            'new-password' => [
                'label' => 'New Password',
                'placeholder' => 'Enter New Password',
            ],
            'confirm-new-password' => [
                'label' => 'Confirm New Password',
                'placeholder' => 'Enter Confirm New Password',
            ],
            'has-account' => 'Has an account? Sign In',
            'button' => 'Reset Now',
        ],
    ]
];
