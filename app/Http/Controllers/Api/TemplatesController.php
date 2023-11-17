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
    private $managerial    = ['manager'];

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
            foreach ($item as $i) {
                $newItems[$i['supervisor_id']][] = $i;
            }

            $list[] = [
                'manager'  => $item[0]->manager,
                'items'    => $item,
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
    public function Index(Request $request)
    {
        $role = $request->get('role');
        $guardType = $this->getGuardType($role);

        $currentUser = Auth::guard($guardType)->user();
        $userRole = $currentUser->getRoleNames()[0];

        $page = (int) $request->get('page', 1);
        $query = $this->templates->withCount('details')->with(['supervisor']);

        if (!in_array($userRole, $this->administrator)) {
            $searchQuery = (in_array($userRole, $this->managerial))
                ? 'manager_id' : 'supervisor_id';

            $query = $query->where($searchQuery, $currentUser->id);
        }

        $total = $query->count();
        $query = $query->limit($this->limit)->offset($this->limit * ($page - 1))->get();

        if (in_array($userRole, $this->administrator)) {
            $query = $query->groupBy('manager_id');
            $query = $this->mapResult($query);
        } else {
            $query = $query->groupBy('supervisor_id');
            $query = $this->mapResult($query);
        }

        $result = $this->generatePagination($query, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }
}