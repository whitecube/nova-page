<?php

namespace Whitecube\NovaPage\Sources;

use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'name', 'title', 'attributes'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('novapage.sources.database.table_name');

        parent::__construct($attributes);
    }
}
