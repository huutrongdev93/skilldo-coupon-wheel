<?php
namespace CouponWheel\Controllers\Admin;

use CouponWheel\Enum\SliceDifficulty;
use CouponWheel\Models\WheelProgram;
use CouponWheel\Modules\Admin\Programs\ProgramTable;
use CouponWheel\Supports\WheelDisplay;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;
use SkillDo\Validate\Rule;

class ProgramController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Cms::setData('module', 'coupon_wheel_program');
    }

    public function index(Request $request)
    {
        Cms::setData('table', new ProgramTable());

        return Cms::view('coupon-wheel::admin/programs/index');
    }

    public function add(Request $request)
    {
        $viewData = $this->buildFormData(null);

        return Cms::view('coupon-wheel::admin/programs/save', data: $viewData);
    }

    public function edit(Request $request, $id)
    {
        /** @var WheelProgram $object */
        $object = WheelProgram::find($id);

        if (empty($object)) {
            return Admin::pageNotFound();
        }

        Cms::setData('object', $object);

        $viewData = $this->buildFormData($object);

        return Cms::view('coupon-wheel::admin/programs/save', data: $viewData);
    }


    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildFormData(?WheelProgram $program): array
    {
        $isEdit       = !empty($program);
        $settingsData = $isEdit ? ($program->settings ?: []) : [];

        // Normalize display sub-key
        $settingsData['display'] = WheelDisplay::normalize($settingsData['display'] ?? []);

        return [
            'object'             => $program,
            'slices_json'        => $isEdit ? json_encode($program->slices ?: [], JSON_UNESCAPED_UNICODE) : '[]',
            'difficulty_options' => SliceDifficulty::options(),
            'difficulty_weights' => SliceDifficulty::weights(),
            'formInfo'           => $this->buildFormInfo($program),
            'formCondition'      => $this->buildFormCondition($settingsData),
            'formDisplay'        => $this->buildFormDisplay($settingsData),
            'formTrigger'        => $this->buildFormTrigger($settingsData),
            'formDisplayText'    => $this->buildFormDisplayText($settingsData),
            'settingDisplay'     => $settingsData['display'],
        ];
    }

    private function buildFormInfo(?WheelProgram $program): \SkillDo\Cms\Form\Form
    {
        $isEdit = !empty($program);
        $form   = form();

        $form->text('name', [
            'label'       => trans('coupon-wheel::admin.form.name'),
            'start'       => 12,
            'validations' => Rule::make(trans('coupon-wheel::admin.form.name'))->notEmpty(),
        ], $isEdit ? $program->name : '');

        $form->select('status', [
            'label' => trans('coupon-wheel::admin.form.status'),
            'start' => 12,
            'note'  => trans('coupon-wheel::admin.form.status.note'),
        ], $isEdit ? $program->status : 0)
             ->options([
                 0 => trans('coupon-wheel::admin.form.status.paused'),
                 1 => trans('coupon-wheel::admin.form.status.running'),
             ]);

        $form->datetime('start_at', [
            'label'       => trans('coupon-wheel::admin.form.start_at'),
            'start'       => 6,
            'validations' => Rule::make(trans('coupon-wheel::admin.form.start_at'))->notEmpty(),
        ], $isEdit && $program->start_at ? date('d/m/Y H:i', strtotime($program->start_at)) : '');

        $form->datetime('end_at', [
            'label'       => trans('coupon-wheel::admin.form.end_at'),
            'start'       => 6,
            'validations' => Rule::make(trans('coupon-wheel::admin.form.end_at'))->notEmpty(),
        ], $isEdit && $program->end_at ? date('d/m/Y H:i', strtotime($program->end_at)) : '');

        return $form;
    }

    private function buildFormCondition(array $settings): \SkillDo\Cms\Form\Form
    {
        $form = form();

        $form->select('require_login', [
            'label' => trans('coupon-wheel::admin.form.require_login'),
            'start' => 12,
        ], $settings['require_login'] ?? 0)
             ->options([
                 0 => trans('coupon-wheel::admin.form.require_login.no'),
                 1 => trans('coupon-wheel::admin.form.require_login.yes'),
             ]);

        $form->select('spin_limit', [
            'label' => trans('coupon-wheel::admin.form.spin_limit'),
            'start' => 12,
        ], $settings['spin_limit'] ?? 'forever')
             ->options([
                 'forever' => trans('coupon-wheel::admin.form.spin_limit.forever'),
                 'daily'   => trans('coupon-wheel::admin.form.spin_limit.daily'),
                 'none'    => trans('coupon-wheel::admin.form.spin_limit.none'),
             ]);

        $form->select('contact_limit', [
            'label' => trans('coupon-wheel::admin.form.contact_limit'),
            'start' => 12,
            'note'  => trans('coupon-wheel::admin.form.contact_limit.note'),
        ], $settings['contact_limit'] ?? 'none')
             ->options([
                 'none'  => trans('coupon-wheel::admin.form.contact_limit.none'),
                 'email' => trans('coupon-wheel::admin.form.contact_limit.email'),
                 'phone' => trans('coupon-wheel::admin.form.contact_limit.phone'),
                 'both'  => trans('coupon-wheel::admin.form.contact_limit.both'),
             ]);

        return $form;
    }

    private function buildFormDisplay(array $settings): \SkillDo\Cms\Form\Form
    {
        $form = form();

        $form->switch('show_email', [
            'label' => trans('coupon-wheel::admin.form.show_email'),
            'start' => 6,
            'note'  => trans('coupon-wheel::admin.form.show_email.note'),
        ], (int)($settings['show_email'] ?? 1));

        $form->switch('show_phone', [
            'label' => trans('coupon-wheel::admin.form.show_phone'),
            'start' => 6,
            'note'  => trans('coupon-wheel::admin.form.show_phone.note'),
        ], (int)($settings['show_phone'] ?? 1));

        // trigger_type lưu dạng mảng: ['auto'], ['button'], ['auto','button']
        $triggerTypes = $settings['trigger_type'] ?? ['auto'];

        if (!is_array($triggerTypes))
        {
            // tương thích ngược với dữ liệu cũ dạng string
            $triggerTypes = [$triggerTypes];
        }

        $form->checkbox('trigger_auto', [
            'label' => trans('coupon-wheel::admin.form.trigger_type.auto'),
            'start' => 12,
        ], in_array('auto', $triggerTypes) ? 1 : 0);

        $form->checkbox('trigger_button', [
            'label' => trans('coupon-wheel::admin.form.trigger_type.button'),
            'start' => 12,
        ], in_array('button', $triggerTypes) ? 1 : 0);

        $form->number('trigger_delay', [
            'label' => trans('coupon-wheel::admin.form.trigger_delay'),
            'start' => 12,
            'min'   => 0,
        ], $settings['trigger_delay'] ?? 3);

        return $form;
    }

    private function buildFormTrigger(array $settings): \SkillDo\Cms\Form\Form
    {
        $display = $settings['display'] ?? WheelDisplay::defaults();

        $form    = form();

        $form->image('triggerIcon', [
            'label' => trans('coupon-wheel::admin.form.trigger_icon'),
            'note'  => trans('coupon-wheel::admin.form.trigger_icon.note'),
            'start' => 3,
        ], $display['triggerIcon'] ?? '');

        $form->select('triggerPosition', [
            'label' => trans('coupon-wheel::admin.form.trigger_position'),
            'start' => 2,
        ], $display['triggerPosition'] ?? 'bottom-right')->options([
            'bottom-right' => trans('coupon-wheel::admin.form.trigger_position.bottom_right'),
            'bottom-left'  => trans('coupon-wheel::admin.form.trigger_position.bottom_left'),
            'top-right'    => trans('coupon-wheel::admin.form.trigger_position.top_right'),
            'top-left'     => trans('coupon-wheel::admin.form.trigger_position.top_left'),
        ]);

        $form->select('triggerEffect', [
            'label' => trans('coupon-wheel::admin.form.trigger_effect'),
            'start' => 2,
        ], $display['triggerEffect'] ?? '')->options([
            ''                       => trans('coupon-wheel::admin.form.trigger_effect.none'),
            'trigger-animate-swing'  => trans('coupon-wheel::admin.form.trigger_effect.1'),
            'trigger-animate-zoomin' => trans('coupon-wheel::admin.form.trigger_effect.2'),
            'trigger-animate-wobble' => trans('coupon-wheel::admin.form.trigger_effect.3'),
            'trigger-animate-bounce' => trans('coupon-wheel::admin.form.trigger_effect.4'),
        ]);

        $form->color('triggerBg', [
            'label' => trans('coupon-wheel::admin.form.trigger_bg'),
            'start' => 2,
        ], $display['triggerBg'] ?? '#ffe2e2');

        return $form;
    }

    private function buildFormDisplayText(array $settings): array
    {
        $dataValues = array_merge([
            'popupHeading'     => trans('coupon-wheel::admin.text.default.popupHeading'),
            'popupDescription' => trans('coupon-wheel::admin.text.default.popupDescription'),
            'winHeading'       => trans('coupon-wheel::admin.text.default.winHeading'),
            'winDescription'   => trans('coupon-wheel::admin.text.default.winDescription'),
            'loseHeading'      => trans('coupon-wheel::admin.text.default.loseHeading'),
            'loseDescription'  => trans('coupon-wheel::admin.text.default.loseDescription'),
        ], $settings['displayText'] ?? []);

        // Nhóm Popup cơ bản
        $formBase = form();
        $formBase->text('popupHeading', ['label' => trans('coupon-wheel::admin.text.field.heading'), 'language' => true]);
        $formBase->text('popupDescription', ['label' => trans('coupon-wheel::admin.text.field.description'), 'language' => true]);
        $formBase->setValues($dataValues);

        // Nhóm thông báo trúng thưởng
        $formWin = form();
        $formWin->text('winHeading', ['label' => trans('coupon-wheel::admin.text.field.heading'), 'language' => true]);
        $formWin->text('winDescription', ['label' => trans('coupon-wheel::admin.text.field.description'), 'language' => true]);
        $formWin->setValues($dataValues);

        // Nhóm thông báo không trúng
        $formLose = form();
        $formLose->text('loseHeading', ['label' => trans('coupon-wheel::admin.text.field.heading'), 'language' => true]);
        $formLose->text('loseDescription', ['label' => trans('coupon-wheel::admin.text.field.description'), 'language' => true]);
        $formLose->setValues($dataValues);

        return [
            'base'      => $formBase->html(),
            'alertWin'  => $formWin->html(),
            'alertLose' => $formLose->html(),
        ];
    }
}
