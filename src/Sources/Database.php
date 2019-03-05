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
    protected $tableName;

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
        $this->tableName = $config['table_name'];
    }

    /**
     * Retrieve data from the filesystem
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return object
     */
    public function fetch(Template $template)
    {
        $staticPage = DB::table($this->tableName)->where('name', $template->getName())->first();
        if ($staticPage) {
            return [
                'title' => $staticPage->title,
                'created_at' => $staticPage->created_at,
                'updated_at' => $staticPage->updated_at,
                'attributes' => $this->getParsedAttributes($template, json_decode($staticPage->attributes, true)) ?? []
            ];
        }
        return;
    }

    /**
     * Save template in the filesystem
     *
     * @param \Whitecube\NovaPage\Pages\Template $template
     * @return bool
     */
    public function store(Template $template)
    {
        $staticPage = DB::table($this->tableName)->where('name', $template->getName())->first();
        if ($staticPage) {
            return DB::table($this->tableName)->update([
                'title' => $template->getTitle(),
                'attributes' => json_encode($template->getAttributes()),
                'updated_at' => Carbon::now(),
                'created_at' => $template->getDate('created_at')
            ]);
        }
        return DB::table($this->tableName)->insert([
            'name' => $template->getName(),
            'title' => $template->getTitle(),
            'type' => $template->getType(),
            'locale' => App::getLocale(),
            'attributes' => json_encode($template->getAttributes()),
            'updated_at' => Carbon::now(),
            'created_at' => $template->getDate('created_at')
        ]);
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
}