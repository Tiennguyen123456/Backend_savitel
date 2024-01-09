<?php
namespace App\Services\Api;

use App\Repositories\Role\RoleRepository;
use App\Services\BaseService;

class RoleService extends BaseService
{
    public function __construct()
    {
        $this->repo = new RoleRepository();
    }

    public function user()
    {
        return new UserService();
    }

    public function getList()
    {
        return $this->repo->getRoles(
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? null,
            $this->attributes['paginate'] ?? 50
        );
    }

    public function store()
    {
        $role = $this->store([
            'name'          => $this->attributes['name'],
            'guard_name'    => $this->attributes['guard_name'] ?? 'api'
        ]);

        return $role;
    }

    public function assign()
    {
        $userId = $this->attributes['user_id'];
        $roleIds = $this->attributes['role_ids'];
        $user = $this->user()->find($userId);

        if ($user) {
            $roles = $this->repo->getCollectionByIds($roleIds);
            $roleNames = $roles->pluck('name')->toArray();
            $user->syncRoles($roleNames);
            return true;
        }

        return false;
    }
}
