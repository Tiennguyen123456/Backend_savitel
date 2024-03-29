<?php

namespace App\Rules;

use App\Helpers\Helper;
use App\Models\BaseModel;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueCustomField implements Rule
{
    protected $id;

    protected $eventId;

    public function __construct($eventId, $id = null)
    {
        $this->id = $id;
        $this->eventId = $eventId;
    }

    public function passes($attribute, $value)
    {
        $query = DB::table('event_custom_fields')
            ->where('event_id', $this->eventId)
            ->where('name', $value);

        if (Helper::tableHasColumn('event_custom_fields', 'status')) {
            $query = $query->where('status', '!=', BaseModel::STATUS_DELETED);
        }

        if (!blank($this->id)) {
            $query = $query->where('id', '!=', $this->id);
        }

        return !$query->exists();
    }

    public function message()
    {
        return 'Another record with the same event ID and name already exists.';
    }
}

