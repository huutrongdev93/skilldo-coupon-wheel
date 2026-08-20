<?php
return [

    // -------------------------------------------------------------------------
    // General
    // -------------------------------------------------------------------------
    'title'       => 'Lucky Wheel',
    'btn.save'    => 'Save Program',
    'btn.back'    => 'Back',

    // -------------------------------------------------------------------------
    // Program list page
    // -------------------------------------------------------------------------
    'program.page_title'        => 'Wheel Programs',
    'program.tab.base'          => 'Basic',
    'program.tab.slices'        => 'Rewards',
    'program.tab.display'       => 'Display',
    'program.tab.text'          => 'Text',
    'program.add_title'         => 'Add New Program',
    'program.edit_title'        => 'Edit Program',

    // Table columns
    'program.col.name'          => 'Program Name',
    'program.col.time'          => 'Time',
    'program.col.status'        => 'Status',
    'program.col.action'        => 'Action',
    'program.status.running'    => 'Running',
    'program.status.paused'     => 'Paused',

    // Action buttons
    'program.btn.edit'          => 'Edit',
    'program.btn.clone'         => 'Clone',
    'program.btn.logs'          => 'View Spin Results',
    'program.btn.delete_confirm' => 'Delete program ":name"?',

    // -------------------------------------------------------------------------
    // Form - Basic tab
    // -------------------------------------------------------------------------
    'form.name'             => 'Program Name',
    'form.status'           => 'Status',
    'form.status.note'      => 'Only 1 program can run at a time.',
    'form.status.paused'    => 'Paused',
    'form.status.running'   => 'Running',
    'form.start_at'         => 'Start Date',
    'form.end_at'           => 'End Date',

    'form.require_login'       => 'Require Login',
    'form.require_login.no'    => 'No — Anyone can spin',
    'form.require_login.yes'   => 'Yes — Must be logged in',
    'form.spin_limit'          => 'Spin Limit',
    'form.spin_limit.forever'  => 'Once forever / user',
    'form.spin_limit.daily'    => 'Once per day',
    'form.spin_limit.none'     => 'Unlimited',
    'form.contact_limit'       => 'Duplicate check by',
    'form.contact_limit.note'  => 'Each email / phone can only spin once (applies to all spin limit modes)',
    'form.contact_limit.none'  => 'No restriction',
    'form.contact_limit.email' => 'Email',
    'form.contact_limit.phone' => 'Phone Number',
    'form.contact_limit.both'  => 'Email or Phone Number',
    'form.show_email'          => 'Show Email Field',
    'form.show_email.note'     => 'Uncheck to hide the email field from the form',
    'form.show_phone'          => 'Show Phone Number Field',
    'form.show_phone.note'     => 'Uncheck to hide the phone number field from the form',

    // Toggle status (table)
    'program.toggle.success'   => 'Status updated.',
    'program.toggle.error'     => 'Unable to update status.',

    // -------------------------------------------------------------------------
    // Form - Rewards (Slices) tab
    // -------------------------------------------------------------------------
    'slice.box_title'           => 'Wheel Slices',
    'slice.btn_add'             => 'Add Slice',
    'slice.title_prefix'        => 'Slice #',
    'slice.field.name'          => 'Prize Name *',
    'slice.field.name_placeholder' => 'E.g. 10% Off, Free Shipping, ...',
    'slice.field.difficulty'    => 'Win Difficulty',
    'slice.field.prize_type'    => 'Prize Type',
    'slice.prize_type.text'     => 'Custom Text',
    'slice.prize_type.product'  => 'Product',
    'slice.field.prize_text'    => 'Content / Coupon Code',
    'slice.field.prize_text_placeholder' => 'E.g. SAVE10, Free shipping nationwide...',
    'slice.field.product'       => 'Product',
    'slice.field.quantity'      => 'Win Quantity',
    'slice.field.bg_color'      => 'Background Color',
    'slice.field.text_color'    => 'Text Color',
    'slice.quantity.note'       => 'Setting quantity to 0 means unlimited wins',

    // Probability
    'slice.prob.title'          => 'Estimated Probability',
    'slice.prob.empty'          => 'Add wheel slices to see probabilities.',
    'slice.prob.warning'        => 'No slice can be won!',

    // Difficulty
    'difficulty.very_easy'  => '🟢 Very Easy',
    'difficulty.easy'       => '🔵 Easy',
    'difficulty.normal'     => '🟡 Normal',
    'difficulty.hard'       => '🟠 Hard',
    'difficulty.very_hard'  => '🔴 Very Hard',
    'difficulty.never'      => '⛔ Never',

    // -------------------------------------------------------------------------
    // Form - Display tab
    // -------------------------------------------------------------------------
    'display.bg'                => 'Background Color',
    'display.frame'             => 'Wheel Frame',
    'display.center'            => 'Wheel Center',
    'display.trigger_icon_opts' => 'Icon Options',

    'form.trigger_type'                => 'Trigger Type',
    'form.trigger_type.auto'           => 'Auto-show after X seconds',
    'form.trigger_type.button'         => 'Floating button',
    'form.trigger_type.at_least_one'   => 'At least one trigger method must be selected.',
    'form.trigger_delay'        => 'Show after (seconds)',
    'form.trigger_icon'         => 'Custom Icon',
    'form.trigger_icon.note'    => 'Upload a PNG image sized 240x240 to use a custom icon',
    'form.trigger_position'     => 'Display Position',
    'form.trigger_position.bottom_right' => 'Bottom - Right',
    'form.trigger_position.bottom_left'  => 'Bottom - Left',
    'form.trigger_position.top_right'    => 'Top - Right',
    'form.trigger_position.top_left'     => 'Top - Left',
    'form.trigger_effect'       => 'Animation Effect',
    'form.trigger_effect.none'  => 'No effect',
    'form.trigger_effect.1'     => 'Effect 1',
    'form.trigger_effect.2'     => 'Effect 2',
    'form.trigger_effect.3'     => 'Effect 3',
    'form.trigger_effect.4'     => 'Effect 4',
    'form.trigger_bg'           => 'Background Color',

    // -------------------------------------------------------------------------
    // Form - Text tab
    // -------------------------------------------------------------------------
    'text.group.base'           => 'Basic',
    'text.group.win'            => 'Win Notification',
    'text.group.lose'           => 'Lose Notification',
    'text.field.heading'        => 'Heading',
    'text.field.description'    => 'Description',

    // Default values
    'text.default.popupHeading'     => 'LUCKY WHEEL SPIN NOW AND WIN GREAT PRIZES',
    'text.default.popupDescription' => 'Don\'t miss the chance to win amazing offers from the lucky wheel. Are you feeling lucky today? Try it now!',
    'text.default.winHeading'       => 'CONGRATULATIONS! YOU WON A PRIZE',
    'text.default.winDescription'   => 'Your prize is',
    'text.default.loseHeading'      => 'Better luck next time!',
    'text.default.loseDescription'  => 'Thank you for participating. Try again in the next campaign!',

    // -------------------------------------------------------------------------
    // Log page
    // -------------------------------------------------------------------------
    'log.page_title'        => 'Spin Statistics',
    'log.col.program'       => 'Program',
    'log.col.prize'         => 'Prize',
    'log.col.code'          => 'Code / Product',
    'log.col.email'         => 'Email / Phone',
    'log.col.result'        => 'Result',
    'log.col.ip'            => 'IP',
    'log.col.time'          => 'Spin Time',
    'log.result.win'        => '🎁 Won',
    'log.result.lose'       => '😔 No Win',
    'log.product_id_prefix' => 'Product #',
    'log.filter.label'      => 'Viewing results for',
    'log.filter.clear'      => 'View All',

    // -------------------------------------------------------------------------
    // Ajax / Messages
    // -------------------------------------------------------------------------
    'ajax.save.success'         => 'Program saved successfully!',
    'ajax.save.error.name'      => 'Program name cannot be empty',
    'ajax.save.error.start_at'  => 'Start date cannot be empty',
    'ajax.save.error.end_at'    => 'End date cannot be empty',
    'ajax.save.error.slices'    => 'Please add at least 2 wheel slices.',
    'ajax.save.error.slice_name' => 'Slice #:num has no prize name.',
    'ajax.delete.success'       => 'Program deleted.',
    'ajax.delete.error.id'      => 'Invalid ID.',
    'ajax.clone.success'        => 'Cloned successfully!',
    'ajax.clone.error.id'       => 'Invalid ID.',
    'ajax.clone.error.not_found' => 'Program not found.',
    'ajax.clone.name_suffix'    => ' (Copy)',

];

