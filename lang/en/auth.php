<?php

return [
    'sign-in' => [
        'title' => 'Sign In',
        'form' => [
            'username' => [
                'label'         => 'Email or Username',
                'placeholder'   => 'Enter Email or Username',
            ],
            'phone_username'    => [
                'label'         => 'Username Or Phone Number',
                'placeholder'   => 'Enter username or phone number',
            ],
            'password' => [
                'label'         => 'Password',
                'placeholder'   => 'Enter password',
            ],
            'remember'          => 'keep signed in',
            'forgot-password'   => 'Forgot Password?',
            'button'            => 'Sign In',
            'error_login'       => 'Account or password is incorrect.',
            'company_disabled'  => 'Your company is disabled',
        ],
    ],
    'forgot-password' => [
        'title' => 'Forgot Password',
        'timer' => 'Resend after :seconds seconds',
        'form' => [
            'email' => [
                'label'         => 'Email',
                'placeholder'   => 'Enter Email',
            ],
            'phone'     => [
                'label'         => 'Phone',
                'placeholder'   => 'Enter Phone',
            ],
            'verification_code' => [
                'label'         => 'Verification Code',
                'placeholder'   => 'Enter Verification Code',
            ],
            'password'          => [
                'label'         => 'Password',
                'placeholder'   => 'Enter Password',
            ],
            'confirm-password'  => [
                'label'         => 'Confirm Password',
                'placeholder'   => 'Enter Confirm Password',
            ],
            'error_account'     => 'This account is in disable status.',
            'has-account'       => 'Has an account? Sign In',
            'sending'           => 'Sending...',
            'verifying'         => 'Verifying...',
            'saving'            => 'Saving...',
            'button'            => 'Send to Phone',
            'resend'            => 'Resend Code',
            'verify'            => 'Verify',
            'save-changes'      => 'Save Changes',
            'too-many-attempts' => 'Too many attempts, please try again later.',
            'error_verification_code'   => 'Verification code is incorrect.',
            'success_verification_code' => 'Verification code is correct.',
            'expired_verification_code' => 'Verification code is expired.',
            'invalid_phone'     => 'Invalid phone number.',
            'quota_exceeded'    => 'Quota exceeded, please try again later.',
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
