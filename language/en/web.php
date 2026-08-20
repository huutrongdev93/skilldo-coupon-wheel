<?php
return [
    // -------------------------------------------------------------------------
    // Frontend Wheel UI
    // -------------------------------------------------------------------------
    'fab.title'             => 'Lucky Wheel',
    'wheel.aria'            => 'Spin wheel',
    'close.aria'            => 'Close',
    'spin.btn'              => 'SPIN',

    // Input form
    'form.fullname.label'       => 'Full Name',
    'form.fullname.placeholder' => 'John Doe',
    'form.email.label'          => 'Email',
    'form.email.required'       => '*',
    'form.email.placeholder'    => 'you@example.com',
    'form.phone.label'          => 'Phone Number',
    'form.phone.placeholder'    => '+1 234 567 890',
    'form.hint'                 => 'Your information will be kept private.',
    'form.hint.email_required'  => 'Email is required. Your information will be kept private.',
    'form.hint.phone_required'  => 'Phone number is required. Your information will be kept private.',
    'form.hint.both_required'   => 'Email and Phone number are required. Your information will be kept private.',

    // Client-side form validation
    'validate.email_required'   => 'Please enter your email to participate.',
    'validate.phone_required'   => 'Please enter your phone number to participate.',
    'validate.both_required'    => 'Please enter both your email and phone number to participate.',

    // Login
    'login.note'            => 'You need to :link to participate.',
    'login.link_text'       => 'log in',

    // Result
    'copy.btn'              => 'Copy Code',
    'view_product'          => 'View Product',
    'footer'                => 'By participating, you agree to the program\'s terms.',

    // -------------------------------------------------------------------------
    // Spin AJAX messages
    // -------------------------------------------------------------------------
    'spin.error.antibot'       => 'An error occurred, please try again.',
    'spin.error.no_program'    => 'There is no active lucky wheel program at this time.',
    'spin.error.require_login' => 'Please log in to participate in the spin.',
    'spin.error.already_spun'  => 'You have already participated. Please come back later!',
    'spin.error.email_used'    => 'This email has already been used to spin!',
    'spin.error.phone_used'    => 'This phone number has already been used to spin!',
    'spin.error.no_slices'     => 'This program has no reward slices yet.',
    'spin.error.exhausted'     => '🎁 The program has ended — all prizes have been given out. See you in the next campaign!',
    'spin.error.no_prize'      => 'No prizes are currently available.',
    'spin.win.message'         => 'Congratulations!',
    'spin.lose.message'        => 'Better luck next time!',
];
