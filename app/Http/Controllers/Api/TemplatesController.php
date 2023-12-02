<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\{
    Admin,
    Templates
};

use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

use Auth;

class TemplatesController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $templates;

    private $administrator = ['superadmin', 'admin'];
    private $managerial    = ['manager', 'supervisor'];

    public function __construct(
        Templates $templates
    ) {
        $this->templates = $templates;
        $this->limit = 15;
    }

    private function mapResult($items)
    {
        $list = [];
        foreach($items as $item) {
            $newItems = [];
            $newListItems = [];

            foreach ($item as $i) {
                $outletId = $i['outlet_id'];

                $newListItems[$outletId]['outlet'][] = $i['outlet'];
                $newListItems[$outletId]['items'][] = $i;

                $newItems = [
                    'items' => $newListItems
                ];
            }

            $list[] = [
                'manager'  => $item[0]->manager,
                'newItems' => $newItems
            ];
        }

        return $list;
    }

    private function mapDefault($items)
    {
        return $this->respondWithSuccess($items);

        $list = [];

        foreach($items as $item) {
            return $this->respondWithSuccess($item);
        }

        return $list;
    }

    public function Test()
    {
        // superadmin
        $currentUser = Admin::where('email', 'superadmin@gmail.com')->first();
        $userRole = $currentUser->getRoleNames()[0];

        $page = (int) 1;
        $query = $this->templates
            ->withCount('details')
            ->with(['manager']);

        // if (!in_array($userRole, $this->administrator)) {
        //     $searchQuery = (in_array($userRole, $this->managerial))
        //         ? 'manager_id' : 'supervisor_id';

        //     $query = $query->where($searchQuery, $currentUser->id);
        // }

        $total = $query->count();
        $query = $query
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        if (in_array($userRole, $this->administrator)) {
            $query = $query->groupBy('manager_id');
            $query = $this->mapResult($query);
        }

        $result = $this->generatePagination($query, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }

    private function nullResponse($page)
    {
        $query = [
            0 => [
                "manager"  => [],
                "items"    => [],
                "newItems" => []
            ]
        ];

        $result = $this->generatePagination($query, count($query), $this->limit, $page);

        return $this->respondWithSuccess($result);
    }

    public function Index(Request $request)
    {
        $role = $request->get('role');
        $guardType = $this->getGuardType($role);

        $currentUser = Auth::guard($guardType)->user();
        $currentUserRole = $currentUser->getRoleNames();

        $page = (int) $request->get('page', 1);

        if (!isset($currentUserRole[0])) {
            return $this->nullResponse($page);
        }

        $userRole = $currentUserRole[0];

        $query = $this->templates
            ->withCount('details')
            ->with(['supervisor', 'outlet']);

        if (!in_array($userRole, $this->administrator)) {
            $searchQuery = (in_array($userRole, $this->managerial))
                ? 'manager_id' : 'supervisor_id';

            $query = $query->where(
                $searchQuery,
                $userRole == "supervisor"
                    ? $currentUser->manager_id
                    : $currentUser->id
            );
        }

        $total = $query->count();
        $query = $query
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('manager_id');

        $result = $this->generatePagination(
            $this->mapResult($query), $total, $this->limit, $page
        );

        return $this->respondWithSuccess($result);
    }
}