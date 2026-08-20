<?php
namespace CouponWheel\Controllers\Admin;

use CouponWheel\Models\SpinLog;
use CouponWheel\Models\WheelProgram;
use CouponWheel\Modules\Admin\Logs\LogTable;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class LogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Cms::setData('module', 'coupon_wheel_log');
    }

    public function index(Request $request)
    {
        $programId = (int) $request->input('program_id', 0);

        $filterProgram = null;

        if ($programId > 0)
        {
            $filterProgram = WheelProgram::find($programId);
        }

        Cms::setData('table',         new LogTable());
        Cms::setData('program_id',    $programId);
        Cms::setData('filterProgram', $filterProgram);

        return Cms::view('coupon-wheel::admin/logs/index');
    }
}

