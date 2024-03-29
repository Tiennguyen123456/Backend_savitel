<?php
namespace App\Repositories\Client;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class ClientRepository extends Repository implements ClientRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Client::class;
    }

    public function getClientsByEventId($eventId,
                                        $searches = [],
                                        $filters = [],
                                        $orderByColumn = 'updated_at',
                                        $orderByDesc = true,
                                        $limit = 0,
                                        $paginate = 50,
                                        $page)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED)
                            ->where('event_id', '=', $eventId);

        $query = $this->addSearchQuery($query, $searches);
        $query = $this->addFilterQuery($query, $filters);

        if (count($filters)) {
            if (isset($filters['status'])) {
                if (is_array($filters['status'])) {
                    $query = $query->whereIn('status', $filters['status']);
                } else {
                    $query = $query->where([
                        'status' => $filters['status']
                    ]);
                }
            }

            if (isset($filters['type'])) {
                if (is_array($filters['type'])) {
                    $query = $query->whereIn('type', $filters['type']);
                } else {
                    $query = $query->where([
                        'type' => $filters['type']
                    ]);
                }
            }
        }

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        if ($paginate > 0) {
            return $query->forPage($page, $paginate)->get();
        }

        return $query->get();
    }

    public function getClientByEventIdQrcode($eventId, $qrcode, $status = null)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED)
                            ->where('event_id', $eventId);

        $query = $this->getClientByQrcode($qrcode, $status, true, $query);
        return $query->first();
    }

    public function getClientByQrcode($qrcode, $status = null, $buildQuery = false, $query = null)
    {
        if ($buildQuery) {
            $query = $query->where('qrcode', $qrcode);
        } else {
            $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED)
                                ->where('qrcode', '=', $qrcode);
        }

        if (!empty($status)) {
            if (is_array($status)) {
                $query = $query->whereIn('status', $status);
            } else {
                $query = $query->where(['status' => $status]);
            }
        }

        if ($buildQuery) {
            return $query;
        }

        return $query->first();
    }

    public function getSummary($searches = [], $filters = [])
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED);

        if (!blank($filters)) {
            $query = $this->addFilterQuery($query, $filters);
        }

        if (!blank($searches)) {
            $query = $this->addSearchQuery($query, $searches);
        }

        $query = $query->groupBy('group');

        $query = $query->select('group', DB::raw('count(*) as total'));

        return $query->get();
    }
}
