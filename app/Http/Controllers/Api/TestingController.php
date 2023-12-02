<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\{
    Forms,
    Manager,
    Outlet,
    Staff,
    Daily
};

use App\Traits\HelpersTrait;

class TestingController extends BaseController
{
    use HelpersTrait;

    private $forms;
    private $manager;
    private $outlet;
    private $staff;
    private $daily;

    public function __construct(
        Manager $manager,
        Outlet $outlet,
        Forms $forms,
        Staff $staff,
        Daily $daily
    ) {
        $this->manager = $manager;
        $this->outlet = $outlet;
        $this->forms = $forms;
        $this->staff = $staff;
        $this->daily = $daily;
        $this->limit = 15;
    }

    public function StaffForms($managerId, $outletId)
    {
        $manager = $this->manager
            ->where('id', $managerId)
            ->first();
        
        $staff = $this->staff
            ->where('manager_id', $managerId)
            ->where('outlet_id', $outletId)
            ->where('is_supervisor', 0)
            ->get();
        
        $manager->staff = $staff;

        return $this->respondWithSuccess($manager);
    }

    public function CreateDailyFormReport(Request $request)
    {
        $today  = $request->get('date');
        $formId = $request->get("formId");
        $items  = $request->get("items");

        $items = json_decode($items, true);

        $form = $this->forms->where('id', $formId)->first();

        foreach ($items as $item) {
            $daily = $this->daily->updateOrCreate([
                'date'       => $today,
                'forms_id'   => $form->id,
                'items_id'   => $item['id'],
                'items_code' => $item['product_code'],
            ],[
                'value'      => $item['unit_value']
            ]);

            $form->daily()->syncWithoutDetaching($daily);
        }

        return $this->respondWithSuccess($form);
    }

    public function FetchFormByStaffId(Request $request, $managerId, $staffId)
    {
        $page = $request->get('page', 1);

        $query = $this->forms
            ->withCount(['items'])
            ->with(['items'])
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        if ($query) {
            $dataForm = [
                'id'            => $query->id,
                "template_id"   => $query->template_id,
                "manager_id"    => $query->manager_id,
                "outlet_id"     => $query->outlet_id,
                "supervisor_id" => $query->supervisor_id,
                "staff_id"      => $query->staff_id,
            ];

            $dataCount = $query->items_count ?? 0;
            $data = [
                "form" => $dataForm,
                "items" => $query->items ?? []
            ];

            $result = $this->generatePagination(
                $data, $dataCount, $this->limit, $page
            );
        } else {
            $result = [];
        }

        return $this->respondWithSuccess($result);
    }
}