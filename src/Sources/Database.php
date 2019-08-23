<?php

namespace Whitecube\NovaPage\Sources;

use \App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Whitecube\NovaPage\Pages\Template;

class Database implements SourceInterface {

    /**
     * The table used to store static pages content
     *
     * @var string
     */
    protected $table;

    /**
     * The model used to store static pages content
     *
     * @var string
     */
    protected $model;

    /**
     * The fetched model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $original;

    /**
     * Retrieve the source's name
     *
     * @return string
     */
    public function getName()
    {
        return 'database';
    }

    /**
     * Set the source's configuration parameters
     *
     * @return string
     */
    public function setConfig(array $config)
    {
        $this->table = $config['table_name'];
        $this->model = $config['model'];
    }

    /**
     * Retrieve data from the database
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return object
     */
    public function fetch(Template $template)
    {
        $model = $this->getOriginal($template);

        if(!$model->id) {
            return;
        }

        $attributes = $this->getParsedAttributes(
            $template,
            $model->attributes ? json_decode($model->attributes, true) : []
        );

        return [
            'title' => $model->title,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'attributes' => $attributes
        ];
    }

    /**
     * Save template in the database
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return void
     */
    public function store(Template $template)
    {
        $original = $this->getOriginal($template);

        $original->fill([
            'name' => $template->getName(),
            'title' => $template->getTitle(),
            'type' => $template->getType(),
            'attributes' => json_encode($template->getAttributes()),
            'created_at' => $template->getDate('created_at'),
            'updated_at' => Carbon::now()
        ]);

        $original->save();
    }

    /**
     * Retrieve original StaticPage model
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return null|\Illuminate\Database\Eloquent\Model
     */
    public function getOriginal(Template $template)
    {
        if(!$this->original) {
            $instance = call_user_func($this->model . '::where', 'name', $template->getName())->first();

            $this->original = $instance ?? (new $this->model);
        }

        return $this->original;
    }

    /**
     * Retrieve and parse attributes array
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @param array $attributes
     * @return array
     */
    protected function getParsedAttributes(Template $template, $attributes)
    {
        foreach ($attributes as $key => $value) {
            if(!is_array($value) && !is_object($value)) continue;
            if($template->isJsonAttribute($key)) continue;
            $attributes[$key] = json_encode($value);
        }

        return $attributes;
    }

    /**
     * Get the text to display in the missing value exception
     *
     * @return string
     */
    public function getErrorLocation($type, $name)
    {
        return $this->getName() . ' table "' . $this->table . '". Page "' . $name . '".';
    }
}
